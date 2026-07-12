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
        Schema::table('settings', function (Blueprint $table) {
            $table->string('template_cuti')->nullable()->after('logo');
            $table->string('template_lembur')->nullable()->after('template_cuti');
            $table->string('template_slip_gaji')->nullable()->after('template_lembur');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['template_cuti', 'template_lembur', 'template_slip_gaji']);
        });
    }
};
