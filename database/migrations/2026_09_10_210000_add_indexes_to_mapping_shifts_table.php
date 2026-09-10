<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $tableExists = Schema::hasTable('mapping_shifts');
        if (!$tableExists) return;

        $col1 = Schema::hasColumn('mapping_shifts', 'user_id');
        $col2 = Schema::hasColumn('mapping_shifts', 'tanggal');
        if (!$col1 || !$col2) return;

        try {
            $rows = DB::table('mapping_shifts')
                ->select(['user_id', 'tanggal', DB::raw('COUNT(*) as c'), DB::raw('MAX(id) as keep_id')])
                ->groupBy(['user_id', 'tanggal'])
                ->havingRaw('COUNT(*) > 1')
                ->get();
            $toDelete = [];
            foreach ($rows as $dup) {
                $subIds = DB::table('mapping_shifts')
                    ->where('user_id', $dup->user_id)
                    ->where('tanggal', $dup->tanggal)
                    ->where('id', '!=', $dup->keep_id)
                    ->pluck('id')
                    ->all();
                $toDelete = array_merge($toDelete, $subIds);
            }
            if (!empty($toDelete)) {
                DB::table('mapping_shifts')->whereIn('id', array_unique($toDelete))->delete();
            }
        } catch (\Throwable $e) { }

        try { Schema::table('mapping_shifts', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $idx = $sm->listTableIndexes('mapping_shifts');
            $names = array_map('strtolower', array_keys($idx));

            if (!in_array('mapping_shifts_shift_id_tanggal_index', $names)) {
                try { $table->index(['shift_id', 'tanggal']); } catch (\Throwable $e) {}
            }
            if (!in_array('mapping_shifts_user_id_tanggal_unique', $names)) {
                try { $table->unique(['user_id', 'tanggal']); } catch (\Throwable $e) {}
            }
        }); } catch (\Throwable $e) { }
    }

    public function down()
    {
        try { Schema::table('mapping_shifts', function (Blueprint $table) {
            try { $table->dropUnique(['user_id', 'tanggal']); } catch (\Throwable $e) {}
            try { $table->dropIndex(['shift_id', 'tanggal']); } catch (\Throwable $e) {}
        }); } catch (\Throwable $e) { }
    }
};
