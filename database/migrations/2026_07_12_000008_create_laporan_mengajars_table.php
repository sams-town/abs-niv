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
        Schema::create('laporan_mengajars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('sesi_daring_id')->nullable()->constrained('sesi_darings')->onDelete('set null');
            $table->integer('durasi_menit')->default(0);
            $table->decimal('total_gaji', 15, 2)->default(0);
            $table->string('status_pembayaran')->default('pending'); // valid, invalid, pending
            $table->text('catatan_sistem')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('laporan_mengajars');
    }
};
