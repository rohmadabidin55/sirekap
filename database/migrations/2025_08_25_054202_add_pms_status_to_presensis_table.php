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
        Schema::table('presensis', function (Blueprint $table) {
            // Mengubah tipe kolom enum untuk menambahkan PMS
            // Catatan: Sintaks ini spesifik untuk MySQL
            DB::statement("ALTER TABLE presensis CHANGE COLUMN status status ENUM('Hadir', 'Sakit', 'Izin', 'Alpa', 'PMS') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('presensis', function (Blueprint $table) {
            // Mengembalikan ke kondisi semula jika migrasi di-rollback
            DB::statement("ALTER TABLE presensis CHANGE COLUMN status status ENUM('Hadir', 'Sakit', 'Izin', 'Alpa') NOT NULL");
        });
    }
};
