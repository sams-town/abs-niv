<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApprovalBertingkatToLembursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lemburs', function (Blueprint $table) {
            $table->string('status_approval_1')->default('Pending')->after('status');
            $table->unsignedBigInteger('user_approval_1')->nullable()->after('status_approval_1');
            $table->foreign('user_approval_1')->references('id')->on('users');
            $table->string('catatan_approval_1')->nullable()->after('user_approval_1');
        });

        // Set existing records to 'Dilewati' for backward compatibility
        \DB::table('lemburs')->update(['status_approval_1' => 'Dilewati']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lemburs', function (Blueprint $table) {
            $table->dropForeign(['user_approval_1']);
            $table->dropColumn(['status_approval_1', 'user_approval_1', 'catatan_approval_1']);
        });
    }
}
