<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('laporan_mengajars', function (Blueprint $table) {
            $table->string('token_input')->nullable()->after('sesi_daring_id');
        });
    }
    public function down() {
        Schema::table('laporan_mengajars', function (Blueprint $table) {
            $table->dropColumn('token_input');
        });
    }
};
