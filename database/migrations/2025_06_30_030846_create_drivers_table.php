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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('last_paternal_name');
            $table->string('last_maternal_name');
            $table->integer('dni');
            $table->unsignedBigInteger('cargo_id');
            $table->string('file')->nullable();
            $table->boolean('status')->default(true);
            $table->foreign('cargo_id')->references('id')->on('cargos')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
