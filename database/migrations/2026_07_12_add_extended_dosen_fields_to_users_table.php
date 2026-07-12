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
            $table->string('nip')->nullable()->after('nidn');
            $table->string('gelar_depan')->nullable()->after('nip');
            $table->string('gelar_belakang')->nullable()->after('gelar_depan');
            $table->string('program_studi')->nullable()->after('gelar_belakang');
            $table->string('pendidikan_terakhir')->nullable()->after('program_studi');
            $table->string('status_kepegawaian')->nullable()->after('pendidikan_terakhir');
            $table->string('tipe_honorarium')->nullable()->after('status_kepegawaian'); // Per Sesi / Per Token
            $table->decimal('nominal_honor', 15, 2)->nullable()->default(0)->after('tipe_honorarium');
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
                'nip',
                'gelar_depan',
                'gelar_belakang',
                'program_studi',
                'pendidikan_terakhir',
                'status_kepegawaian',
                'tipe_honorarium',
                'nominal_honor'
            ]);
        });
    }
};
