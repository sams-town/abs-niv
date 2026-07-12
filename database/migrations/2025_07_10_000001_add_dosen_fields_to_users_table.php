<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDosenFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('tipe_user')->default('pegawai')->after('is_admin');
            $table->string('nidn')->nullable()->after('tipe_user');
            $table->string('jabatan_akademik')->nullable()->after('nidn');
            $table->string('mata_kuliah')->nullable()->after('jabatan_akademik');
            $table->boolean('status_aktif')->default(true)->after('mata_kuliah');
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
                'tipe_user',
                'nidn',
                'jabatan_akademik',
                'mata_kuliah',
                'status_aktif',
            ]);
        });
    }
}
