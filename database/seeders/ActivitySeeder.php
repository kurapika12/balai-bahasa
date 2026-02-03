<?php

namespace Database\Seeders; // Pastikan namespace ini ada
use Illuminate\Database\Seeder;
use App\Models\Activity;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        Activity::create([
            'title' => 'Penyuluhan Bahasa Indonesia',
            'description' => 'Kegiatan penyuluhan penggunaan bahasa di ruang publik bagi instansi pemerintah.',
            'date' => '2024-10-15',
            'status' => 'Selesai',
        ]);

        Activity::create([
            'title' => 'Festival Musikalisasi Puisi',
            'description' => 'Lomba tingkat SMA se-Provinsi Sulawesi Tenggara dalam rangka Bulan Bahasa.',
            'date' => '2024-11-20',
            'status' => 'Sedang Berlangsung',
        ]);

        Activity::create([
            'title' => 'Uji Kemahiran Berbahasa Indonesia (UKBI)',
            'description' => 'Pelaksanaan tes UKBI Adaptif Merdeka bagi kalangan profesional.',
            'date' => '2024-12-05',
            'status' => 'Akan Datang',
        ]);
    }
}
