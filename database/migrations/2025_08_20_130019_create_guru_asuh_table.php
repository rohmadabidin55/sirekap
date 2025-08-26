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
    Schema::create('guru_asuh', function (Blueprint $table) {
        $table->id();
        $table->foreignId('guru_id')->constrained('gurus')->onDelete('cascade')->unique();
        $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade')->unique();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guru_asuh');
    }
};
