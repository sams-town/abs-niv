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
        Schema::create('kpis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('kpi_categories')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('unit')->comment('Percentage, Nominal, Rating, etc.');
            $table->enum('type', ['Higher is Better', 'Lower is Better']);
            $table->decimal('target_value', 15, 2)->default(0);
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
        Schema::dropIfExists('kpis');
    }
};
