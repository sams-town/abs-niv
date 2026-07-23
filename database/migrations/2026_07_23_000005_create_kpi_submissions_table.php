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
        Schema::create('kpi_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kpi_assignment_id')->constrained('kpi_assignments')->onDelete('cascade');
            $table->foreignId('period_id')->constrained('kpi_periods')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->decimal('actual_value', 15, 2)->default(0);
            $table->decimal('score', 5, 2)->default(0)->comment('Calculated score based on actual vs target and weight');
            $table->text('notes')->nullable();
            $table->string('attachment')->nullable();
            $table->enum('status', ['Draft', 'Submitted', 'Approved', 'Rejected'])->default('Draft');
            $table->text('feedback')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
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
        Schema::dropIfExists('kpi_submissions');
    }
};
