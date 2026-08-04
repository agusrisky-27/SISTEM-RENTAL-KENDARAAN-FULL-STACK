<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    protected $fillable = [
        'nama', 'merk', 'jenis', 'plat_nomor', 'harga_sewa', 'status'
    ];

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'id_kendaraan');
    }
}
