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
            $table->integer('batas_terlambat')->default(5)->after('terlambat');
            $table->bigInteger('kasbon_obat')->default(0)->after('saldo_kasbon');
            $table->bigInteger('potongan_koperasi')->default(0)->after('potongan_bpjs_ketenagakerjaan');
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
                'batas_terlambat',
                'kasbon_obat',
                'potongan_koperasi'
            ]);
        });
    }
};
