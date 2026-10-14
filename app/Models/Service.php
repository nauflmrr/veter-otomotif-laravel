<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'technician_id', 'service_type_id', 'estimasi_biaya',
        'nama_pemilik', 'telepon', 'nomor_polisi', 'tipe_kendaraan',
        'jenis_servis', 'tanggal_servis', 'keluhan', 'status',
    ];

    public function technician()
    {
        return $this->belongsTo(Technician::class);
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }
}