<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('driver_mine_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('driver_id');
            $table->unsignedBigInteger('mine_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('month');
            $table->integer('year');
            $table->enum('status', ['Activo', 'Completedo', 'Cancelado'])->default('Activo');
            $table->text('notes')->nullable();
            // Índices para optimizar consultas
            $table->index(['driver_id', 'status']);
            $table->index(['mine_id', 'status']);
            $table->index(['year', 'month']);
            // Evitar asignaciones duplicadas para el mismo período
            $table->unique(['driver_id', 'year', 'month'], 'unique_driver_month_assignment');

            $table->foreign('driver_id')->references('id')->on('drivers')->cascadeOnDelete();
            $table->foreign('mine_id')->references('id')->on('mines')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_mine_assignments');
    }
};
