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
            'executive_summary' => $request->executive_summary,
            'status' => 'Submitted' // Default saat pertama diunggah
        ]);

        return back()->with('success', 'Laporan berhasil diunggah dan masuk antrean pemeriksaan!');
    }

    /**
     * Smart File Organizer: HANYA MENGUNDUH LAPORAN YANG SUDAH APPROVED.
     */
    public function downloadEventReports($eventId) {
        if (Auth::user()->role !== 'admin') abort(403);

        $activity = Activity::with('reports.user')->findOrFail($eventId);
        // HANYA ambil laporan yang statusnya Approved
        $reports = $activity->reports()->where('status', 'Approved')->get();

        if ($reports->isEmpty()) {
            return back()->with('error', 'Belum ada laporan yang disetujui (Approved) untuk kegiatan ini. Sistem pengarsipan otomatis hanya mengunduh dokumen yang telah sah.');
        }

        $slugEvent = Str::slug($activity->title);
        $tempFolderName = 'temp_' . $slugEvent . '_' . time();
        $tempPath = storage_path('app/temp/' . $tempFolderName);
        $zipFileName = 'Laporan_Sah_' . $slugEvent . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        $categories = ['Narasi', 'Keuangan', 'Dokumentasi'];
        foreach ($categories as $cat) {
            File::makeDirectory($tempPath . '/' . $cat, 0755, true, true);
        }

        foreach ($reports as $report) {
            $sourceFile = storage_path('app/public/' . $report->file_path);
            if (File::exists($sourceFile)) {
                $extension = File::extension($sourceFile);
                $cleanName = Str::slug($report->user->name) . '_' . Str::slug($report->type) . '_' . Str::slug($report->title);
                $newFileName = $cleanName . '.' . $extension;

                $subFolder = 'Dokumentasi';
                if (Str::contains($report->type, 'Narasi')) $subFolder = 'Narasi';
                if (Str::contains($report->type, 'Keuangan')) $subFolder = 'Keuangan';

                File::copy($sourceFile, $tempPath . '/' . $subFolder . '/' . $newFileName);
            }
        }

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($tempPath), \RecursiveIteratorIterator::LEAVES_ONLY);
            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($tempPath) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();
        }

        File::deleteDirectory($tempPath);
        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    /**
     * API untuk merangkum file menggunakan Gemini.
     */
    public function summarize(Request $request) {
        $request->validate(['file' => 'required|file|max:10240']);
        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        $apiKey = env('GEMINI_API_KEY');
        
        if (!$apiKey) return response()->json(['success' => false, 'message' => 'Konfigurasi GEMINI_API_KEY tidak ditemukan.'], 500);

        $textToSummarize = "";
        $useDirectGemini = false;

        if ($extension === 'docx') {
            if (class_exists(\ZipArchive::class)) {
                $zip = new \ZipArchive;
                if ($zip->open($file->getRealPath()) === true) {
                    if (($index = $zip->locateName('word/document.xml')) !== false) {
                        $xmlContent = $zip->getFromIndex($index);
                        $textToSummarize = html_entity_decode(strip_tags($xmlContent));
                    }
                    $zip->close();
                }
            } else { $useDirectGemini = true; }
        } elseif ($extension === 'txt') {
            $textToSummarize = file_get_contents($file->getRealPath());
        } else {
            $useDirectGemini = true;
        }

        $prompt = "Tugas Anda merangkum laporan instansi pemerintah. Buat Executive Summary yang formal dalam 1-2 paragraf padat (maks 120 kata). STRUKTUR: Esensi kegiatan, pencapaian riil, kendala & rekomendasi. Bahasa sangat baku.";

        $modelsToTry = ['gemini-2.5-flash', 'gemini-1.5-flash'];
        $response = null;
        $success = false;
        $lastErrorMessage = 'Gagal memproses AI.';

        foreach ($modelsToTry as $modelName) {
            $url = "https://generativelanguage.googleapis.com/v1/models/{$modelName}:generateContent?key={$apiKey}";
            try {
                if ($useDirectGemini) {
                    $fileData = base64_encode(file_get_contents($file));
                    $mimeType = $file->getMimeType();
                    $response = Http::withoutVerifying()->timeout(60)->post($url, [
                        "contents" => [["parts" => [["text" => $prompt], ["inline_data" => ["mime_type" => $mimeType, "data" => $fileData]]]]]
                    ]);
                } else {
                    if (empty(trim($textToSummarize))) $textToSummarize = "Dokumen kosong.";
                    $response = Http::withoutVerifying()->timeout(60)->post($url, [
                        "contents" => [["parts" => [["text" => $prompt . "\n\nDokumen:\n" . Str::limit($textToSummarize, 8000)]]]]
                    ]);
                }

                if ($response->successful()) {
                    $success = true; break;
                } else {
                    $errorBody = $response->json();
                    $lastErrorMessage = isset($errorBody['error']['message']) ? $errorBody['error']['message'] : 'Error API.';
                    if (Str::contains(strtolower($lastErrorMessage), ['not found', 'not supported'])) continue;
                    break;
                }
            } catch (\Exception $e) { $lastErrorMessage = $e->getMessage(); continue; }
        }

        if ($success && $response) {
            $summaryResult = $response->json('candidates.0.content.parts.0.text');
            $cleanSummary = trim(str_replace(['*', '#', '_'], '', $summaryResult));
            return response()->json(['success' => true, 'summary' => $cleanSummary]);
        }
        return response()->json(['success' => false, 'message' => 'Validasi AI gagal: ' . $lastErrorMessage], 400);
    }

    public function updateSummary(Request $request, $id) {
        if (Auth::user()->role !== 'admin') return response()->json(['success' => false], 403);
        $request->validate(['executive_summary' => 'required|string']);
        Report::findOrFail($id)->update(['executive_summary' => $request->executive_summary]);
        return response()->json(['success' => true]);
    }

    // --- FITUR BARU: APPROVAL WORKFLOW ---

    public function markAsReviewed($id) {
        if (Auth::user()->role !== 'admin') return response()->json(['success' => false], 403);
        $report = Report::findOrFail($id);
        if($report->status === 'Submitted') {
            $report->update(['status' => 'Reviewed']);
        }
        return response()->json(['success' => true, 'status' => $report->status]);
    }

    public function updateStatus(Request $request, $id) {
        if (Auth::user()->role !== 'admin') return response()->json(['success' => false], 403);
        $request->validate(['status' => 'required|in:Approved,Rejected']);
        
        $report = Report::findOrFail($id);
        $report->status = $request->status;
        $report->rejection_note = $request->status === 'Rejected' ? $request->rejection_note : null;
        $report->save();

        return response()->json(['success' => true]);
    }

    public function revise(Request $request, $id) {
        $report = Report::findOrFail($id);
        if ($report->user_id !== Auth::id()) abort(403);

        $request->validate([
            'file' => 'required|mimes:pdf,doc,docx,xls,xlsx,jpg,png|max:10240',
            'executive_summary' => 'required|string'
        ]);

        // Hapus file lama untuk cegah server membengkak
        Storage::disk('public')->delete($report->file_path);

        // Upload file baru
        $file = $request->file('file');
        $path = $file->store('reports', 'public');

        // Reset Status & Update Data
        $report->update([
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'executive_summary' => $request->executive_summary,
            'status' => 'Submitted',
            'rejection_note' => null, // Bersihkan catatan penolakan lama
            'updated_at' => now()
        ]);

        return back()->with('success', 'Laporan berhasil direvisi dan dikembalikan ke antrean Admin!');
    }

    public function destroy($id) {
        $report = Report::findOrFail($id);
        // Pengecekan Keamanan: File Approved tidak boleh dihapus
        if ($report->status === 'Approved' && Auth::user()->role !== 'admin') {
            return back()->with('error', 'Laporan yang telah disetujui tidak dapat dihapus.');
        }
        Storage::disk('public')->delete($report->file_path);
        $report->delete();
        return back()->with('success', 'Laporan dihapus.');
    }

    public function download($id) {
        $report = Report::findOrFail($id);
        return Storage::disk('public')->download($report->file_path, $report->original_filename);
    }
}