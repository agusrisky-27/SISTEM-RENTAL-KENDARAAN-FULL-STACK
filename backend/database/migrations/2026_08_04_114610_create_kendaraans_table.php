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
        Schema::create('kendaraans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('merk');
            $table->enum('jenis', ['Motor', 'Mobil', 'SUV', 'Van', 'Truck']);
            $table->string('plat_nomor')->unique();
            $table->decimal('harga_sewa', 12, 2);
            $table->enum('status', ['tersedia', 'disewa', 'servis'])->default('tersedia');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kendaraans');
    }
};
