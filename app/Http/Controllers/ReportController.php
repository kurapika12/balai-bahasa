<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // Tambahkan ini untuk mengubah judul jadi nama file

class ReportController extends Controller
{
    public function store(Request $request) {
        $request->validate([
            'activity_id' => 'required',
            'title' => 'required',
            'file' => 'required|mimes:pdf,doc,docx,xls,xlsx,jpg,png|max:10240',
            'type' => 'required'
        ]);

        $file = $request->file('file');

        // 🔹 Ambil nama asli file
        $originalName = $file->getClientOriginalName();

        // 🔹 File tetap disimpan dengan nama acak (default Laravel)
        $path = $file->store('reports', 'public');

        Report::create([
            'user_id' => Auth::id(),
            'activity_id' => $request->activity_id,
            'title' => $request->title,
            'type' => $request->type,
            'description' => $request->description,
            'file_path' => $path,
            'original_filename' => $originalName // kolom baru
        ]);

        return back()->with('success', 'Laporan berhasil diunggah!');
    }

    public function destroy($id) {
        // Hanya admin yang bisa hapus
        if(Auth::user()->role !== 'admin') abort(403);

        $report = Report::findOrFail($id);
        Storage::disk('public')->delete($report->file_path); // Hapus file fisik
        $report->delete(); // Hapus data di DB

        return back()->with('success', 'Laporan dihapus.');
    }

    public function download($id)
    {
        $report = Report::findOrFail($id);

        return Storage::disk('public')->download(
            $report->file_path,
            $report->original_filename // ← ini kuncinya
        );
    }
}
