<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->string('nama_pemilik');
            $table->string('telepon');
            $table->string('nomor_polisi');
            $table->string('tipe_kendaraan');
            $table->string('jenis_servis');
            $table->date('tanggal_servis');
            $table->text('keluhan')->nullable();
            $table->enum('status', ['Pendaftaran', 'Dikerjakan', 'Selesai'])->default('Pendaftaran');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};