<?php

namespace Database\Seeders;

use App\Models\MataKuliah;
use Illuminate\Database\Seeder;

class MataKuliahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $courses = [
            // Fakultas Kedokteran & Ilmu Kesehatan
            [
                'kode_mk' => 'MK-KED-01',
                'nama_mk' => 'Anatomi',
                'prodi' => 'Pendidikan Dokter',
                'fakultas' => 'Fakultas Kedokteran & Ilmu Kesehatan',
            ],
            [
                'kode_mk' => 'MK-KED-02',
                'nama_mk' => 'Fisiologi',
                'prodi' => 'Pendidikan Dokter',
                'fakultas' => 'Fakultas Kedokteran & Ilmu Kesehatan',
            ],
            [
                'kode_mk' => 'MK-KED-03',
                'nama_mk' => 'Farmakologi',
                'prodi' => 'Farmasi',
                'fakultas' => 'Fakultas Kedokteran & Ilmu Kesehatan',
            ],
            [
                'kode_mk' => 'MK-KED-04',
                'nama_mk' => 'Praktik Klinik',
                'prodi' => 'Ilmu Keperawatan',
                'fakultas' => 'Fakultas Kedokteran & Ilmu Kesehatan',
            ],

            // Fakultas Hukum
            [
                'kode_mk' => 'MK-HKM-01',
                'nama_mk' => 'Hukum Perdata',
                'prodi' => 'Ilmu Hukum',
                'fakultas' => 'Fakultas Hukum',
            ],
            [
                'kode_mk' => 'MK-HKM-02',
                'nama_mk' => 'Hukum Pidana',
                'prodi' => 'Ilmu Hukum',
                'fakultas' => 'Fakultas Hukum',
            ],
            [
                'kode_mk' => 'MK-HKM-03',
                'nama_mk' => 'Hukum Tata Negara',
                'prodi' => 'Ilmu Hukum',
                'fakultas' => 'Fakultas Hukum',
            ],
            [
                'kode_mk' => 'MK-HKM-04',
                'nama_mk' => 'Hukum Internasional',
                'prodi' => 'Ilmu Hukum',
                'fakultas' => 'Fakultas Hukum',
            ],

            // Fakultas Ekonomi
            [
                'kode_mk' => 'MK-EKN-01',
                'nama_mk' => 'Pengantar Bisnis',
                'prodi' => 'Manajemen',
                'fakultas' => 'Fakultas Ekonomi',
            ],
            [
                'kode_mk' => 'MK-EKN-02',
                'nama_mk' => 'Matematika Ekonomi',
                'prodi' => 'Manajemen',
                'fakultas' => 'Fakultas Ekonomi',
            ],
            [
                'kode_mk' => 'MK-EKN-03',
                'nama_mk' => 'Manajemen Keuangan',
                'prodi' => 'Akuntansi',
                'fakultas' => 'Fakultas Ekonomi',
            ],
            [
                'kode_mk' => 'MK-EKN-04',
                'nama_mk' => 'Perpajakan',
                'prodi' => 'Akuntansi',
                'fakultas' => 'Fakultas Ekonomi',
            ],

            // Fakultas Teknik & Sistem Informasi
            [
                'kode_mk' => 'MK-TKN-01',
                'nama_mk' => 'Pemrograman',
                'prodi' => 'Teknik Informatika',
                'fakultas' => 'Fakultas Teknik & Sistem Informasi',
            ],
            [
                'kode_mk' => 'MK-TKN-02',
                'nama_mk' => 'Jaringan Komputer',
                'prodi' => 'Sistem Informasi',
                'fakultas' => 'Fakultas Teknik & Sistem Informasi',
            ],
            [
                'kode_mk' => 'MK-TKN-03',
                'nama_mk' => 'Kalkulus',
                'prodi' => 'Teknik Sipil',
                'fakultas' => 'Fakultas Teknik & Sistem Informasi',
            ],
            [
                'kode_mk' => 'MK-TKN-04',
                'nama_mk' => 'Struktur Bangunan',
                'prodi' => 'Teknik Sipil',
                'fakultas' => 'Fakultas Teknik & Sistem Informasi',
            ],
        ];

        foreach ($courses as $course) {
            MataKuliah::updateOrCreate(
                ['kode_mk' => $course['kode_mk']],
                $course
            );
        }
    }
}
