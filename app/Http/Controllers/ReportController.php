<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
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
            'original_filename' => $originalName,
            'executive_summary' => $request->executive_summary // Menyimpan hasil ringkasan eksekutif
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

    /**
     * Membaca isi file (DOCX/PDF/Gambar) dan membuat Ringkasan Eksekutif Otomatis menggunakan Gemini API
     * VERSI PINTAR: Mendukung auto-fallback model dari gemini-2.5-flash ke gemini-1.5-flash jika tidak terdaftar
     */
    public function summarize(Request $request) {
        $request->validate([
            'file' => 'required|file|max:10240'
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        $apiKey = env('GEMINI_API_KEY');
        
        if (!$apiKey) {
            return response()->json(['success' => false, 'message' => 'Konfigurasi GEMINI_API_KEY tidak ditemukan dalam file .env proyek Anda.'], 500);
        }

        $textToSummarize = "";
        $useDirectGemini = false;

        // 1. Ekstraksi teks mandiri dari file Word (.docx) & Teks (.txt)
        if ($extension === 'docx') {
            if (class_exists(\ZipArchive::class)) {
                $zip = new \ZipArchive;
                if ($zip->open($file->getRealPath()) === true) {
                    if (($index = $zip->locateName('word/document.xml')) !== false) {
                        $xmlContent = $zip->getFromIndex($index);
                        // Bersihkan tag XML untuk mendapatkan isi tulisan murni
                        $textToSummarize = html_entity_decode(strip_tags($xmlContent));
                    }
                    $zip->close();
                }
            } else {
                // Jika ekstensi ZipArchive PHP mati, kirim langsung ke Gemini sebagai fallback
                $useDirectGemini = true;
            }
        } elseif ($extension === 'txt') {
            $textToSummarize = file_get_contents($file->getRealPath());
        } else {
            // PDF & Gambar dikirim langsung menggunakan Model Visi Gemini
            $useDirectGemini = true;
        }

        // 2. Prompt Standar Operasional Prosedur (SOP) Birokrasi Pemerintahan Indonesia
        $prompt = "Tugas Anda adalah merangkum laporan administrasi kantor dinas instansi pemerintah. 
                   Buatlah Ringkasan Eksekutif (Executive Summary) yang formal, baku, dan profesional dari dokumen berikut dalam 1-2 paragraf padat (maksimal 120 kata).
                   
                   STRUKTUR WAJIB YANG HARUS ADA:
                   - Paragraf Awal: Deskripsikan pelaksanaan kegiatan secara formal (Nama agenda, waktu, dan tempat pelaksanaan).
                   - Paragraf Tengah: Jabarkan pencapaian riil atau hasil nyata dari kegiatan tersebut secara objektif.
                   - Paragraf Akhir: Sebutkan kendala lapangan yang dihadapi dan berikan satu rekomendasi perbaikan untuk pimpinan.

                   Gunakan Bahasa Indonesia yang sangat formal, baku, santun, dan langsung pada intinya.";

        // Daftar model yang akan dicoba secara berurutan (Model Baru -> Model Lama)
        $modelsToTry = ['gemini-2.5-flash', 'gemini-1.5-flash'];
        $response = null;
        $success = false;
        $lastErrorMessage = 'Gagal memproses AI.';

        // 3. Eksekusi Request dengan Multi-Model Fallback
        foreach ($modelsToTry as $modelName) {
            $url = "https://generativelanguage.googleapis.com/v1/models/{$modelName}:generateContent?key={$apiKey}";

            try {
                if ($useDirectGemini) {
                    $fileData = base64_encode(file_get_contents($file));
                    $mimeType = $file->getMimeType();

                    $response = Http::withoutVerifying()
                        ->timeout(60)
                        ->post($url, [
                            "contents" => [["parts" => [
                                ["text" => $prompt],
                                ["inline_data" => ["mime_type" => $mimeType, "data" => $fileData]]
                            ]]]
                        ]);
                } else {
                    if (empty(trim($textToSummarize))) {
                        $textToSummarize = "Dokumen Word kosong atau tidak dapat diekstrak teksnya.";
                    }

                    $response = Http::withoutVerifying()
                        ->timeout(60)
                        ->post($url, [
                            "contents" => [["parts" => [
                                ["text" => $prompt . "\n\nIsi Dokumen Mentah:\n" . Str::limit($textToSummarize, 8000)]
                            ]]]
                        ]);
                }

                // Periksa apakah respons dari server Google sukses
                if ($response->successful()) {
                    $success = true;
                    break; // Berhasil! Keluar dari loop pencarian model
                } else {
                    $errorBody = $response->json();
                    $lastErrorMessage = isset($errorBody['error']['message']) ? $errorBody['error']['message'] : 'Respons tidak dikenal.';
                    
                    // Jika kesalahan karena model tidak ditemukan/tidak didukung, coba model berikutnya di dalam daftar loop
                    if (Str::contains(strtolower($lastErrorMessage), ['not found', 'not supported', 'unsupported'])) {
                        continue;
                    }
                    
                    // Jika kesalahan lain (misalnya API key salah atau kuota habis), hentikan loop agar pesan error tampil
                    break;
                }
            } catch (\Exception $e) {
                $lastErrorMessage = $e->getMessage();
                continue; // Terjadi kendala jaringan/koneksi, coba model berikutnya
            }
        }

        // 4. Pengiriman Respon Akhir ke Frontend
        if ($success && $response) {
            $summaryResult = $response->json('candidates.0.content.parts.0.text');
            
            // Hilangkan format tebal markdown (*) agar teks nyaman diedit langsung oleh pegawai
            $cleanSummary = trim(str_replace(['*', '#', '_'], '', $summaryResult));

            return response()->json([
                'success' => true,
                'summary' => $cleanSummary
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Validasi gagal di semua model: ' . $lastErrorMessage
        ], 400);
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