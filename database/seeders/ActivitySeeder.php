<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        Activity::create([
            'title' => 'Penyuluhan Bahasa Indonesia',
            'description' => 'Kegiatan penyuluhan penggunaan bahasa di ruang publik bagi instansi pemerintah.',
            'start_date' => '2024-10-15',
            'end_date' => '2024-10-15',
            'status' => 'Selesai',
        ]);

        Activity::create([
            'title' => 'Festival Musikalisasi Puisi',
            'description' => 'Lomba tingkat SMA se-Provinsi Sulawesi Tenggara dalam rangka Bulan Bahasa.',
            'start_date' => '2024-11-18',
            'end_date' => '2024-11-20',
            'status' => 'Sedang Berlangsung',
        ]);

        Activity::create([
            'title' => 'Uji Kemahiran Berbahasa Indonesia (UKBI)',
            'description' => 'Pelaksanaan tes UKBI Adaptif Merdeka bagi kalangan profesional.',
            'start_date' => '2024-12-05',
            'end_date' => '2024-12-06',
            'status' => 'Akan Datang',
        ]);
    }
}