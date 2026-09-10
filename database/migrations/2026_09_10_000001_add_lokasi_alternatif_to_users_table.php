<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLokasiAlternatifToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('lokasi_alternatif_id')
                  ->nullable()
                  ->after('lokasi_id')
                  ->comment('Lokasi kedua untuk dosen yang mengajar di 2 gedung');

            $table->foreign('lokasi_alternatif_id')
                  ->references('id')
                  ->on('lokasis')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['lokasi_alternatif_id']);
            $table->dropColumn('lokasi_alternatif_id');
        });
    }
}
