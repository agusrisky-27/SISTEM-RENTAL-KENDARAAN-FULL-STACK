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
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_transaksi')->constrained('transaksis')->onDelete('cascade');
            $table->date('tgl_bayar');
            $table->decimal('jumlah', 15, 2);
            $table->enum('metode', ['Transfer Bank', 'Cash', 'QRIS', 'Kartu Debit']);
            $table->enum('status', ['pending', 'lunas'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
