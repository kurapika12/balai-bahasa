<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function index() {
        // Ambil kegiatan beserta laporan dan user pembuatnya
        $activities = Activity::with(['reports.user'])->latest('date')->get();

        if (Auth::user()->role == 'admin') {
            $totalReports = Report::count();
            return view('dashboard.admin', compact('activities', 'totalReports'));
        } else {
            return view('dashboard.pegawai', compact('activities'));
        }
    }

    // Fungsi Admin Tambah Kegiatan
    public function storeActivity(Request $request) {
        $request->validate([
            'title' => 'required',
            'status' => 'required'
        ]);
        Activity::create($request->all());
        return back()->with('success', 'Kegiatan dibuat!');
    }

    // Fungsi Admin Edit Kegiatan (BARU)
    public function updateActivity(Request $request, $id) {
        if (Auth::user()->role !== 'admin') abort(403);

        $request->validate([
            'title' => 'required',
            'status' => 'required'
        ]);

        $activity = Activity::findOrFail($id);
        $activity->update($request->all());

        return back()->with('success', 'Data kegiatan berhasil diperbarui!');
    }

    // Fungsi Admin Hapus Kegiatan
    public function destroyActivity($id) {
        if (Auth::user()->role !== 'admin') abort(403);

        $activity = Activity::findOrFail($id);
        $activity->delete();

        return back()->with('success', 'Kegiatan berhasil dihapus.');
    }

    // Fungsi Admin Tambah Pegawai
    public function storeUser(Request $request) {
        if (Auth::user()->role !== 'admin') abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users',
            'password' => 'required|string|min:6'
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'pegawai'
        ]);

        return back()->with('success', 'Pegawai baru berhasil ditambahkan.');
    }
}
