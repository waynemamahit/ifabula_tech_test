<?php

namespace App\Http\Controllers;

use App\Models\Perusahaan;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PerusahaanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $perusahaans = Perusahaan::all();
            
            return response()->json([
                "data" => $perusahaans
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
                'kode' => 'required|unique:perusahaans,kode'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "errors" => $validator->errors()->all(),
                    "message" => 'Data yang dimasukan tidak valid!'
                ], 200);
            }

            $new_perusahaan = new Perusahaan();
            
            $new_perusahaan->nama = $request->nama;
            $new_perusahaan->kode = $request->kode;
            
            $new_perusahaan->save();

            
            return response()->json([
                "data" => $new_perusahaan,
                "message" => "Perusahaan baru telah ditambahkan!"
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
     * @param  \App\Models\Perusahaan  $perusahaan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Perusahaan $perusahaan)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nama' => 'required',
                'kode' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "errors" => $validator->errors()->all(),
                    "message" => 'Data yang dimasukan tidak valid!'
                ], 200);
            }

            $perusahaan->nama = $request->nama;
            if ($perusahaan->kode != $request->kode) {
                $perusahaan->kode = $request->kode;
            }
            
            $perusahaan->save();

            return response()->json([
                "data" => $perusahaan,
                "message" => "Data perusahaan telah diperbaharui!"
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
     * @param  \App\Models\Perusahaan  $perusahaan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Perusahaan $perusahaan)
    {
        try {
            $check_transaksi = Transaksi::where('perusahaan_id', $perusahaan->id)->count();
            if ($check_transaksi > 0) {
                return response()->json([
                    'message' => "Data perusahaan gagal dihapus, masih beberap transaksi yang tersedia"
                ], 200);
            }

            $perusahaan->delete();

            return response()->json([
                'data' => "OK",
                'message' => 'Data perusahaan berhasil dihapus!'
            ], 200);

        } catch (\Error $err) {
            
            return response()->json([
                "message" => 'Something went wrong on server, try again!',
                "err" => $err->getMessage(),
            ], 200);
        }
    }
}
