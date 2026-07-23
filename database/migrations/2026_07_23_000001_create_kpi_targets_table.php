<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kpi_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->year('year');
            $table->string('indicator_name');
            $table->decimal('target_value', 10, 2);
            $table->decimal('realization_value', 10, 2)->nullable();
            $table->decimal('weight', 5, 2); // weight in percentage, e.g., 20.00 for 20%
            $table->decimal('calculated_score', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kpi_targets');
    }
};
