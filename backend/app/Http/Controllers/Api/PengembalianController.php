<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengembalian;
use App\Models\Transaksi;
use App\Models\Kendaraan;
use Illuminate\Support\Facades\DB;

class PengembalianController extends Controller
{
    public function index()
    {
        $pengembalians = Pengembalian::with('transaksi.kendaraan', 'transaksi.pelanggan')->get();
        // Transform the collection to match Vue frontend expectations
        $mapped = $pengembalians->map(function ($p) {
            return [
                'id_pengembalian' => $p->id,
                'id_transaksi' => $p->id_transaksi,
                'tanggal_kembali' => $p->tgl_kembali_aktual,
                'kondisi_kendaraan' => $p->kondisi_kendaraan,
                'catatan' => $p->catatan,
                'transaksi' => $p->transaksi
            ];
        });
        return response()->json(['status' => true, 'data' => $mapped]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_transaksi' => 'required|exists:transaksis,id',
            'tanggal_kembali' => 'required|date',
            'kondisi_kendaraan' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $pengembalian = Pengembalian::create([
                'id_transaksi' => $request->id_transaksi,
                'tgl_kembali_aktual' => $request->tanggal_kembali,
                'kondisi_kendaraan' => $request->kondisi_kendaraan,
                'catatan' => $request->catatan ?? ''
            ]);

            $transaksi = Transaksi::find($request->id_transaksi);
            $transaksi->update(['status' => 'selesai']);

            Kendaraan::where('id', $transaksi->id_kendaraan)->update(['status' => 'tersedia']);

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Berhasil']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $pengembalian = Pengembalian::find($id);
        if ($pengembalian) {
            $pengembalian->delete();
            return response()->json(['status' => true]);
        }
        return response()->json(['status' => false], 404);
    }
}
