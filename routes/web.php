<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PerusahaanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/', [LoginController::class, 'login']);
Route::get('/logout', [LoginController::class, 'logout']);

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index']);
    
    Route::get('/barang', [BarangController::class, 'index']);
    Route::post('/barang', [BarangController::class, 'store']);
    Route::put('/barang/{barang}', [BarangController::class, 'update']);
    Route::delete('/barang/{barang}', [BarangController::class, 'destroy']);
    
    Route::get('/perusahaan', [PerusahaanController::class, 'index']);
    Route::post('/perusahaan', [PerusahaanController::class, 'store']);
    Route::put('/perusahaan/{perusahaan}', [PerusahaanController::class, 'update']);
    Route::delete('/perusahaan/{perusahaan}', [PerusahaanController::class, 'destroy']);
    
    Route::get('/transaksi', [TransaksiController::class, 'index']);
    Route::post('/transaksi/{barang}/{perusahaan}', [TransaksiController::class, 'store']);
    Route::delete('/transaksi/{transaksi}', [TransaksiController::class, 'destroy']);
    
    Route::get('/profile', [ProfileController::class, 'index']);
    Route::post('/profile', [ProfileController::class, 'submit']);
});
