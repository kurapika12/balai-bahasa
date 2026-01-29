<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @auth
            @if(Auth::user()->role === 'admin')
                Dashboard Admin - Balai Bahasa Sultra
            @elseif(Auth::user()->role === 'pegawai')
                Dashboard Pegawai - Balai Bahasa Sultra
            @else
                Login - Balai Bahasa Sultra
            @endif
        @else
            Login - Balai Bahasa Sultra
        @endauth
    </title>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

   <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="icon" href="{{ asset('images/logo.png') }}" type="images/png">


    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-official-dark { background-color: #1e3a8a; }
        .bg-official-accent { background-color: #fbbf24; }
    </style>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
{{-- karya mahasiswa magang uho 2026 balai bahasa dari ilmu komputer --}}
    <!-- Navbar Official Style -->
    <nav class="bg-white shadow-lg border-b-4 border-yellow-500 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-24">
                <!-- Logo Area -->
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center gap-4">
                        <!-- Logo Placeholder -->
                        <div class="h-14 w-14 rounded-full overflow-hidden shadow-md border-2 border-yellow-400">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo Balai Bahasa" class="h-full w-full object-cover">
                        </div>
                        <div class="flex flex-col justify-center">

                            <span class="text-xl font-extrabold text-blue-900 leading-tight">BALAI BAHASA</span>

                            <!-- Hidden on Mobile -->
                            <span class="text-sm font-semibold text-gray-600 leading-tight">Provinsi Sulawesi Tenggara</span>

                            <!-- Hidden on Mobile to save space -->
                            <span class="hidden md:block mt-2.5 text-[11px] font-bold text-gray-500 uppercase tracking-widest leading-tight">Kementerian Pendidikan, Kebudayaan, Riset, dan Teknologi</span>
                        </div>
                    </div>
                </div>

                <!-- User Menu -->
                @auth
                <div class="flex items-center gap-6">
                    <div class="hidden md:flex flex-col text-right">
                        <span class="text-sm font-bold text-gray-800">{{ Auth::user()->name }}</span>
                        <div class="flex items-center justify-end gap-2">
                             <div class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></div>
                             <span class="text-xs text-blue-600 font-semibold uppercase tracking-wide">{{ Auth::user()->role == 'admin' ? 'Administrator' : 'Pegawai ASN' }}</span>
                        </div>
                    </div>
                    <!-- Separator hidden on mobile -->
                    <div class="h-10 w-[1px] bg-gray-200 hidden md:block"></div>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="group flex items-center gap-2 text-gray-500 hover:text-red-600 transition-colors duration-200">
                            <div class="bg-gray-100 group-hover:bg-red-50 p-2 rounded-lg transition">
                                <i class="fa-solid fa-right-from-bracket text-lg"></i>
                            </div>
                        </button>
                    </form>
                </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb / Title Area -->
        <div class="mb-6 pb-4 border-b border-gray-200 flex items-center gap-2 text-sm text-gray-500">
            <i class="fa-solid fa-house"></i> / Sistem Pelaporan
        </div>

        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-600 text-green-800 p-4 mb-6 rounded-r shadow-sm flex items-start gap-3" role="alert">
                <i class="fa-solid fa-circle-check mt-1"></i>
                <div>
                    <p class="font-bold">Berhasil</p>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-blue-900 text-white pt-10 pb-6 border-t-4 border-yellow-500 mt-auto">
        <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            <div>
                <h4 class="font-bold text-lg mb-4 text-yellow-400">Balai Bahasa Sultra</h4>
                <p class="text-sm text-blue-100 leading-relaxed">
                    Unit Pelaksana Teknis Kementerian Pendidikan, Kebudayaan, Riset, dan Teknologi yang bertugas melaksanakan pelindungan dan pemasyarakatan bahasa dan sastra Indonesia di Sulawesi Tenggara.
                </p>
            </div>
            <div>
                <h4 class="font-bold text-lg mb-4 text-yellow-400">Kontak Kami</h4>
                <ul class="text-sm text-blue-100 space-y-2">
                    <li><i class="fa-solid fa-location-dot w-6 text-center"></i> Jalan Haluoleo, Kompleks Bumi Praja, Anduonohu, Kendari, Sulawesi Tenggara</li>
                    <li><i class="fa-solid fa-phone w-6 text-center"></i> +6281342520567</li>
                    <li><i class="fa-solid fa-envelope w-6 text-center"></i> balaibahasasultra@kemdikbud.go.id</li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-lg mb-4 text-yellow-400">Tautan Cepat</h4>
                <ul class="text-sm text-blue-100 space-y-2">
                <li>
                    <a href="https://balaibahasasultra.kemendikdasmen.go.id/"
                    class="hover:text-white hover:underline"
                    target="_blank"
                    rel="noopener noreferrer">
                    Portal Balai Bahasa Provinsi SULTRA
                    </a>
                </li>
                    {{-- <li><a href="#" class="hover:text-white hover:underline">Peta Bahasa</a></li>
                    <li><a href="#" class="hover:text-white hover:underline">KBBI Daring</a></li> --}}
                </ul>
            </div>
        </div>
        <div class="text-center border-t border-blue-800 pt-6 text-xs text-blue-200">
            &copy; {{ date('Y') }} Sistem Informasi Pelaporan Kegiatan. KKP BALAI BAHASA 2026
        </div>
    </footer>

</body>
</html>
