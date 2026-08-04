<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Kendaraan;
use App\Http\Resources\KendaraanResource;
use Illuminate\Support\Facades\Validator;

class KendaraanController extends Controller
{
    public function index()
    {
        $kendaraans = Kendaraan::all();
        return response()->json([
            'status' => true,
            'message' => 'Berhasil mengambil data kendaraan',
            'data' => KendaraanResource::collection($kendaraans)
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string',
            'merk' => 'required|string',
            'jenis' => 'required|in:Motor,Mobil,SUV,Van,Truck',
            'plat_nomor' => 'required|string|unique:kendaraans,plat_nomor',
            'harga_sewa' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $kendaraan = Kendaraan::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Kendaraan berhasil ditambahkan',
            'data' => new KendaraanResource($kendaraan)
        ], 201);
    }

    public function show(string $id)
    {
        $kendaraan = Kendaraan::find($id);
        
        if (!$kendaraan) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Berhasil mengambil detail kendaraan',
            'data' => new KendaraanResource($kendaraan)
        ]);
    }

    public function update(Request $request, string $id)
    {
        $kendaraan = Kendaraan::find($id);

        if (!$kendaraan) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'sometimes|string',
            'merk' => 'sometimes|string',
            'jenis' => 'sometimes|in:Motor,Mobil,SUV,Van,Truck',
            'plat_nomor' => 'sometimes|string|unique:kendaraans,plat_nomor,'.$id,
            'harga_sewa' => 'sometimes|numeric',
            'status' => 'sometimes|in:tersedia,disewa,servis',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $kendaraan->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Kendaraan berhasil diupdate',
            'data' => new KendaraanResource($kendaraan)
        ]);
    }

    public function destroy(string $id)
    {
        $kendaraan = Kendaraan::find($id);

        if (!$kendaraan) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        $kendaraan->delete();

        return response()->json([
            'status' => true,
            'message' => 'Kendaraan berhasil dihapus'
        ]);
    }
}
