<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nama_ibu_kandung')->nullable()->after('alamat');
            $table->string('kontak_darurat_nama')->nullable()->after('nama_ibu_kandung');
            $table->string('kontak_darurat_hp')->nullable()->after('kontak_darurat_nama');
            $table->string('kontak_darurat_hubungan')->nullable()->after('kontak_darurat_hp');
            $table->text('alamat_domisili')->nullable()->after('alamat');
            $table->bigInteger('cuti_melahirkan')->default(90)->after('izin_pulang_cepat');
            $table->bigInteger('cuti_kematian')->default(3)->after('cuti_melahirkan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'nama_ibu_kandung',
                'kontak_darurat_nama',
                'kontak_darurat_hp',
                'kontak_darurat_hubungan',
                'alamat_domisili',
                'cuti_melahirkan',
                'cuti_kematian'
            ]);
        });
    }
};
