<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->unsignedInteger('toleransi')->default(0)->after('jam_selesai_istirahat')->comment('Toleransi keterlambatan dalam MENIT (default 0 = tanpa toleransi)');
        });
    }

    public function down()
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn('toleransi');
        });
    }
};
