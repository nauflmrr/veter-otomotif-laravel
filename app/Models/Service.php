<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'nama_pemilik', 'telepon', 'nomor_polisi',
        'tipe_kendaraan', 'jenis_servis', 'tanggal_servis',
        'keluhan', 'status',
    ];
}