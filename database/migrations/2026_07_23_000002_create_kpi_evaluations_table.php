<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kpi_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->year('year');
            $table->decimal('discipline_score', 5, 2)->nullable(); // 0-100
            $table->decimal('initiative_score', 5, 2)->nullable(); // 0-100
            $table->text('hr_notes')->nullable();
            $table->decimal('final_score', 10, 2)->nullable();
            $table->string('grade', 2)->nullable(); // A, B, C, D
            $table->string('status')->default('draft'); // draft, submitted, approved
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kpi_evaluations');
    }
};
