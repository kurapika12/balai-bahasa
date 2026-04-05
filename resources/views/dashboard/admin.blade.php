@extends('layout')

@section('content')
<!-- Header & Navigation Tabs -->
<div class="mb-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-blue-900">Panel Administrasi</h2>
            <p class="text-gray-500 text-sm">Kelola kegiatan, laporan, dan data pegawai Balai Bahasa.</p>
        </div>

        <!-- Tab Navigation Buttons -->
        <div class="bg-white p-1 rounded-lg border border-gray-200 shadow-sm flex">
            <button onclick="showSection('dashboard')" id="nav-dashboard" class="px-5 py-2 rounded-md text-sm font-bold transition-all bg-blue-900 text-white shadow-sm">
                <i class="fa-solid fa-chart-pie mr-2"></i> Dashboard & Kegiatan
            </button>
            <button onclick="showSection('pegawai')" id="nav-pegawai" class="px-5 py-2 rounded-md text-sm font-bold text-gray-500 hover:bg-gray-100 transition-all">
                <i class="fa-solid fa-users-gear mr-2"></i> Data Pegawai
            </button>
        </div>
    </div>
</div>

<!-- SECTION 1: DASHBOARD & KEGIATAN -->
<div id="section-dashboard" class="space-y-8 animate-fade-in">

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-blue-600
            transition transform hover:-translate-y-1 hover:shadow-md">
            <div>
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Total Kegiatan</p>
                <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $activities->count() }}</h3>
            </div>
            <div class="h-12 w-12 bg-blue-50 rounded-full flex items-center justify-center text-blue-600 text-xl">
                <i class="fa-regular fa-calendar-check"></i>
            </div>
        </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-blue-600
            transition transform hover:-translate-y-1 hover:shadow-md">
            <div>
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Laporan Masuk</p>
                <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $totalReports }}</h3>
            </div>
            <div class="h-12 w-12 bg-green-50 rounded-full flex items-center justify-center text-green-600 text-xl">
                <i class="fa-solid fa-box-archive"></i>
            </div>
        </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-blue-600
            transition transform hover:-translate-y-1 hover:shadow-md">
            <div>
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Pegawai Aktif</p>
                <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $users->count() }}</h3>
            </div>
            <div class="h-12 w-12 bg-yellow-100 rounded-full flex items-center justify-center text-yellow-600 text-xl">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>
    </div>

    <!-- Activity Section -->
    <div>
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                <i class="fa-solid fa-folder-open text-yellow-600"></i> Daftar Agenda Kegiatan
            </h3>
            <button onclick="document.getElementById('modalNewActivity').classList.remove('hidden')" class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded shadow-sm font-bold flex items-center gap-2 text-sm transition">
                <i class="fa-solid fa-plus-circle"></i> Kegiatan Baru
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($activities as $activity)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200
            transition-all duration-300 ease-out
            hover:-translate-y-1 hover:shadow-md
            flex flex-col h-full group">
                <div class="h-3 bg-blue-900 group-hover:bg-yellow-500 transition-colors duration-300 rounded-t-[28px]"></div>
                <div class="p-6 flex-grow">
                    <div class="flex items-center justify-between mb-3">
                        <span class="bg-blue-50 text-blue-700 text-[10px] font-bold px-2 py-1 rounded border border-blue-100">
                            {{ $activity->start_date ? \Carbon\Carbon::parse($activity->start_date)->format('d M') : '?' }} - {{ $activity->end_date ? \Carbon\Carbon::parse($activity->end_date)->format('d M Y') : '?' }}
                        </span>
                        @php
                            $statusColor = 'bg-gray-100 text-gray-600';
                            if($activity->status == 'Sedang Berlangsung') $statusColor = 'bg-green-100 text-green-700 border-green-200';
                            if($activity->status == 'Akan Datang') $statusColor = 'bg-blue-100 text-blue-700 border-blue-200';
                            if($activity->status == 'Selesai') $statusColor = 'bg-gray-100 text-gray-500 border-gray-200';
                        @endphp
                        <span class="text-[10px] font-bold px-2 py-1 rounded border {{ $statusColor }}">
                            {{ $activity->status }}
                        </span>
                    </div>
                    <h4 class="font-bold text-lg text-gray-800 mb-2 line-clamp-2
           group-hover:text-blue-900 transition-colors duration-300">{{ $activity->title }}</h4>
                    <p class="text-gray-500 text-sm line-clamp-2">{{ $activity->description }}</p>

                    <!-- Terlibat Count -->
                    <div class="mt-4 flex items-center gap-2">
                        <div class="flex -space-x-2">
                            @foreach($activity->involvedEmployees->take(3) as $emp)
                                <div class="h-6 w-6 rounded-full bg-blue-100 border-2 border-white flex items-center justify-center text-[10px] font-bold text-blue-600" title="{{ $emp->name }}">
                                    {{ substr($emp->name, 0, 1) }}
                                </div>
                            @endforeach
                        </div>
                        <span class="text-[10px] text-gray-400 font-medium">{{ $activity->involvedEmployees->count() }} Pegawai Terlibat</span>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 rounded-b-[28px]">
                    <button onclick="openAdminModal({{ $activity->id }})" class="w-full text-blue-700 hover:text-blue-900 text-sm font-bold flex justify-center items-center gap-2">
                        Kelola Arsip & Laporan <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>
            @empty
            <div class="col-span-3 text-center py-12 bg-white border-2 border-dashed border-gray-300 rounded-lg">
                <p class="text-gray-500 italic">Belum ada agenda kegiatan yang dibuat.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- SECTION 2: KELOLA PEGAWAI -->
<div id="section-pegawai" class="space-y-6 hidden animate-fade-in">
    <div class="flex justify-between items-center bg-white p-4 rounded-lg shadow-sm border border-gray-200">
        <div>
            <h3 class="font-bold text-gray-800 text-lg">Manajemen Akun Pegawai</h3>
            <p class="text-sm text-gray-500">Daftar pegawai yang memiliki akses ke sistem pelaporan.</p>
        </div>
        <button onclick="document.getElementById('modalNewUser').classList.remove('hidden')" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded shadow-sm font-bold flex items-center gap-2 text-sm transition">
            <i class="fa-solid fa-user-plus"></i> Tambah Pegawai
        </button>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Identitas Pegawai</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Username / NIP</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status Akun</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                <tr class="hover:bg-blue-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="h-9 w-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold mr-3 border border-blue-200">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div class="text-sm font-bold text-gray-800">{{ $user->name }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-mono bg-gray-50 px-2 py-1 rounded w-fit">
                        {{ $user->username }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-[10px] leading-5 font-bold rounded-full bg-green-100 text-green-800 uppercase border border-green-200">Aktif</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button onclick='openEditUserModal(@json($user))' class="text-blue-600 hover:text-blue-900 font-bold mr-4">
                            <i class="fa-solid fa-pen-to-square mr-1"></i> Edit
                        </button>
                        <form action="/users/{{ $user->id }}" method="POST" onsubmit="confirmDeleteUser(event, this)" class="inline">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 hover:text-red-900 font-bold">
                                <i class="fa-solid fa-trash-can mr-1"></i> Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic font-medium">Belum ada data pegawai terdaftar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL: TAMBAH KEGIATAN BARU -->
<div id="modalNewActivity" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full overflow-hidden transform transition-all">
        <div class="bg-blue-900 px-6 py-4 flex justify-between items-center">
            <h3 class="font-bold text-white text-lg">Buat Agenda Kegiatan</h3>
            <button onclick="document.getElementById('modalNewActivity').classList.add('hidden')" class="text-blue-200 hover:text-white">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <form action="/activities" method="POST" class="p-6 overflow-y-auto max-h-[80vh]">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Judul Kegiatan</label>
                    <input type="text" name="title" class="w-full border-gray-300 rounded-md shadow-sm border p-2.5 focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: Rapat Koordinasi Tahunan" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi Singkat</label>
                    <textarea name="description" class="w-full border-gray-300 rounded-md shadow-sm border p-2.5 focus:ring-blue-500 focus:border-blue-500" rows="2"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="w-full border-gray-300 rounded-md shadow-sm border p-2.5 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Selesai</label>
                        <input type="date" name="end_date" class="w-full border-gray-300 rounded-md shadow-sm border p-2.5 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Status Saat Ini</label>
                    <select name="status" class="w-full border-gray-300 rounded-md shadow-sm border p-2.5 focus:ring-blue-500 focus:border-blue-500">
                        <option value="Akan Datang">Akan Datang</option>
                        <option value="Sedang Berlangsung">Sedang Berlangsung</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>

                <!-- Multi-select Pegawai Terlibat -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Pegawai yang Terlibat</label>
                    <div class="grid grid-cols-2 gap-2 max-h-40 overflow-y-auto border p-3 rounded-md bg-gray-50 border-gray-200">
                        @foreach($users as $user)
                        <label class="flex items-center gap-2 text-sm cursor-pointer hover:bg-white p-1 rounded transition">
                            <input type="checkbox" name="involved_users[]" value="{{ $user->id }}" class="rounded text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="text-gray-700 truncate">{{ $user->name }}</span>
                        </label>
                        @endforeach
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1">* Pegawai lain tetap bisa mengunggah laporan meskipun tidak dicentang.</p>
                </div>
            </div>
            <div class="mt-8 flex justify-end gap-3 border-t pt-4">
                <button type="button" onclick="document.getElementById('modalNewActivity').classList.add('hidden')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-medium rounded transition">Batal</button>
                <button type="submit" class="px-6 py-2 bg-blue-900 hover:bg-blue-800 text-white text-sm font-bold rounded shadow transition">Simpan Kegiatan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: MANAGE ACTIVITY (DETAIL & REPORTS) -->
<div id="adminModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-6xl h-[88vh] overflow-hidden flex flex-col transform scale-95 transition-transform duration-300" id="adminModalContent">

        <!-- Modal Header -->
        <div class="bg-blue-900 px-6 py-4 flex justify-between items-center flex-shrink-0">
            <div>
                <h3 class="font-bold text-white text-xl" id="admModalTitle">Judul Kegiatan</h3>
                <p class="text-blue-200 text-xs" id="admModalDate">Periode: -</p>
            </div>
            <button onclick="closeAdminModal()" class="text-white hover:text-yellow-400 text-2xl transition"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="flex flex-col lg:flex-row flex-grow overflow-hidden text-sm">
            <!-- Left Panel: Info & Settings -->
            <div class="w-full lg:w-1/3 bg-gray-50 p-6 overflow-y-auto border-b lg:border-b-0 lg:border-r border-gray-200 flex flex-col justify-between">
                <div class="space-y-6">
                    <h4 class="font-bold text-blue-900 uppercase tracking-widest text-xs flex items-center gap-2">
                        <i class="fa-solid fa-circle-info"></i> Detail Informasi
                    </h4>

                    <div class="space-y-4">
                        <div>
                            <span class="block text-[10px] font-bold text-gray-400 uppercase">Status</span>
                            <span id="admModalStatus" class="inline-block mt-1 px-2 py-0.5 rounded-full bg-gray-200 text-gray-700 text-[10px] font-bold uppercase">-</span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold text-gray-400 uppercase">Deskripsi</span>
                            <p id="admModalDesc" class="text-gray-700 leading-relaxed italic">"..."</p>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold text-gray-400 uppercase">Pegawai Terlibat</span>
                            <div id="admModalInvolved" class="flex flex-wrap gap-1 mt-2">
                                <!-- JS Populated badges -->
                            </div>
                        </div>

                        <div class="bg-white p-3 rounded-lg border border-gray-100 shadow-sm">
                            <span class="block text-[10px] font-bold text-blue-800 uppercase mb-2">Klasifikasi Dokumen</span>
                            <ul class="space-y-1 text-xs text-gray-600" id="admStats"></ul>
                        </div>
                    </div>

                    <button id="btnEditActivity" class="w-full bg-white border-2 border-yellow-500 text-yellow-600 hover:bg-yellow-500 hover:text-white font-bold py-2 rounded-lg transition shadow-sm flex justify-center items-center gap-2">
                        <i class="fa-solid fa-pen-to-square"></i> Edit Data Kegiatan
                    </button>
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <h4 class="font-bold text-gray-700 text-xs uppercase mb-3">Unduh Semua</h4>
                        <!-- Tombol Download ZIP -->
                        <a id="btnDownloadAll" href="#" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 rounded-lg transition shadow-sm flex justify-center items-center gap-2 text-sm">
                            <i class="fa-solid fa-file-zipper"></i> Download Laporan Event
                        </a>
                        <p class="text-[10px] text-gray-400 mt-2 italic text-center">
                            * File akan dikelompokkan otomatis ke dalam folder Narasi, Keuangan, dan Dokumentasi.
                        </p>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-red-200">
                    <form id="formDeleteActivity" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confirmDeleteActivity(this)" class="w-full bg-red-50 text-red-700 hover:bg-red-600 hover:text-white border border-red-200 font-bold py-2 rounded-lg transition flex justify-center items-center gap-2">
                            <i class="fa-solid fa-trash-can"></i> Hapus Kegiatan
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Panel: Reports List -->
            <div class="w-full lg:w-2/3 bg-white p-6 overflow-y-auto">
                <div class="flex justify-between items-center mb-6 border-b pb-3">
                    <h4 class="font-bold text-gray-800 flex items-center gap-2 text-lg">
                        <i class="fa-solid fa-folder-tree text-yellow-600"></i> Berkas Laporan Masuk
                    </h4>
                    <span class="text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded-full font-bold" id="admReportCount">0 Berkas</span>
                </div>

                <div class="space-y-3" id="admReportsList">
                    <!-- Populated by JS -->
                    <p class="text-center text-gray-400 py-10 italic">Memuat data laporan...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: EDIT DATA KEGIATAN -->
<div id="modalEditActivity" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 z-[60] flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-lg shadow-2xl max-w-md w-full overflow-hidden transform">
        <div class="bg-yellow-500 px-6 py-4 flex justify-between items-center">
            <h3 class="font-bold text-white text-lg">Edit Kegiatan</h3>
            <button onclick="document.getElementById('modalEditActivity').classList.add('hidden')" class="text-yellow-100 hover:text-white"><i class="fa-solid fa-xmark text-xl"></i></button>
        </div>
        <form id="formEditActivity" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Judul</label>
                <input type="text" id="editTitle" name="title" class="w-full border border-gray-300 rounded p-2 focus:ring-yellow-500" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Deskripsi</label>
                <textarea id="editDesc" name="description" class="w-full border border-gray-300 rounded p-2 focus:ring-yellow-500" rows="3"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tgl Mulai</label>
                    <input type="date" id="editStartDate" name="start_date" class="w-full border border-gray-300 rounded p-2 focus:ring-yellow-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tgl Selesai</label>
                    <input type="date" id="editEndDate" name="end_date" class="w-full border border-gray-300 rounded p-2 focus:ring-yellow-500">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Status</label>
                <select id="editStatus" name="status" class="w-full border border-gray-300 rounded p-2 focus:ring-yellow-500">
                    <option value="Akan Datang">Akan Datang</option>
                    <option value="Sedang Berlangsung">Sedang Berlangsung</option>
                    <option value="Selesai">Selesai</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Pembaruan Pegawai Terlibat</label>
                <div class="grid grid-cols-2 gap-1 max-h-32 overflow-y-auto border p-2 rounded bg-gray-50 border-gray-200" id="editInvolvedList">
                    @foreach($users as $user)
                    <label class="flex items-center gap-2 text-xs cursor-pointer"><input type="checkbox" name="involved_users[]" value="{{ $user->id }}" class="edit-checkbox rounded" data-user-id="{{ $user->id }}"> {{ $user->name }}</label>
                    @endforeach
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3 border-t pt-4">
                <button type="button" onclick="document.getElementById('modalEditActivity').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-600 rounded text-sm">Batal</button>
                <button type="submit" class="px-6 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded shadow text-sm">Update Data</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: TAMBAH / EDIT PEGAWAI (Same as Previous Version) -->
<div id="modalNewUser" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-lg shadow-2xl max-w-md w-full overflow-hidden transform transition-all">
        <div class="bg-yellow-500 px-6 py-4 flex justify-between items-center text-white">
            <h3 class="font-bold text-lg">Tambah Pegawai Baru</h3>
            <button onclick="document.getElementById('modalNewUser').classList.add('hidden')"><i class="fa-solid fa-xmark text-xl"></i></button>
        </div>
        <form action="/users" method="POST" class="p-6 space-y-4">
            @csrf
            <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Lengkap</label><input type="text" name="name" class="w-full border rounded p-2.5 shadow-sm" required></div>
            <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Username / NIP</label><input type="text" name="username" class="w-full border rounded p-2.5 shadow-sm" required></div>
            <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Password</label><input type="text" name="password" class="w-full border rounded p-2.5 shadow-sm" placeholder="Min. 6 karakter" required></div>
            <div class="mt-6 flex justify-end gap-3"><button type="button" onclick="document.getElementById('modalNewUser').classList.add('hidden')" class="px-4 py-2 text-gray-500 text-sm">Batal</button><button type="submit" class="bg-yellow-500 text-white px-6 py-2 rounded font-bold shadow text-sm">Simpan Akun</button></div>
        </form>
    </div>
</div>

<div id="modalEditUser" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-[60] flex items-center justify-center p-4 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-lg shadow-2xl max-w-md w-full overflow-hidden transform transition-all">
        <div class="bg-blue-600 px-6 py-4 flex justify-between items-center text-white">
            <h3 class="font-bold text-lg">Edit Akun Pegawai</h3>
            <button onclick="document.getElementById('modalEditUser').classList.add('hidden')"><i class="fa-solid fa-xmark text-xl"></i></button>
        </div>
        <form id="formEditUser" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Lengkap</label><input type="text" id="editUserName" name="name" class="w-full border rounded p-2.5" required></div>
            <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Username / NIP</label><input type="text" id="editUserUsername" name="username" class="w-full border rounded p-2.5" required></div>
            <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Password Baru (Kosongkan jika tidak ganti)</label><input type="text" name="password" class="w-full border rounded p-2.5" placeholder="Reset Password"></div>
            <div class="mt-6 flex justify-end gap-3"><button type="button" onclick="document.getElementById('modalEditUser').classList.add('hidden')" class="px-4 py-2 text-gray-500 text-sm">Batal</button><button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded font-bold shadow text-sm">Update Akun</button></div>
        </form>
    </div>
</div>

<!-- SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const activitiesData = @json($activities);

    function showSection(id) {
        document.getElementById('section-dashboard').classList.toggle('hidden', id !== 'dashboard');
        document.getElementById('section-pegawai').classList.toggle('hidden', id !== 'pegawai');

        const tabs = ['nav-dashboard', 'nav-pegawai'];
        tabs.forEach(t => {
            const el = document.getElementById(t);
            if(t.includes(id)) {
                el.className = 'px-5 py-2 rounded-md text-sm font-bold transition-all bg-blue-900 text-white shadow-sm';
            } else {
                el.className = 'px-5 py-2 rounded-md text-sm font-bold text-gray-500 hover:bg-gray-100 transition-all';
            }
        });
    }

    function openAdminModal(id) {
        const activity = activitiesData.find(a => a.id === id);
        if(!activity) return;

        // 1. Basic Info
        document.getElementById('admModalTitle').innerText = activity.title;
        const startDate = activity.start_date ? new Date(activity.start_date).toLocaleDateString('id-ID', {day:'numeric', month:'long'}) : '?';
        const endDate = activity.end_date ? new Date(activity.end_date).toLocaleDateString('id-ID', {day:'numeric', month:'long', year:'numeric'}) : '?';
        document.getElementById('admModalDate').innerText = `Periode: ${startDate} s/d ${endDate}`;
        document.getElementById('admModalDesc').innerText = activity.description || 'Tidak ada deskripsi tambahan.';
        document.getElementById('admModalStatus').innerText = activity.status;
        // baris ini untuk update link download ZIP dengan ID kegiatan yang sesuai
        document.getElementById('btnDownloadAll').href = `/activities/${activity.id}/download-all`;

        // 2. Involved Employees
        const involvedDiv = document.getElementById('admModalInvolved');
        involvedDiv.innerHTML = activity.involved_employees.length > 0
            ? activity.involved_employees.map(e => `<span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-[10px] font-bold border border-blue-200">${e.name}</span>`).join('')
            : '<span class="text-xs text-gray-400 italic">Terbuka Umum</span>';

        // 3. Stats
        const statsList = document.getElementById('admStats');
        const counts = {};
        activity.reports.forEach(r => counts[r.type] = (counts[r.type] || 0) + 1);
        let statsHtml = '';
        for (const [type, count] of Object.entries(counts)) statsHtml += `<li class="flex justify-between"><span>${type}:</span> <span class="font-bold">${count}</span></li>`;
        statsList.innerHTML = statsHtml || '<li>Belum ada berkas.</li>';

        // 4. Configure Forms Action
        document.getElementById('formDeleteActivity').action = '/activities/' + activity.id;

        const btnEdit = document.getElementById('btnEditActivity');
        btnEdit.onclick = function() {
            document.getElementById('formEditActivity').action = '/activities/' + activity.id;
            document.getElementById('editTitle').value = activity.title;
            document.getElementById('editDesc').value = activity.description;
            document.getElementById('editStartDate').value = activity.start_date;
            document.getElementById('editEndDate').value = activity.end_date;
            document.getElementById('editStatus').value = activity.status;

            // Mark involved employees in checkboxes
            const involvedIds = activity.involved_employees.map(e => e.id);
            document.querySelectorAll('.edit-checkbox').forEach(cb => {
                cb.checked = involvedIds.includes(parseInt(cb.getAttribute('data-user-id')));
            });

            document.getElementById('modalEditActivity').classList.remove('hidden');
        };

        // 5. Populate Reports
        const listContainer = document.getElementById('admReportsList');
        document.getElementById('admReportCount').innerText = activity.reports.length + ' Berkas';
        listContainer.innerHTML = '';

        if (activity.reports.length === 0) {
            listContainer.innerHTML = `<div class="text-center py-20 border-2 border-dashed border-gray-100 rounded-lg text-gray-300 italic"><i class="fa-regular fa-folder-open text-4xl mb-2 block"></i> Belum ada laporan masuk.</div>`;
        } else {
            activity.reports.forEach(report => {
                let iconColor = 'bg-blue-100 text-blue-600';
                let icon = 'fa-file-lines';
                if(report.type === 'Keuangan') { iconColor = 'bg-green-100 text-green-600'; icon = 'fa-file-invoice-dollar'; }
                else if(report.type === 'Dokumentasi') { iconColor = 'bg-purple-100 text-purple-600'; icon = 'fa-images'; }

                const dateStr = new Date(report.created_at).toLocaleDateString('id-ID', {day:'numeric', month:'short'});

                const html = `
                    <div class="flex items-center justify-between p-3 border border-gray-100 rounded-lg hover:bg-blue-50 transition group">
                        <div class="flex items-center gap-3 overflow-hidden">
                            <div class="h-9 w-9 rounded-lg ${iconColor} flex items-center justify-center flex-shrink-0"><i class="fa-solid ${icon}"></i></div>
                            <div class="min-w-0">
                                <h5 class="font-bold text-gray-800 text-xs truncate uppercase tracking-tight">${report.title}</h5>
                                <p class="text-[10px] text-gray-500 font-medium">${report.user.name} • ${dateStr} • ${report.type}</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="showReportDetail('${report.title}', '${report.type}', '${report.user.name}', '${report.description || 'Tidak ada keterangan'}', '/reports/${report.id}/download')" class="h-8 w-8 flex items-center justify-center bg-gray-100 hover:bg-yellow-100 hover:text-yellow-600 text-gray-500 rounded transition shadow-sm"><i class="fa-solid fa-eye"></i></button>
                            <a href="/reports/${report.id}/download" class="h-8 w-8 flex items-center justify-center bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-600 rounded transition shadow-sm"><i class="fa-solid fa-download"></i></a>
                            <button onclick="confirmDeleteReport(${report.id})" class="h-8 w-8 flex items-center justify-center bg-red-50 hover:bg-red-600 hover:text-white text-red-600 rounded transition shadow-sm"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </div>`;
                listContainer.insertAdjacentHTML('beforeend', html);
            });
        }

        const modal = document.getElementById('adminModal');
        const content = document.getElementById('adminModalContent');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }, 10);
    }

    function closeAdminModal() {
        const modal = document.getElementById('adminModal');
        const content = document.getElementById('adminModalContent');
        modal.classList.add('opacity-0');
        content.classList.remove('scale-100');
        content.classList.add('scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    function openEditUserModal(user) {
        document.getElementById('formEditUser').action = '/users/' + user.id;
        document.getElementById('editUserName').value = user.name;
        document.getElementById('editUserUsername').value = user.username;
        document.getElementById('modalEditUser').classList.remove('hidden');
    }

    function showReportDetail(title, type, user, desc, downloadUrl) {
        Swal.fire({
            title: `<div class="text-lg font-bold text-blue-900">${title}</div>`,
            html: `
                <div class="text-left text-xs text-gray-600 p-2 border-t mt-4">
                    <div class="flex justify-between mb-2"><span>Jenis:</span> <span class="font-bold">${type}</span></div>
                    <div class="flex justify-between mb-2"><span>Pengirim:</span> <span class="font-bold">${user}</span></div>
                    <div class="mt-4 font-bold uppercase text-gray-400">Keterangan:</div>
                    <div class="p-3 bg-gray-50 border rounded mt-1 italic">${desc}</div>
                    <a href="${downloadUrl}" class="flex items-center justify-center w-full gap-2 mt-6 py-2.5 bg-blue-900 text-white rounded-lg font-bold shadow-md hover:bg-blue-800 transition">
                        <i class="fa-solid fa-download"></i> Unduh File Laporan
                    </a>
                </div>
            `,
            showConfirmButton: false,
            showCloseButton: true
        });
    }

    function confirmDeleteActivity(button) {
        Swal.fire({
            title: 'Hapus Kegiatan?',
            text: "Seluruh berkas laporan di dalamnya akan ikut terhapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus Semua!'
        }).then((res) => { if(res.isConfirmed) button.closest('form').submit(); });
    }

    function confirmDeleteReport(id) {
        Swal.fire({
            title: 'Hapus Berkas?',
            text: "Tindakan ini tidak bisa dibatalkan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!'
        }).then((res) => {
            if(res.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST'; form.action = '/reports/' + id;
                form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">`;
                document.body.appendChild(form); form.submit();
            }
        });
    }

    function confirmDeleteUser(e, form) {
        e.preventDefault();
        Swal.fire({
            title: 'Hapus Pegawai?',
            text: "Akun akses akan dicabut dan seluruh laporan pegawai ini akan ikut terhapus!",
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Cabut Akses!'
        }).then((res) => { if(res.isConfirmed) form.submit(); });
    }

    // Modal click-outside logic
    window.onclick = function(e) {
        if(e.target.id === 'adminModal') closeAdminModal();
        if(e.target.id === 'modalNewActivity') e.target.classList.add('hidden');
        if(e.target.id === 'modalNewUser') e.target.classList.add('hidden');
        if(e.target.id === 'modalEditUser') e.target.classList.add('hidden');
        if(e.target.id === 'modalEditActivity') e.target.classList.add('hidden');
    }
</script>
@endsection
