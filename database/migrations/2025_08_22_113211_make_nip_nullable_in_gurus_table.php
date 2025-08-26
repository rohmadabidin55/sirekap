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
        Schema::table('gurus', function (Blueprint $table) {
            // Ubah kolom nip agar boleh null dan tidak lagi unik
            $table->string('nip', 50)->nullable()->unique(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gurus', function (Blueprint $table) {
            // Kembalikan seperti semula jika migrasi di-rollback
            $table->string('nip', 50)->unique()->change();
        });
    }
};
