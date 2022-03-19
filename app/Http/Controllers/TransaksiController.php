<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Perusahaan;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $transaksis = Transaksi::all();

            foreach ($transaksis as $transaksi) {
                $transaksi->perusahaan;
                $transaksi->barang;
            }
            
            return response()->json([
                "data" => $transaksis
            ], 200);

        } catch (\Error $err) {
            
            return response()->json([
                "message" => 'Something went wrong on server, try again!',
                "err" => $err->getMessage(),
            ], 200);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Barang $barang, Perusahaan $perusahaan)
    {
        try {
            $validator = Validator::make($request->all(), [
                'total_barang' => 'required|numeric|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "errors" => $validator->errors()->all(),
                    "message" => 'Data yang dimasukan tidak valid!'
                ], 200);
            }
            
            $grand_total = $request->total_barang * $barang->harga;
            
            $sisa_barang = $barang->stock - $request->total_barang;
            if ($sisa_barang < 0) {
                return response()->json([
                    "message" => "Total barang melewati stok barang yang tersedia!"
                ], 200);
            }

            $barang->stock = $sisa_barang;
            $barang->save();
            

            $new_transaksi = new Transaksi();
            
            $new_transaksi->total_barang = $request->total_barang;
            $new_transaksi->harga_barang = $barang->harga;
            $new_transaksi->grand_total = $grand_total;
            $new_transaksi->sisa_barang = $sisa_barang;
            $new_transaksi->barang_id = $barang->id;
            $new_transaksi->perusahaan_id = $perusahaan->id;
            
            $new_transaksi->save();

            $new_transaksi->barang;
            $new_transaksi->perusahaan;
            
            return response()->json([
                "data" => $new_transaksi,
                "message" => "Transaksi baru telah ditambahkan!"
            ], 201);
            
        } catch (\Error $err) {

            return response()->json([
                "message" => 'Something went wrong on server, try again!',
                "err" => $err->getMessage(),
            ], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaksi  $transaksi
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaksi $transaksi)
    {
        try {
            $barang = Barang::findOrFail($transaksi->barang_id);
            $barang->stock = $barang->stock + $transaksi->total_barang;
            $barang->save();
            
            $transaksi->delete();
            
            return response()->json([
                "data" => $barang,
                "message" => "Transaksi berhasil dihapus!"
            ], 201);
            
        } catch (\Error $err) {

            return response()->json([
                "message" => 'Something went wrong on server, try again!',
                "err" => $err->getMessage(),
            ], 200);
        }
    }
}
