<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $barangs = Barang::all();
            
            return response()->json([
                'data' => $barangs
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
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nama' => 'required',
                'harga' => 'required|numeric|min:1',
                'stock' => 'required|numeric|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "errors" => $validator->errors()->all(),
                    "message" => 'Data yang dimasukan tidak valid!'
                ], 200);
            }

            $new_barang = new Barang();
            
            $new_barang->nama = $request->nama;
            $new_barang->harga = $request->harga;
            $new_barang->stock = $request->stock;
            
            $new_barang->save();

            
            return response()->json([
                "data" => $new_barang,
                "message" => "Barang baru telah ditambahkan!"
            ], 201);
            
        } catch (\Error $err) {

            return response()->json([
                "message" => 'Something went wrong on server, try again!',
                "err" => $err->getMessage(),
            ], 200);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Barang  $barang
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Barang $barang)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nama' => 'required',
                'harga' => 'required|numeric|min:1',
                'stock' => 'required|numeric|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "errors" => $validator->errors()->all(),
                    "message" => 'Data yang dimasukan tidak valid!'
                ], 200);
            }

            $barang->nama = $request->nama;
            $barang->harga = $request->harga;
            $barang->stock = $request->stock;
            
            $barang->save();

            
            return response()->json([
                "data" => $barang,
                "message" => "Data barang telah diperbaharui!"
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
     * @param  \App\Models\Barang  $barang
     * @return \Illuminate\Http\Response
     */
    public function destroy(Barang $barang)
    {
        try {
            $check_transaksi = Transaksi::where('perusahaan_id', $barang->id)->count();
            if ($check_transaksi > 0) {
                return response()->json([
                    'message' => "Data barang gagal dihapus, masih beberap transaksi yang tersedia"
                ], 200);
            }

            $barang->delete();

            return response()->json([
                'data' => "OK",
                'message' => 'Data barang berhasil dihapus!'
            ], 200);

        } catch (\Error $err) {
            
            return response()->json([
                "message" => 'Something went wrong on server, try again!',
                "err" => $err->getMessage(),
            ], 200);
        }
    }
}
