<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'total_barang',
        'harga_barang',
        'grand_total',
        'sisa_barang',
        'perusahaan_id',
        'barang_id',
    ];

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
