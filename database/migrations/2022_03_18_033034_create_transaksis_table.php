<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->integer('total_barang');
            $table->integer('harga_barang');
            $table->integer('grand_total');
            $table->integer('sisa_barang');
            $table->timestamps();
            
            $table->unsignedBigInteger('perusahaan_id');
            $table->foreign('perusahaan_id')->references('id')->on('perusahaans');

            $table->unsignedBigInteger('barang_id');
            $table->foreign('barang_id')->references('id')->on('barangs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaksis');
    }
};
