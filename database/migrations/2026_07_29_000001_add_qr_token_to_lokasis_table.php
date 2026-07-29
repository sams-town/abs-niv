<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQrTokenToLokasisTable extends Migration
{
    public function up()
    {
        Schema::table('lokasis', function (Blueprint $table) {
            $table->string('qr_token')->nullable()->unique()->after('keterangan');
        });
    }

    public function down()
    {
        Schema::table('lokasis', function (Blueprint $table) {
            $table->dropColumn('qr_token');
        });
    }
}
