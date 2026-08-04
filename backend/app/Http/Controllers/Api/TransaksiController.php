<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Transaksi;
use App\Models\Kendaraan;
use App\Http\Resources\TransaksiResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksis = Transaksi::with(['pelanggan', 'kendaraan'])->get();
        return response()->json([
            'status' => true,
            'message' => 'Berhasil mengambil data transaksi',
            'data' => TransaksiResource::collection($transaksis)
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_pelanggan' => 'required|exists:pelanggans,id',
            'id_kendaraan' => 'required|exists:kendaraans,id',
            'tgl_sewa' => 'required|date',
            'tgl_kembali' => 'required|date|after_or_equal:tgl_sewa',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $kendaraan = Kendaraan::find($request->id_kendaraan);

        if ($kendaraan->status !== 'tersedia') {
            return response()->json([
                'status' => false,
                'message' => 'Kendaraan sedang tidak tersedia'
            ], 400);
        }

        // Kalkulasi lama sewa & total harga
        $tglSewa = new \DateTime($request->tgl_sewa);
        $tglKembali = new \DateTime($request->tgl_kembali);
        $lamaSewa = $tglSewa->diff($tglKembali)->days;
        
        // Jika hari yang sama, hitung 1 hari
        if ($lamaSewa == 0) $lamaSewa = 1;

        $totalHarga = $lamaSewa * $kendaraan->harga_sewa;

        DB::beginTransaction();
        try {
            $transaksi = Transaksi::create([
                'id_pelanggan' => $request->id_pelanggan,
                'id_kendaraan' => $request->id_kendaraan,
                'tgl_sewa' => $request->tgl_sewa,
                'tgl_kembali' => $request->tgl_kembali,
                'lama_sewa' => $lamaSewa,
                'total_harga' => $totalHarga,
                'status' => 'aktif'
            ]);

            // Update status kendaraan menjadi disewa
            $kendaraan->update(['status' => 'disewa']);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Transaksi berhasil dibuat',
                'data' => new TransaksiResource($transaksi)
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Gagal membuat transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id)
    {
        $transaksi = Transaksi::with(['pelanggan', 'kendaraan', 'pengembalian', 'pembayaran'])->find($id);
        
        if (!$transaksi) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Berhasil mengambil detail transaksi',
            'data' => new TransaksiResource($transaksi)
        ]);
    }

    public function update(Request $request, string $id)
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:aktif,selesai,terlambat',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $transaksi->update(['status' => $request->status]);

        return response()->json([
            'status' => true,
            'message' => 'Transaksi berhasil diupdate',
            'data' => new TransaksiResource($transaksi)
        ]);
    }

    public function destroy(string $id)
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        DB::beginTransaction();
        try {
            // Kembalikan status kendaraan jika transaksi masih aktif/terlambat
            if (in_array($transaksi->status, ['aktif', 'terlambat'])) {
                Kendaraan::where('id', $transaksi->id_kendaraan)->update(['status' => 'tersedia']);
            }
            
            $transaksi->delete();
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Transaksi berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus transaksi'
            ], 500);
        }
    }
}
