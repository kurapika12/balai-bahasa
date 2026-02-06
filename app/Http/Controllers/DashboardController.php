<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil data aktiviti bersama laporan dan kakitangan yang terlibat
        $activities = Activity::with(['reports.user', 'involvedEmployees'])->latest()->get();

        // Ambil senarai kakitangan untuk pengurusan akaun
        $users = User::where('role', 'pegawai')->get();

        if (Auth::user()->role == 'admin') {
            $totalReports = Report::count();
            return view('dashboard.admin', compact('activities', 'totalReports', 'users'));
        } else {
            return view('dashboard.pegawai', compact('activities'));
        }
    }

    // --- PENGURUSAN AKTIVITI ---

    public function storeActivity(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'status' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $activity = Activity::create($request->only(['title', 'description', 'start_date', 'end_date', 'status']));

        // Simpan kakitangan yang terlibat (Relasi Many-to-Many)
        if ($request->has('involved_users')) {
            $activity->involvedEmployees()->attach($request->involved_users);
        }

        return back()->with('success', 'Kegiatan baru berhasil ditambahkan!');
    }

    public function updateActivity(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $request->validate([
            'title' => 'required|string|max:255',
            'status' => 'required',
        ]);

        $activity = Activity::findOrFail($id);
        $activity->update($request->only(['title', 'description', 'start_date', 'end_date', 'status']));

        // Kemas kini kakitangan yang terlibat
        $activity->involvedEmployees()->sync($request->involved_users ?? []);

        return back()->with('success', 'Data kegiatan berhasil diperbarui!');
    }

    public function destroyActivity($id)
    {
        if (Auth::user()->role !== 'admin') abort(403);
        Activity::findOrFail($id)->delete();
        return back()->with('success', 'Kegiatan berhasil dihapus.');
    }

    // --- PENGURUSAN KAKITANGAN (PEGAWAI) ---

    public function storeUser(Request $request)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:6'
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'pegawai'
        ]);

        return back()->with('success', 'User baru berhasil ditambahkan!');
    }

    public function updateUser(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $user->id,
        ]);

        $data = [
            'name' => $request->name,
            'username' => $request->username
        ];

        // Tukar kata laluan hanya jika ruangan diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        return back()->with('success', 'Data user berhasil di perbarui!');
    }

    public function destroyUser($id)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $user = User::findOrFail($id);
        $user->delete();

        return back()->with('success', 'Akun user berhasil dihapus.');
    }
}
