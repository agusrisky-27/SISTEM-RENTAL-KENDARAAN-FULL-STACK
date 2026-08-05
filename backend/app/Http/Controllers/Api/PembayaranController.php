<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pembayaran;

class PembayaranController extends Controller
{
    public function index()
    {
        $pembayarans = Pembayaran::with('transaksi.pelanggan')->get();
        
        $mapped = $pembayarans->map(function ($p) {
            return [
                'id_pembayaran' => $p->id,
                'id_transaksi' => $p->id_transaksi,
                'tanggal_bayar' => $p->tgl_bayar,
                'jumlah' => $p->jumlah,
                'metode_pembayaran' => $p->metode,
                'transaksi' => $p->transaksi
            ];
        });

        return response()->json(['status' => true, 'data' => $mapped]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_transaksi' => 'required|exists:transaksis,id',
            'tanggal_bayar' => 'required|date',
            'jumlah' => 'required|numeric',
            'metode_pembayaran' => 'required|string'
        ]);

        try {
            $pembayaran = Pembayaran::create([
                'id_transaksi' => $request->id_transaksi,
                'tgl_bayar' => $request->tanggal_bayar,
                'jumlah' => $request->jumlah,
                'metode' => $request->metode_pembayaran,
                'status' => 'Lunas'
            ]);

            return response()->json(['status' => true, 'message' => 'Pembayaran berhasil disimpan']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $pembayaran = Pembayaran::find($id);
        if ($pembayaran) {
            $pembayaran->delete();
            return response()->json(['status' => true]);
        }
        return response()->json(['status' => false], 404);
    }
}
