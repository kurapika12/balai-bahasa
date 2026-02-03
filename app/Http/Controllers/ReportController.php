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

        $path = $request->file('file')->store('reports', 'public');

        Report::create([
            'user_id' => Auth::id(),
            'activity_id' => $request->activity_id,
            'title' => $request->title,
            'type' => $request->type,
            'description' => $request->description,
            'file_path' => $path
        ]);

        return back()->with('success', 'Laporan berhasil diunggah!');
    }

    public function destroy($id) {
        if(Auth::user()->role !== 'admin') abort(403);

        $report = Report::findOrFail($id);
        
        if (Storage::disk('public')->exists($report->file_path)) {
            Storage::disk('public')->delete($report->file_path);
        }
        
        $report->delete();

        return back()->with('success', 'Laporan dihapus.');
    }

    public function download($id) {
        $report = Report::findOrFail($id);

        // Cek keberadaan file fisik
        if (!Storage::disk('public')->exists($report->file_path)) {
            return back()->with('error', 'File fisik tidak ditemukan di server.');
        }

        // AMBIL EKSTENSI ASLI (misal: .pdf, .docx)
        $extension = pathinfo($report->file_path, PATHINFO_EXTENSION);

        // UBAH NAMA FILE JADI SESUAI JUDUL (misal: Laporan Keuangan -> laporan-keuangan.pdf)
        $newFilename = Str::slug($report->title) . '.' . $extension;

        // Download dengan nama baru
        return Storage::disk('public')->download($report->file_path, $newFilename);
    }
}