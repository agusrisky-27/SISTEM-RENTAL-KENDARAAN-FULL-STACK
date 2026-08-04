<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    protected $fillable = [
        'nama', 'no_hp', 'no_identitas', 'alamat'
    ];

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'id_pelanggan');
    }
}
