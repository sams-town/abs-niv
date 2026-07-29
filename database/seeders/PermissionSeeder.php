<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            // Pegawai
            'pegawai.view', 'pegawai.create', 'pegawai.edit', 'pegawai.delete',
            
            // Dosen
            'dosen.view', 'dosen.create', 'dosen.edit', 'dosen.delete',

            // Role
            'role.view', 'role.create', 'role.edit', 'role.delete',

            // Data Master
            'shift.view', 'shift.create', 'shift.edit', 'shift.delete',
            'lokasi.view', 'lokasi.create', 'lokasi.edit', 'lokasi.delete',
            'jabatan.view', 'jabatan.create', 'jabatan.edit', 'jabatan.delete',
            'golongan.view', 'golongan.create', 'golongan.edit', 'golongan.delete',
            'tunjangan.view', 'tunjangan.create', 'tunjangan.edit', 'tunjangan.delete',
            
            // Rekap
            'rekap.view',
            'laporan.view',

            // Akademik
            'jadwal.view', 'jadwal.create', 'jadwal.edit', 'jadwal.delete',
            
            // Absensi
            'absen.view', 'absen.create', 'absen.edit', 'absen.delete', 'absen.data',
            
            // Dinas Luar
            'dinas_luar.view', 'dinas_luar.create', 'dinas_luar.edit', 'dinas_luar.delete', 'dinas_luar.data',
            
            // Lembur
            'lembur.view', 'lembur.create', 'lembur.edit', 'lembur.delete', 'lembur.data',
            
            // Cuti
            'cuti.view', 'cuti.create', 'cuti.edit', 'cuti.delete', 'cuti.data',
            
            // Keuangan
            'payroll.view', 'payroll.create', 'payroll.edit', 'payroll.delete',
            'kasbon.view', 'kasbon.create', 'kasbon.edit', 'kasbon.delete',
            
            // Dokumen
            'dokumen.view', 'dokumen.create', 'dokumen.edit', 'dokumen.delete',
            
            // KPI
            'kpi.view', 'kpi.create', 'kpi.edit', 'kpi.delete', 'kpi.approve',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions($permissions);
    }
}
