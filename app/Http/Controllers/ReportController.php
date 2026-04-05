<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ZipArchive;

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
        $originalName = $file->getClientOriginalName(); 
        $path = $file->store('reports', 'public');

        Report::create([
            'user_id' => Auth::id(),
            'activity_id' => $request->activity_id,
            'title' => $request->title,
            'type' => $request->type,
            'description' => $request->description,
            'file_path' => $path,
            'original_filename' => $originalName
        ]);

        return back()->with('success', 'Laporan berhasil diunggah!');
    }

    /**
     * Fitur Smart File Organizer: Mengunduh semua laporan dalam satu ZIP yang terorganisir.
     */
    public function downloadEventReports($eventId) {
        if (Auth::user()->role !== 'admin') abort(403);

        $activity = Activity::with('reports.user')->findOrFail($eventId);
        $reports = $activity->reports;

        if ($reports->isEmpty()) {
            return back()->with('error', 'Belum ada laporan untuk kegiatan ini.');
        }

        // 1. Persiapan Path
        $slugEvent = Str::slug($activity->title);
        $tempFolderName = 'temp_' . $slugEvent . '_' . time();
        $tempPath = storage_path('app/temp/' . $tempFolderName);
        $zipFileName = 'Laporan_' . $slugEvent . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        // 2. Buat folder sementara dan subfolder kategori
        $categories = ['Narasi', 'Keuangan', 'Dokumentasi'];
        foreach ($categories as $cat) {
            File::makeDirectory($tempPath . '/' . $cat, 0755, true, true);
        }

        // 3. Salin dan ganti nama file laporan ke folder kategori
        foreach ($reports as $report) {
            $sourceFile = storage_path('app/public/' . $report->file_path);
            
            if (File::exists($sourceFile)) {
                $extension = File::extension($sourceFile);
                
                // Format Nama: nama_pegawai_kategori_judul.ext
                $cleanName = Str::slug($report->user->name) . '_' . Str::slug($report->type) . '_' . Str::slug($report->title);
                $newFileName = $cleanName . '.' . $extension;

                // Tentukan subfolder tujuan (Mapping simple)
                $subFolder = 'Dokumentasi';
                if (Str::contains($report->type, 'Narasi')) $subFolder = 'Narasi';
                if (Str::contains($report->type, 'Keuangan')) $subFolder = 'Keuangan';

                File::copy($sourceFile, $tempPath . '/' . $subFolder . '/' . $newFileName);
            }
        }

        // 4. Proses Zipping
        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($tempPath),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($tempPath) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();
        }

        // 5. Bersihkan Folder Sementara (Hanya hapus foldernya, bukan ZIP nya dulu)
        File::deleteDirectory($tempPath);

        // 6. Kirim file ZIP ke browser dan hapus setelah terkirim
        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function destroy($id) {
        if(Auth::user()->role !== 'admin') abort(403);
        $report = Report::findOrFail($id);
        Storage::disk('public')->delete($report->file_path);
        $report->delete();
        return back()->with('success', 'Laporan dihapus.');
    }

    public function download($id) {
        $report = Report::findOrFail($id);
        return Storage::disk('public')->download($report->file_path, $report->original_filename);
    }
}