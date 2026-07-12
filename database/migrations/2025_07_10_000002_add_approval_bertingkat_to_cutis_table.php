<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddApprovalBertingkatToCutisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cutis', function (Blueprint $table) {
            $table->string('status_approval_1')->default('Pending')->after('status_cuti');
            // nilai: 'Pending' | 'Disetujui' | 'Ditolak' | 'Dilewati'
            $table->unsignedBigInteger('user_approval_1')->nullable()->after('status_approval_1');
            $table->foreign('user_approval_1')->references('id')->on('users');
            $table->string('catatan_approval_1')->nullable()->after('user_approval_1');
        });

        // Backward compatibility: data lama di-set 'Dilewati' agar Level 2 approval
        // tetap bisa dijalankan tanpa memerlukan ulang approval Level 1 (Req 13.2)
        DB::table('cutis')->update(['status_approval_1' => 'Dilewati']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cutis', function (Blueprint $table) {
            $table->dropForeign(['user_approval_1']);
            $table->dropColumn(['status_approval_1', 'user_approval_1', 'catatan_approval_1']);
        });
    }
}
