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
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pelanggan')->constrained('pelanggans')->onDelete('cascade');
            $table->foreignId('id_kendaraan')->constrained('kendaraans')->onDelete('cascade');
            $table->date('tgl_sewa');
            $table->date('tgl_kembali');
            $table->integer('lama_sewa');
            $table->decimal('total_harga', 15, 2);
            $table->enum('status', ['aktif', 'selesai', 'terlambat'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
