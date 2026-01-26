@extends('layout')

@section('content')
<div class="min-h-[70vh] flex flex-col justify-center items-center">
    <!-- Login Card -->
    <div class="w-full max-w-lg bg-white rounded-lg shadow-2xl overflow-hidden">
        <!-- Header Strip -->
        <div class="bg-blue-900 p-8 text-center relative overflow-hidden">
            <!-- Decorative Circle -->
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-yellow-500 rounded-full opacity-20"></div>
            <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-yellow-500 rounded-full opacity-20"></div>

            <div class="relative z-10 flex flex-col items-center">
                <div class="h-20 w-20 rounded-full overflow-hidden shadow-md border-2 border-yellow-400">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo Balai Bahasa" class="h-full w-full object-cover">
                </div>
                <h2 class="text-2xl font-bold text-white tracking-wide">PORTAL MASUK</h2>
                <p class="text-blue-200 text-sm mt-1 uppercase tracking-wider font-semibold">Sistem Pelaporan Kegiatan Pegawai</p>
            </div>
        </div>

        <!-- Form -->
        <div class="p-8">
            <form action="/login" method="POST" class="space-y-5">
                @csrf
                @if($errors->any())
                    <div class="bg-red-50 text-red-600 p-3 rounded text-sm text-center border border-red-200">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-user text-gray-400"></i>
                        </div>
                        <input type="text" name="username" class="pl-10 block w-full border-gray-300 rounded-lg border focus:ring-blue-500 focus:border-blue-500 p-2.5 bg-gray-50" placeholder="Username" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" name="password" class="pl-10 block w-full border-gray-300 rounded-lg border focus:ring-blue-500 focus:border-blue-500 p-2.5 bg-gray-50" placeholder="••••••••" required>
                    </div>
                </div>

                <button class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 rounded-lg shadow transition transform hover:scale-[1.02] duration-200">
                    Masuk Aplikasi
                </button>
            </form>

            <div class="mt-6 text-center text-xs text-gray-400">
                <p>Masalah saat login? Hubungi Bagian TU.</p>
            </div>
        </div>
    </div>
</div>
@endsection
