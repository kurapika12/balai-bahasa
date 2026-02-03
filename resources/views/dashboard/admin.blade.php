@extends('layout')

@section('content')
<div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
    <div>
        <h2 class="text-2xl font-bold text-blue-900">Panel Administrasi</h2>
        <p class="text-gray-500 text-sm">Kelola kegiatan dan pantau arsip laporan.</p>
    </div>
    <div class="flex gap-2">
        <button onclick="document.getElementById('modalNewUser').classList.remove('hidden')" 
            class="bg-yellow-500 hover:bg-yellow-600 text-white px-5 py-2.5 rounded shadow-sm font-medium flex items-center gap-2 text-sm transition">
            <i class="fa-solid fa-user-plus"></i> Tambah Pegawai
        </button>
        <button onclick="document.getElementById('modalNewActivity').classList.remove('hidden')" 
            class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2.5 rounded shadow-sm font-medium flex items-center gap-2 text-sm transition">
            <i class="fa-solid fa-plus-circle"></i> Tambah Kegiatan
        </button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded shadow-sm border-l-4 border-blue-600 flex justify-between items-center">
        <div>
            <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Total Kegiatan</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $activities->count() }}</h3>
        </div>
        <div class="h-12 w-12 bg-blue-50 rounded-full flex items-center justify-center text-blue-600">
            <i class="fa-solid fa-calendar-check text-xl"></i>
        </div>
    </div>
    <div class="bg-white p-6 rounded shadow-sm border-l-4 border-green-600 flex justify-between items-center">
        <div>
            <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Total Laporan</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $totalReports }}</h3>
        </div>
        <div class="h-12 w-12 bg-green-50 rounded-full flex items-center justify-center text-green-600">
            <i class="fa-solid fa-box-archive text-xl"></i>
        </div>
    </div>
    <div class="bg-white p-6 rounded shadow-sm border-l-4 border-yellow-500 flex justify-between items-center">
        <div>
            <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Pegawai Aktif</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ \App\Models\User::where('role','pegawai')->count() }}</h3>
        </div>
        <div class="h-12 w-12 bg-yellow-50 rounded-full flex items-center justify-center text-yellow-600">
            <i class="fa-solid fa-users text-xl"></i>
        </div>
    </div>
</div>

<h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
    <i class="fa-solid fa-folder-open text-yellow-600"></i> Kelola Kegiatan
</h3>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($activities as $activity)
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden hover:shadow-lg transition duration-300 transform hover:-translate-y-1 flex flex-col h-full group">
        <div class="h-3 bg-blue-900 group-hover:bg-yellow-500 transition-colors duration-300"></div>
        
        <div class="p-6 flex-grow">
            <div class="flex items-center justify-between mb-3">
                 <span class="bg-blue-50 text-blue-700 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide border border-blue-100">
                    <i class="fa-regular fa-calendar mr-1"></i> {{ \Carbon\Carbon::parse($activity->date)->format('d M Y') }}
                </span>
                @php
                    $statusColor = 'bg-gray-100 text-gray-600';
                    if($activity->status == 'Sedang Berlangsung') $statusColor = 'bg-green-100 text-green-700 border-green-200';
                    if($activity->status == 'Akan Datang') $statusColor = 'bg-blue-100 text-blue-700 border-blue-200';
                    if($activity->status == 'Selesai') $statusColor = 'bg-red-100 text-red-500 border-red-200';
                @endphp
                <span class="text-[10px] font-bold px-2 py-1 rounded border {{ $statusColor }}">
                    {{ $activity->status }}
                </span>
            </div>
            
            <h3 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-blue-900 transition">{{ $activity->title }}</h3>
            <p class="text-gray-600 text-sm line-clamp-3">{{ $activity->description }}</p>
        </div>

        <div class="p-6 pt-0 mt-auto">
            <button onclick="openAdminModal({{ $activity->id }})" class="w-full bg-white border-2 border-blue-900 text-blue-900 hover:bg-blue-900 hover:text-white font-bold py-2 rounded-lg transition-all duration-300 flex justify-center items-center gap-2">
                <i class="fa-solid fa-gear"></i> Kelola & Lihat Laporan
            </button>
        </div>
    </div>
    @empty
    <div class="col-span-3 text-center py-12 bg-white rounded-xl border border-dashed border-gray-300">
        <i class="fa-solid fa-calendar-xmark text-4xl text-gray-300 mb-3"></i>
        <p class="text-gray-500">Belum ada agenda kegiatan.</p>
    </div>
    @endforelse
</div>

<div id="adminModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-center justify-center p-4 backdrop-blur-sm transition-opacity opacity-0">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl h-[85vh] overflow-hidden flex flex-col transform scale-95 transition-transform duration-300" id="adminModalContent">

        <div class="bg-blue-900 px-6 py-4 flex justify-between items-center flex-shrink-0">
            <div>
                <h3 class="font-bold text-white text-lg" id="admModalTitle">Judul Kegiatan</h3>
                <p class="text-blue-200 text-xs" id="admModalDate">Pelaksanaan: -</p>
            </div>
            <button onclick="closeAdminModal()" class="text-white hover:text-yellow-400 text-2xl transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="flex flex-col lg:flex-row flex-grow overflow-hidden">

            <div class="w-full lg:w-1/3 bg-gray-50 p-6 overflow-y-auto border-b lg:border-b-0 lg:border-r border-gray-200 flex flex-col justify-between">
                <div>
                    <h4 class="font-bold text-blue-900 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info"></i> Detail Kegiatan
                    </h4>
                    <div class="space-y-4 text-sm text-gray-600">
                        <div>
                            <span class="block font-bold text-gray-800 text-xs uppercase mb-1">Status:</span>
                            <span id="admModalStatus" class="inline-block px-2 py-1 rounded bg-gray-200 text-gray-700 text-xs font-bold uppercase">-</span>
                        </div>
                        <div>
                            <span class="block font-bold text-gray-800 text-xs uppercase mb-1">Deskripsi:</span>
                            <p id="admModalDesc" class="leading-relaxed">-</p>
                        </div>
                        
                        <div class="bg-blue-50 p-3 rounded border border-blue-100 mt-2">
                            <span class="block font-bold text-blue-800 text-xs mb-1">Statistik Laporan:</span>
                            <ul class="list-disc list-inside text-xs space-y-1 text-blue-900" id="admStats">
                                </ul>
                        </div>
                    </div>

                    <button id="btnEditActivity" class="w-full mt-6 bg-white border border-yellow-500 text-yellow-600 hover:bg-yellow-50 font-bold py-2 rounded shadow-sm transition flex justify-center items-center gap-2 text-sm">
                        <i class="fa-solid fa-pen-to-square"></i> Edit Data Kegiatan
                    </button>
                </div>

                <div class="mt-6 pt-6 border-t border-red-200">
                    <form id="formDeleteActivity" action="#" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confirmDeleteActivity(this)"
                            class="w-full bg-red-100 text-red-700 hover:bg-red-600 hover:text-white border border-red-300 font-bold py-2 rounded shadow-sm transition flex justify-center items-center gap-2 text-sm">
                            <i class="fa-solid fa-trash-can"></i> Hapus Kegiatan
                        </button>
                    </form>
                </div>
            </div>

            <div class="w-full lg:w-2/3 bg-white p-6 overflow-y-auto">
                <div class="flex justify-between items-center mb-4 border-b pb-2">
                    <h4 class="font-bold text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-folder-tree text-yellow-600"></i> Arsip Laporan Masuk
                    </h4>
                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded font-mono" id="admReportCount">0 File</span>
                </div>

                <div class="space-y-3" id="admReportsList">
                    <p class="text-center text-gray-400 py-10">Memuat data...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modalEditActivity" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-[60] flex items-center justify-center p-4 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-lg shadow-2xl max-w-md w-full overflow-hidden transform transition-all">
        <div class="bg-yellow-500 px-6 py-4 flex justify-between items-center">
            <h3 class="font-bold text-white text-lg">Edit Data Kegiatan</h3>
            <button onclick="document.getElementById('modalEditActivity').classList.add('hidden')" class="text-yellow-100 hover:text-white">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <form id="formEditActivity" method="POST" class="p-6">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama Kegiatan</label>
                    <input type="text" id="editTitle" name="title" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 border p-2.5" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi Singkat</label>
                    <textarea id="editDesc" name="description" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 border p-2.5" rows="3"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal</label>
                        <input type="date" id="editDate" name="date" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 border p-2.5">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Status</label>
                        <select id="editStatus" name="status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 border p-2.5">
                            <option value="Akan Datang">Akan Datang</option>
                            <option value="Sedang Berlangsung">Sedang Berlangsung</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mt-8 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modalEditActivity').classList.add('hidden')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-medium rounded transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-bold rounded shadow transition">Update Perubahan</button>
            </div>
        </form>
    </div>
</div>

<div id="modalNewActivity" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-lg shadow-2xl max-w-md w-full overflow-hidden transform transition-all">
        <div class="bg-blue-900 px-6 py-4 flex justify-between items-center">
            <h3 class="font-bold text-white text-lg">Buat Kegiatan Baru</h3>
            <button onclick="document.getElementById('modalNewActivity').classList.add('hidden')" class="text-blue-200 hover:text-white">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <form action="/activities" method="POST" class="p-6">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama Kegiatan</label>
                    <input type="text" name="title" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 border p-2.5" placeholder="Contoh: Bulan Bahasa 2024" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi Singkat</label>
                    <textarea name="description" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 border p-2.5" rows="3"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Pelaksanaan</label>
                        <input type="date" name="date" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 border p-2.5">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Status Kegiatan</label>
                        <select name="status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 border p-2.5">
                            <option value="Akan Datang">Akan Datang</option>
                            <option value="Sedang Berlangsung">Sedang Berlangsung</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mt-8 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modalNewActivity').classList.add('hidden')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-medium rounded transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-900 hover:bg-blue-800 text-white text-sm font-bold rounded shadow transition">Simpan Kegiatan</button>
            </div>
        </form>
    </div>
</div>

<div id="modalNewUser" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-lg shadow-2xl max-w-md w-full overflow-hidden transform transition-all">
        <div class="bg-yellow-500 px-6 py-4 flex justify-between items-center">
            <h3 class="font-bold text-white text-lg">Tambah Pegawai Baru</h3>
            <button onclick="document.getElementById('modalNewUser').classList.add('hidden')" class="text-yellow-100 hover:text-white">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <form action="/users" method="POST" class="p-6">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 border p-2.5" placeholder="Contoh: Andi Wijaya, S.Pd." required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Username</label>
                    <input type="text" name="username" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 border p-2.5" placeholder="Contoh: 198001012024011001" required>
                    <p class="text-xs text-gray-400 mt-1">Digunakan untuk login ke sistem.</p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Password</label>
                    <input type="text" name="password" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 border p-2.5" placeholder="Minimal 6 karakter" required>
                </div>
            </div>
            <div class="mt-8 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modalNewUser').classList.add('hidden')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-medium rounded transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-bold rounded shadow transition">Simpan Pegawai</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Data JSON dari Laravel Controller
    const activitiesData = @json($activities);

    function openAdminModal(id) {
        const activity = activitiesData.find(a => a.id === id);
        if(!activity) return;

        // 1. Populate Data Header & Kiri
        document.getElementById('admModalTitle').innerText = activity.title;
        document.getElementById('admModalDate').innerText = 'Pelaksanaan: ' + (activity.date || '-');
        document.getElementById('admModalDesc').innerText = activity.description || 'Tidak ada deskripsi.';
        document.getElementById('admModalStatus').innerText = activity.status;

        // 2. Populate Statistik
        const statsList = document.getElementById('admStats');
        const types = {};
        if (activity.reports) {
            activity.reports.forEach(r => types[r.type] = (types[r.type] || 0) + 1);
        }

        let statsHtml = '';
        if(Object.keys(types).length === 0) {
            statsHtml = '<li>Belum ada data laporan</li>';
        } else {
            for (const [type, count] of Object.entries(types)) {
                statsHtml += `<li>${type}: ${count} dokumen</li>`;
            }
        }
        statsList.innerHTML = statsHtml;

        // 3. Konfigurasi Tombol Edit & Hapus
        document.getElementById('formDeleteActivity').action = '/activities/' + activity.id;

        const btnEdit = document.getElementById('btnEditActivity');
        btnEdit.onclick = function() {
            document.getElementById('formEditActivity').action = '/activities/' + activity.id;
            document.getElementById('editTitle').value = activity.title;
            document.getElementById('editDesc').value = activity.description;
            document.getElementById('editDate').value = activity.date;
            document.getElementById('editStatus').value = activity.status;
            document.getElementById('modalEditActivity').classList.remove('hidden');
        };

        // 4. Populate Daftar Laporan (Kanan)
        const listContainer = document.getElementById('admReportsList');
        const reportCount = activity.reports ? activity.reports.length : 0;
        document.getElementById('admReportCount').innerText = reportCount + ' File';
        listContainer.innerHTML = '';

        if (reportCount === 0) {
            listContainer.innerHTML = `
                <div class="text-center py-12 border-2 border-dashed border-gray-100 rounded-lg">
                    <i class="fa-regular fa-folder-open text-3xl text-gray-300 mb-2"></i>
                    <p class="text-gray-400 text-sm">Folder ini masih kosong.</p>
                </div>`;
        } else {
            activity.reports.forEach(report => {
                let iconColor = 'bg-blue-100 text-blue-600';
                let icon = 'fa-file-lines';
                if(report.type === 'Keuangan') { iconColor = 'bg-green-100 text-green-600'; icon = 'fa-file-invoice-dollar'; }
                else if(report.type === 'Dokumentasi') { iconColor = 'bg-purple-100 text-purple-600'; icon = 'fa-images'; }

                const date = new Date(report.created_at);
                const dateStr = date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                const userName = report.user ? report.user.name : 'Unknown';
                
                // Escape string untuk mencegah error JS
                const safeDesc = report.description ? report.description.replace(/'/g, "\\'") : 'Tidak ada keterangan.';
                const safeTitle = report.title.replace(/'/g, "\\'");

                const html = `
                    <div class="flex items-center justify-between p-3 border border-gray-100 rounded-lg hover:bg-blue-50 transition group">
                        <div class="flex items-center gap-3 overflow-hidden">
                            <div class="h-10 w-10 rounded-lg ${iconColor} flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid ${icon}"></i>
                            </div>
                            <div class="min-w-0">
                                <h5 class="font-bold text-gray-800 text-sm group-hover:text-blue-800 truncate">${report.title}</h5>
                                <div class="flex items-center gap-2 text-xs text-gray-500 mt-0.5">
                                    <span class="font-semibold text-gray-600"><i class="fa-solid fa-user text-[10px] mr-1"></i>${userName}</span>
                                    <span>•</span>
                                    <span>${dateStr}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            
                            <button type="button" 
                                onclick="showReportDetail('${safeTitle}', '${report.type}', '${userName}', '${safeDesc}', '/reports/${report.id}/download')"
                                class="text-gray-500 bg-gray-100 hover:bg-yellow-100 hover:text-yellow-600 w-8 h-8 flex items-center justify-center rounded transition shadow-sm"
                                title="Lihat Detail & Keterangan">
                                <i class="fa-solid fa-eye"></i>
                            </button>

                            <a href="/reports/${report.id}/download" class="text-blue-600 bg-blue-50 hover:bg-blue-200 w-8 h-8 flex items-center justify-center rounded transition shadow-sm" title="Unduh">
                                <i class="fa-solid fa-download"></i>
                            </a>

                            <form id="delete-report-${report.id}" action="/reports/${report.id}" method="POST" class="inline">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="button" onclick="confirmDeleteReport(${report.id})"
                                    class="text-red-600 bg-red-50 hover:bg-red-200 w-8 h-8 flex items-center justify-center rounded transition shadow-sm"
                                    title="Hapus Laporan">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                `;
                listContainer.insertAdjacentHTML('beforeend', html);
            });
        }

        // Tampilkan Modal
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
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // --- FUNGSI POPUP DETAIL (View Mata) ---
    function showReportDetail(title, type, user, description, downloadUrl) {
        Swal.fire({
            title: title,
            html: `
                <div class="text-left mt-2">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="bg-gray-50 p-2 rounded border border-gray-100">
                            <span class="block text-xs font-bold text-gray-400 uppercase">Jenis Dokumen</span>
                            <span class="text-sm font-semibold text-blue-900">${type}</span>
                        </div>
                        <div class="bg-gray-50 p-2 rounded border border-gray-100">
                            <span class="block text-xs font-bold text-gray-400 uppercase">Diunggah Oleh</span>
                            <span class="text-sm font-semibold text-gray-800">${user}</span>
                        </div>
                    </div>
                    
                    <div class="mb-5">
                        <span class="block text-xs font-bold text-gray-400 uppercase mb-1">Keterangan / Deskripsi</span>
                        <div class="p-3 bg-yellow-50 border border-yellow-100 rounded text-sm text-gray-700 leading-relaxed max-h-40 overflow-y-auto">
                            ${description}
                        </div>
                    </div>

                    <a href="${downloadUrl}" class="flex items-center justify-center w-full gap-2 px-4 py-3 bg-blue-900 text-white font-bold rounded hover:bg-blue-800 transition shadow-md">
                        <i class="fa-solid fa-download"></i> Download Dokumen Asli
                    </a>
                </div>
            `,
            showConfirmButton: false,
            showCloseButton: true,
            width: '450px'
        });
    }

    // --- HELPER LAIN ---
    document.getElementById('adminModal').addEventListener('click', function(e) {
        if (e.target === this) closeAdminModal();
    });

    function confirmDeleteActivity(button) {
        Swal.fire({
            title: 'Hapus Kegiatan?',
            text: "Seluruh data laporan di dalamnya akan ikut terhapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) button.closest('form').submit();
        });
    }

    function confirmDeleteReport(id) {
        Swal.fire({
            title: 'Hapus Laporan?',
            text: "File tidak dapat dikembalikan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('delete-report-' + id).submit();
        });
    }
</script>
@endsection
