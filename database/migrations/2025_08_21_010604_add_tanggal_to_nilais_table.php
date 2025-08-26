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
        Schema::table('nilais', function (Blueprint $table) {
            // 1. Hapus foreign key yang terkait dengan kolom di unique index
            $table->dropForeign(['siswa_id']);
            $table->dropForeign(['mata_pelajaran_id']);

            // 2. Hapus unique constraint yang lama
            $table->dropUnique(['siswa_id', 'mata_pelajaran_id']);

            // 3. Tambahkan kolom baru
            $table->date('tanggal')->after('guru_id')->nullable();
            $table->string('jenis_nilai', 50)->after('tanggal')->default('Harian');

            // 4. Buat unique constraint baru yang lebih sesuai
            $table->unique(['siswa_id', 'mata_pelajaran_id', 'tanggal', 'jenis_nilai']);

            // 5. Tambahkan kembali foreign key
            $table->foreign('siswa_id')->references('id')->on('siswas')->onDelete('cascade');
            $table->foreign('mata_pelajaran_id')->references('id')->on('mata_pelajarans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nilais', function (Blueprint $table) {
            // Lakukan proses kebalikan dari method up()
            $table->dropForeign(['siswa_id']);
            $table->dropForeign(['mata_pelajaran_id']);
            
            $table->dropUnique(['siswa_id', 'mata_pelajaran_id', 'tanggal', 'jenis_nilai']);
            
            $table->dropColumn(['tanggal', 'jenis_nilai']);
            
            $table->unique(['siswa_id', 'mata_pelajaran_id']);

            $table->foreign('siswa_id')->references('id')->on('siswas')->onDelete('cascade');
            $table->foreign('mata_pelajaran_id')->references('id')->on('mata_pelajarans')->onDelete('cascade');
        });
    }
};
