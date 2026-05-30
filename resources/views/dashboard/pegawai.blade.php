@extends('layout')

@section('content')
<!-- Welcome Banner -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8 flex flex-col md:flex-row items-center justify-between relative overflow-hidden">
    <div class="relative z-10">
        <h2 class="text-2xl font-bold text-blue-900 text-center md:text-left">Selamat Datang, {{ Auth::user()->name }}</h2>
        <p class="text-gray-600 mt-1 text-center md:text-left text-sm">Silakan pilih agenda kegiatan di bawah untuk melihat detail dan mengunggah laporan.</p>
    </div>
    <div class="hidden md:block relative z-10 text-right">
        <div class="text-sm font-bold text-gray-500 uppercase">Tanggal Hari Ini</div>
        <div class="text-xl font-bold text-blue-900">
            <i class="fa-regular fa-calendar-days mr-2 text-yellow-500"></i> {{ date('d F Y') }}
        </div>
    </div>
    <!-- Decor Official -->
    <div class="absolute right-0 top-0 h-full w-2 bg-yellow-500"></div>
</div>

<!-- Grid Kegiatan Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($activities as $activity)
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden hover:shadow-lg transition duration-300 transform hover:-translate-y-1 flex flex-col h-full group">
        <!-- Card Header Accent -->
        <div class="h-3 bg-blue-900 group-hover:bg-yellow-500 transition-colors duration-300"></div>
        
        <div class="p-6 flex-grow">
            <div class="flex items-center justify-between mb-4">
                 <!-- Date Range Badge -->
                 <span class="bg-blue-50 text-blue-700 text-[10px] font-bold px-2 py-1 rounded-full border border-blue-100">
                    <i class="fa-regular fa-calendar mr-1"></i> 
                    {{ $activity->start_date ? \Carbon\Carbon::parse($activity->start_date)->format('d M') : '?' }} - 
                    {{ $activity->end_date ? \Carbon\Carbon::parse($activity->end_date)->format('d M Y') : '?' }}
                </span>

                <!-- Status Badge -->
                @php
                    $statusColor = 'bg-gray-100 text-gray-600 border-gray-200';
                    if($activity->status == 'Sedang Berlangsung') $statusColor = 'bg-green-100 text-green-700 border-green-200';
                    if($activity->status == 'Akan Datang') $statusColor = 'bg-blue-100 text-blue-700 border-blue-200';
                    if($activity->status == 'Selesai') $statusColor = 'bg-gray-100 text-gray-500 border-gray-200';
                @endphp
                <span class="text-[10px] font-bold px-2 py-1 rounded border {{ $statusColor }}">
                    {{ $activity->status }}
                </span>
            </div>

            <h3 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-blue-900 transition">{{ $activity->title }}</h3>
            <p class="text-gray-600 text-sm line-clamp-3 leading-relaxed mb-4">{{ $activity->description }}</p>
            
            <!-- Involved Peek -->
            <div class="flex items-center gap-2 text-xs text-gray-400 font-medium">
                <i class="fa-solid fa-users"></i>
                <span>{{ $activity->involvedEmployees->count() }} Pegawai Terlibat</span>
            </div>
        </div>

        <div class="p-6 pt-0 mt-auto">
            <button onclick="openModal({{ $activity->id }})" class="w-full bg-white border-2 border-blue-900 text-blue-900 hover:bg-blue-900 hover:text-white font-bold py-2.5 rounded-lg transition-all duration-300 flex justify-center items-center gap-2 shadow-sm">
                <i class="fa-solid fa-folder-open"></i> Buka Kegiatan
            </button>
        </div>
    </div>
    @empty
    <div class="col-span-full text-center py-16 bg-white rounded-xl border-2 border-dashed border-gray-200 text-gray-400">Belum ada agenda kegiatan.</div>
    @endforelse
</div>

<!-- Modal Interactive Detail & Upload -->
<div id="activityModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-center justify-center p-4 backdrop-blur-sm transition-opacity opacity-0">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-6xl h-[85vh] overflow-hidden flex flex-col transform scale-95 transition-transform duration-300" id="modalContent">

        <!-- Modal Header -->
        <div class="bg-blue-900 px-6 py-5 flex justify-between items-center flex-shrink-0 border-b-4 border-yellow-500">
            <div>
                <h3 class="font-bold text-white text-xl uppercase tracking-tight" id="modalTitle">Judul Kegiatan</h3>
                <p class="text-blue-200 text-xs mt-1" id="modalDateRange"><i class="fa-regular fa-calendar mr-1"></i> Tanggal Mulai - Selesai</p>
            </div>
            <button onclick="closeModal()" class="text-white hover:text-yellow-400 text-2xl transition duration-200">
                <i class="fa-solid fa-circle-xmark"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="flex flex-col lg:flex-row flex-grow overflow-hidden">

            <!-- Kiri: Info & Form Unggah -->
            <div class="w-full lg:w-1/3 bg-gray-50 p-6 overflow-y-auto border-b lg:border-b-0 lg:border-r border-gray-200">
                
                <!-- Pegawai Terlibat Section -->
                <div class="mb-8">
                    <h4 class="font-bold text-gray-700 uppercase tracking-widest text-[10px] mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-user-tag text-blue-600"></i> Pegawai Terlibat Resmi:
                    </h4>
                    <div id="modalInvolvedList" class="flex flex-wrap gap-1">
                        <!-- Populated by JS -->
                    </div>
                </div>

                <hr class="mb-8 border-gray-200">

                <h4 class="font-bold text-blue-900 mb-4 flex items-center gap-2 text-sm">
                    <i class="fa-solid fa-cloud-arrow-up"></i> Unggah Laporan Baru
                </h4>

                <form id="uploadForm" action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="hidden" name="activity_id" id="formActivityId">

                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Judul Laporan</label>
                        <input type="text" name="title" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm p-2.5 border" placeholder="Contoh: Notula Rapat Hari-1..." required>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Jenis Dokumen</label>
                        <select name="type" id="reportType" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm p-2.5 border bg-white">
                            <option value="Narasi">Narasi / Notula</option>
                            <option value="Keuangan">Keuangan / Nota</option>
                            <option value="Dokumentasi">Dokumentasi / Foto</option>
                        </select>
                    </div>

                    <div class="bg-white p-4 rounded-lg border-2 border-dashed border-gray-200 hover:border-blue-400 transition cursor-pointer relative group">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2">Berkas Laporan</label>
                        <input type="file" id="fileInput" name="file" class="w-full text-xs text-gray-500 file:mr-2 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                        <p class="text-[9px] text-gray-400 mt-2 italic">* PDF, DOCX, JPG, XLSX (Maks. 10MB)</p>
                    </div>

                    <!-- FITUR EDITABLE TEXTAREA RINGKASAN AI -->
                    <div class="relative">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1 flex items-center justify-between">
                            <span>Ringkasan Eksekutif (AI)</span>
                            <span class="text-[8px] bg-blue-100 text-blue-700 px-1.5 rounded-full font-bold">Dapat Diedit</span>
                        </label>
                        <textarea name="executive_summary" id="executiveSummary" rows="4" class="w-full border-gray-300 rounded-md shadow-sm text-xs p-2.5 border bg-white focus:ring-blue-500 focus:border-blue-500 font-medium leading-relaxed" placeholder="Unggah berkas untuk membuat ringkasan eksekutif otomatis oleh AI..."></textarea>
                        
                        <!-- Overlay Loading Ringkasan -->
                        <div id="summaryLoading" class="hidden absolute inset-0 bg-gray-50 bg-opacity-90 flex flex-col items-center justify-center rounded-md border border-blue-200">
                            <span class="text-[10px] font-bold text-blue-900 flex items-center gap-2 animate-pulse mb-1">
                                <i class="fa-solid fa-robot animate-spin"></i> AI Sedang Membaca Dokumen...
                            </span>
                            <span class="text-[8px] text-gray-400">Sedang menyusun ringkasan formal...</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Keterangan Singkat (Opsional)</label>
                        <textarea name="description" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-xs p-2.5 border" placeholder="Catatan tambahan mengenai berkas ini..."></textarea>
                    </div>

                    <button type="submit" id="submitBtn" class="w-full bg-blue-800 hover:bg-blue-900 text-white font-bold py-3 rounded-lg shadow-lg transition transform active:scale-95 flex justify-center items-center gap-2 text-sm">
                        <i class="fa-solid fa-paper-plane"></i> KIRIM LAPORAN
                    </button>
                </form>
            </div>

            <!-- Kanan: Daftar Arsip Laporan -->
            <div class="w-full lg:w-2/3 bg-white p-6 overflow-y-auto">
                <div class="flex justify-between items-center mb-6 border-b pb-3">
                    <h4 class="font-bold text-gray-800 flex items-center gap-2 text-lg uppercase tracking-tight">
                        <i class="fa-solid fa-folder-tree text-yellow-600"></i> Berkas Arsip Kegiatan
                    </h4>
                    <span class="text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded-full font-bold font-mono" id="reportCount">0 Berkas</span>
                </div>

                <div class="space-y-3" id="reportsList">
                    <!-- Dinamis via JavaScript -->
                    <p class="text-center text-gray-400 py-10 italic">Memuat arsip laporan...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL KHUSUS: REVISI BERKAS -->
<div id="modalRevisi" class="hidden fixed inset-0 bg-gray-900 bg-opacity-80 z-[60] flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-2xl max-w-md w-full overflow-hidden">
        <div class="bg-red-600 px-6 py-4 flex justify-between items-center">
            <h3 class="font-bold text-white"><i class="fa-solid fa-triangle-exclamation"></i> Revisi Berkas Laporan</h3>
            <button type="button" onclick="document.getElementById('modalRevisi').classList.add('hidden')" class="text-white hover:text-red-200 transition">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <div class="bg-red-50 p-4 border-b border-red-100">
            <p class="text-xs font-bold text-red-800 uppercase mb-1">Catatan Admin:</p>
            <p id="revisiNote" class="text-sm text-red-900 italic font-medium">"..."</p>
        </div>
        <form id="formRevisi" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Unggah File Pengganti</label>
                <input type="file" id="revisiFileInput" name="file" class="w-full border border-gray-300 rounded p-2 text-xs file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:text-[10px] file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100" required>
                <p class="text-[10px] text-gray-400 mt-1">* File usang sebelumnya akan dihapus dari server.</p>
            </div>
            <div class="relative">
                <label class="block text-[10px] font-bold text-blue-900 uppercase mb-1">Ringkasan AI (Pembaruan)</label>
                <textarea name="executive_summary" id="revisiSummary" rows="4" class="w-full border-gray-300 rounded p-2 text-xs" placeholder="Tunggu proses baca AI setelah file dipilih..." required></textarea>
                
                <div id="revisiLoading" class="hidden absolute inset-0 bg-white bg-opacity-90 flex items-center justify-center rounded">
                    <span class="text-xs font-bold text-blue-900 animate-pulse"><i class="fa-solid fa-spinner animate-spin mr-2"></i>Memproses AI...</span>
                </div>
            </div>
            <button type="submit" id="btnSubmitRevisi" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition shadow-md flex items-center justify-center gap-2">
                <i class="fa-solid fa-upload"></i> Kirim Berkas Revisi
            </button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const activitiesData = @json($activities);
    const userId = {{ Auth::id() }};

    function getStatusBadge(status) {
        if(status === 'Approved') return `<span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-[10px] font-bold border border-green-200"><i class="fa-solid fa-shield-check mr-1"></i> Sah/Approved</span>`;
        if(status === 'Rejected') return `<span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-[10px] font-bold border border-red-200"><i class="fa-solid fa-xmark mr-1"></i> Butuh Revisi</span>`;
        if(status === 'Reviewed') return `<span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-[10px] font-bold border border-blue-200"><i class="fa-solid fa-glasses mr-1"></i> Diperiksa</span>`;
        return `<span class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full text-[10px] font-bold border border-yellow-200"><i class="fa-regular fa-clock mr-1"></i> Antrean</span>`;
    }

    function addslashes(str) { 
        return (str + '').replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0'); 
    }

    function openModal(id) {
        const activity = activitiesData.find(a => a.id === id);
        if(!activity) return;

        document.getElementById('modalTitle').innerText = activity.title;
        const start = activity.start_date ? new Date(activity.start_date).toLocaleDateString('id-ID', {day: 'numeric', month: 'long'}) : '?';
        const end = activity.end_date ? new Date(activity.end_date).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}) : '?';
        document.getElementById('modalDateRange').innerHTML = `<i class="fa-regular fa-calendar-check mr-1"></i> Pelaksanaan: ${start} s/d ${end}`;
        document.getElementById('formActivityId').value = activity.id;

        const involvedDiv = document.getElementById('modalInvolvedList');
        involvedDiv.innerHTML = activity.involved_employees && activity.involved_employees.length > 0 
            ? activity.involved_employees.map(e => `<span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-[10px] font-bold border border-blue-200"><i class="fa-solid fa-user-check text-[8px] mr-1"></i>${e.name}</span>`).join('')
            : '<span class="text-xs text-gray-400 italic">Terbuka bagi seluruh pegawai</span>';

        const listContainer = document.getElementById('reportsList');
        const reportCountSpan = document.getElementById('reportCount');
        const reports = activity.reports || [];
        reportCountSpan.innerText = reports.length + ' Berkas';
        listContainer.innerHTML = '';

        if (reports.length === 0) {
            listContainer.innerHTML = `<div class="text-center py-20 border-2 border-dashed border-gray-100 rounded-xl text-gray-300"><i class="fa-regular fa-folder-open text-5xl mb-3 block"></i><p class="text-sm font-medium">Belum ada laporan yang diunggah.</p></div>`;
        } else {
            reports.forEach(report => {
                let iconColor = 'bg-blue-100 text-blue-600';
                let icon = 'fa-file-lines';
                if(report.type === 'Keuangan') { iconColor = 'bg-green-100 text-green-600'; icon = 'fa-file-invoice-dollar'; }
                else if(report.type === 'Dokumentasi') { iconColor = 'bg-purple-100 text-purple-600'; icon = 'fa-images'; }
                const uploadDate = new Date(report.created_at).toLocaleDateString('id-ID', {day:'numeric', month:'short', year:'numeric'});
                const uploader = report.user ? report.user.name : 'Unknown';

                // Logika Approval Workflow Actions
                let actionHtml = '';
                const isMine = report.user_id === userId;

                if (report.status === 'Approved') {
                    actionHtml = `<span class="text-[10px] font-bold text-green-600 bg-green-50 px-2 py-1 border border-green-200 rounded shadow-sm"><i class="fa-solid fa-lock mr-1"></i> Terkunci</span>`;
                } else if (report.status === 'Rejected' && isMine) {
                    actionHtml = `<button type="button" onclick="openRevisiModal(${report.id}, '${addslashes(report.rejection_note || '')}')" class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-[10px] font-bold transition shadow-sm w-full"><i class="fa-solid fa-rotate mr-1"></i> REVISI</button>`;
                } else if (isMine) {
                    actionHtml = `<button type="button" onclick="confirmDelete(${report.id})" class="px-3 py-2 bg-gray-200 hover:bg-red-600 hover:text-white text-gray-600 rounded-md text-[10px] font-bold transition w-full"><i class="fa-solid fa-trash mr-1"></i> Tarik</button>`;
                }

                const rowClass = report.status === 'Rejected' ? 'border-red-300 bg-red-50 hover:bg-red-100' : 'border-gray-100 hover:bg-blue-50 bg-white';

                const html = `
                    <div class="p-4 border ${rowClass} rounded-xl transition group shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-start gap-4 overflow-hidden flex-grow">
                                <div class="h-10 w-10 rounded-lg ${iconColor} flex items-center justify-center flex-shrink-0 text-lg shadow-sm border border-white"><i class="fa-solid ${icon}"></i></div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h5 class="font-bold text-gray-800 text-sm uppercase tracking-tight truncate group-hover:text-blue-900">${report.title}</h5>
                                        ${getStatusBadge(report.status)}
                                    </div>
                                    <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-[10px] text-gray-500 mt-1">
                                        <span class="font-semibold text-gray-700 bg-gray-100 px-1.5 py-0.5 rounded">${uploader}</span>
                                        <span>•</span><span>${uploadDate}</span><span>•</span><span class="font-bold text-blue-600">${report.type}</span>
                                    </div>
                                    ${report.status === 'Rejected' ? `
                                        <div class="mt-3 p-2 bg-white border border-red-100 rounded text-xs text-red-700 font-medium">
                                            <i class="fa-solid fa-arrow-turn-down text-red-400 mr-1"></i> Catatan Admin: <span class="italic">"${report.rejection_note}"</span>
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                            <div class="flex flex-col items-end gap-2 flex-shrink-0 w-28">
                                <a href="/reports/${report.id}/download" class="w-full px-3 py-2 flex justify-center items-center bg-blue-50 text-blue-700 rounded-md hover:bg-blue-600 hover:text-white transition font-bold text-[10px]"><i class="fa-solid fa-download mr-1"></i> Unduh</a>
                                ${actionHtml}
                            </div>
                        </div>
                    </div>`;
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
            resetAiStatus('executiveSummary', 'summaryLoading', 'submitBtn');
        }, 300);
    }

    // Modal click-outside & escape key
    document.getElementById('activityModal').addEventListener('click', function(e) { if (e.target === this) closeModal(); });
    document.addEventListener('keydown', function(e) { if (e.key === "Escape") closeModal(); });

    function openRevisiModal(reportId, note) {
        document.getElementById('formRevisi').action = `/reports/${reportId}/revise`;
        document.getElementById('revisiNote').innerText = `"${note}"`;
        resetAiStatus('revisiSummary', 'revisiLoading', 'btnSubmitRevisi');
        document.getElementById('revisiFileInput').value = '';
        document.getElementById('modalRevisi').classList.remove('hidden');
    }

    function confirmDelete(id) {
        Swal.fire({
            title: 'Tarik Laporan?',
            text: "Laporan yang ditarik akan dihapus secara permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Tarik & Hapus'
        }).then((res) => {
            if(res.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST'; form.action = '/reports/' + id;
                form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">`;
                document.body.appendChild(form); form.submit();
            }
        });
    }

    // --- INTEGRASI PINTAR AUTOMATIC SUMMARY GENERATOR (REUSABLE) ---
    function resetAiStatus(textareaId, loadingId, btnId) {
        const textEl = document.getElementById(textareaId);
        const loadEl = document.getElementById(loadingId);
        const btnEl = document.getElementById(btnId);
        
        textEl.value = "";
        loadEl.classList.add('hidden');
        btnEl.disabled = false;
        btnEl.classList.remove('opacity-50', 'cursor-not-allowed');
    }

    function setupAiScanner(inputId, textareaId, loadingId, btnId) {
        document.getElementById(inputId).addEventListener('change', async function() {
            if (!this.files.length) return;
            
            const file = this.files[0];
            const textEl = document.getElementById(textareaId);
            const loadEl = document.getElementById(loadingId);
            const btnEl = document.getElementById(btnId);
            
            // Aktifkan visual loading
            loadEl.classList.remove('hidden');
            btnEl.disabled = true;
            btnEl.classList.add('opacity-50', 'cursor-not-allowed');
            textEl.value = "";
            textEl.placeholder = "Sistem sedang mengurai berkas, mohon tunggu...";

            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', '{{ csrf_token() }}');

            try {
                const response = await fetch('{{ route('reports.summarize') }}', {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) {
                    const rawResponseText = await response.text();
                    let errMsg = "Terjadi kesalahan respons server.";
                    try {
                        const errorObj = JSON.parse(rawResponseText);
                        errMsg = errorObj.message || errMsg;
                    } catch(parseErr) {
                        errMsg = rawResponseText.substring(0, 100) + "...";
                    }
                    throw new Error(errMsg);
                }

                const result = await response.json();

                if (result.success) {
                    textEl.value = result.summary;
                    Swal.fire({
                        icon: 'success',
                        title: 'Analisis AI Berhasil',
                        text: 'Draf Ringkasan Eksekutif telah berhasil disiapkan oleh AI. Silakan baca dan sesuaikan teks di atas sebelum dikirim.',
                        confirmButtonColor: '#1e3a8a'
                    });
                } else {
                    throw new Error(result.message || "Gagal menyusun ringkasan otomatis.");
                }
            } catch (error) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Verifikasi Manual',
                    html: `<div class="text-left text-xs space-y-2">
                            <p class="font-bold text-red-600">Catatan/Error Terdeteksi:</p>
                            <p class="bg-gray-100 p-2 rounded font-mono text-[10px] break-all">${error.message}</p>
                            <p class="text-gray-500">Jangan khawatir, Anda tetap bisa melapor dengan mengetik ringkasan secara manual di layar.</p>
                           </div>`,
                    confirmButtonColor: '#1e3a8a'
                });
                textEl.placeholder = "Tulis ringkasan eksekutif laporan Anda secara manual di sini...";
            } finally {
                // Nonaktifkan visual loading
                loadEl.classList.add('hidden');
                btnEl.disabled = false;
                btnEl.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        });
    }

    // Inisialisasi Scanner AI untuk Form Upload Baru dan Form Revisi
    setupAiScanner('fileInput', 'executiveSummary', 'summaryLoading', 'submitBtn');
    setupAiScanner('revisiFileInput', 'revisiSummary', 'revisiLoading', 'btnSubmitRevisi');

</script>

<style>
    .animate-fade-in {
        animation: fadeIn 0.4s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection