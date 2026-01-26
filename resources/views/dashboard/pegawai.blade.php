@extends('layout')

@section('content')
<!-- Welcome Banner -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8 flex items-center justify-between relative overflow-hidden">
    <div class="relative z-10">
        <h2 class="text-2xl font-bold text-blue-900">Selamat Datang, {{ Auth::user()->name }}</h2>
        <p class="text-gray-600 mt-1">Pilih kegiatan di bawah ini untuk melihat detail dan mengunggah laporan tugas Anda.</p>
    </div>
    <div class="hidden md:block relative z-10 text-right">
        <div class="text-sm font-bold text-gray-500 uppercase">Tanggal Hari Ini</div>
        <div class="text-xl font-bold text-blue-900"><i class="fa-regular fa-calendar-days mr-2 text-yellow-500"></i> {{ date('d F Y') }}</div>
    </div>
    <!-- Decor -->
    <div class="absolute right-0 top-0 h-full w-2 bg-yellow-500"></div>
</div>

<!-- Grid Kegiatan Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($activities as $activity)
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden hover:shadow-lg transition duration-300 transform hover:-translate-y-1 flex flex-col h-full group">
        <!-- Card Header -->
        <div class="h-3 bg-blue-900 group-hover:bg-yellow-500 transition-colors duration-300"></div>
        <div class="p-6 flex-grow">
            <div class="flex items-center justify-between mb-3">
                 <span class="bg-blue-50 text-blue-700 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide border border-blue-100">
                    <i class="fa-regular fa-calendar mr-1"></i> {{ \Carbon\Carbon::parse($activity->date)->format('d M Y') }}
                </span>

                <!-- Status Badge -->
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
            <button onclick="openModal({{ $activity->id }})" class="w-full bg-white border-2 border-blue-900 text-blue-900 hover:bg-blue-900 hover:text-white font-bold py-2 rounded-lg transition-all duration-300 flex justify-center items-center gap-2">
                <i class="fa-solid fa-folder-open"></i> Buka Kegiatan
            </button>
        </div>
    </div>
    @empty
    <div class="col-span-3 text-center py-12 bg-white rounded-xl border border-dashed border-gray-300">
        <i class="fa-solid fa-calendar-xmark text-4xl text-gray-300 mb-3"></i>
        <p class="text-gray-500">Belum ada agenda kegiatan yang tersedia.</p>
    </div>
    @endforelse
</div>

<!-- Modal Interactive -->
<div id="activityModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-center justify-center p-4 backdrop-blur-sm transition-opacity opacity-0">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl h-[85vh] overflow-hidden flex flex-col transform scale-95 transition-transform duration-300" id="modalContent">

        <!-- Modal Header -->
        <div class="bg-blue-900 px-6 py-4 flex justify-between items-center flex-shrink-0">
            <div>
                <h3 class="font-bold text-white text-lg" id="modalTitle">Judul Kegiatan</h3>
                <p class="text-blue-200 text-xs" id="modalDate">Tanggal</p>
            </div>
            <button onclick="closeModal()" class="text-white hover:text-yellow-400 text-2xl transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Modal Body (Split Layout) -->
        <div class="flex flex-col lg:flex-row flex-grow overflow-hidden">

            <!-- Kiri: Form Upload -->
            <div class="w-full lg:w-1/3 bg-gray-50 p-6 overflow-y-auto border-b lg:border-b-0 lg:border-r border-gray-200">
                <h4 class="font-bold text-blue-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-cloud-arrow-up"></i> Unggah Laporan Baru
                </h4>

                <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <!-- Hidden Activity ID -->
                    <input type="hidden" name="activity_id" id="formActivityId">

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Judul Laporan</label>
                        <input type="text" name="title" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm p-2 border" placeholder="Contoh: Nota Konsumsi..." required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Jenis Dokumen</label>
                        <select name="type" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm p-2 border">
                            <option value="Dokumentasi">Dokumentasi</option>
                            <option value="Keuangan">Keuangan</option>
                            <option value="Narasi">Narasi / Notula</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">File</label>
                        <input type="file" name="file" class="w-full text-sm text-gray-500 file:mr-2 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Keterangan</label>
                        <textarea name="description" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm p-2 border"></textarea>
                    </div>

                    <button class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-2 rounded shadow transition flex justify-center items-center gap-2">
                        <i class="fa-solid fa-paper-plane"></i> Kirim
                    </button>
                </form>
            </div>

            <!-- Kanan: Daftar Laporan -->
            <div class="w-full lg:w-2/3 bg-white p-6 overflow-y-auto">
                <div class="flex justify-between items-center mb-4 border-b pb-2">
                    <h4 class="font-bold text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-folder-tree text-yellow-600"></i> Arsip Laporan Masuk
                    </h4>
                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded" id="reportCount">0 File</span>
                </div>

                <div class="space-y-3" id="reportsList">
                    <!-- List will be populated by JS -->
                    <p class="text-center text-gray-400 py-10">Memuat data...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const activitiesData = @json($activities);

    function openModal(id) {
        const activity = activitiesData.find(a => a.id === id);
        if(!activity) return;

        document.getElementById('modalTitle').innerText = activity.title;
        document.getElementById('modalDate').innerText = 'Pelaksanaan: ' + (activity.date || '-');
        document.getElementById('formActivityId').value = activity.id;

        const listContainer = document.getElementById('reportsList');
        const reportCount = document.getElementById('reportCount');

        listContainer.innerHTML = '';
        reportCount.innerText = activity.reports.length + ' File';

        if (activity.reports.length === 0) {
            listContainer.innerHTML = `
                <div class="text-center py-12 border-2 border-dashed border-gray-100 rounded-lg">
                    <i class="fa-regular fa-folder-open text-3xl text-gray-300 mb-2"></i>
                    <p class="text-gray-400 text-sm">Belum ada laporan untuk kegiatan ini.</p>
                </div>`;
        } else {
            activity.reports.forEach(report => {
                let iconColor = 'bg-blue-100 text-blue-600';
                let icon = 'fa-file-lines';
                if(report.type === 'Keuangan') { iconColor = 'bg-green-100 text-green-600'; icon = 'fa-file-invoice-dollar'; }
                else if(report.type === 'Dokumentasi') { iconColor = 'bg-purple-100 text-purple-600'; icon = 'fa-images'; }

                const date = new Date(report.created_at);
                const dateStr = date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });

                const html = `
                    <div class="flex items-center justify-between p-3 border border-gray-100 rounded-lg hover:bg-blue-50 transition group">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-lg ${iconColor} flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid ${icon}"></i>
                            </div>
                            <div>
                                <h5 class="font-bold text-gray-800 text-sm group-hover:text-blue-800">${report.title}</h5>
                                <div class="flex items-center gap-2 text-xs text-gray-500 mt-0.5">
                                    <span class="font-semibold text-gray-600"><i class="fa-solid fa-user text-[10px] mr-1"></i>${report.user.name}</span>
                                    <span>•</span>
                                    <span>${dateStr}</span>
                                </div>
                            </div>
                        </div>
                        <a href="/reports/${report.id}/download" class="text-gray-400 hover:text-blue-600 hover:bg-white p-2 rounded-full transition shadow-sm border border-transparent hover:border-gray-200">
                            <i class="fa-solid fa-download"></i>
                        </a>
                    </div>
                `;
                listContainer.insertAdjacentHTML('beforeend', html);
            });
        }

        const modal = document.getElementById('activityModal');
        const content = document.getElementById('modalContent');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }, 10);
    }

    function closeModal() {
        const modal = document.getElementById('activityModal');
        const content = document.getElementById('modalContent');
        modal.classList.add('opacity-0');
        content.classList.remove('scale-100');
        content.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    document.getElementById('activityModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
</script>
@endsection
