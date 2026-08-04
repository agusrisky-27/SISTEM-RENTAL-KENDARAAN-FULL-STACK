<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $fillable = [
        'id_pelanggan', 'id_kendaraan', 'tgl_sewa', 'tgl_kembali', 'lama_sewa', 'total_harga', 'status'
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan');
    }

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'id_kendaraan');
    }

    public function pengembalian()
    {
        return $this->hasOne(Pengembalian::class, 'id_transaksi');
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'id_transaksi');
    }
}
