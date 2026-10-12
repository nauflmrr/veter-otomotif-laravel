<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class BengkelController extends Controller
{
    public function index()
    {
        return view('bengkel.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pemilik' => 'required|string|max:100',
            'telepon' => 'required|string|max:20',
            'nomor_polisi' => 'required|string|max:20',
            'tipe_kendaraan' => 'required|string|max:100',
            'jenis_servis' => 'required|string',
            'tanggal_servis' => 'required|date',
            'keluhan' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'Pendaftaran';

        Service::create($validated);

        return redirect()->route('bengkel.index')->with('sukses', 'Booking servis berhasil!');
    }

    public function dashboard()
    {
        $services = Service::orderBy('id', 'desc')->get();

        $counts = [
            'semua' => $services->count(),
            'Pendaftaran' => $services->where('status', 'Pendaftaran')->count(),
            'Dikerjakan' => $services->where('status', 'Dikerjakan')->count(),
            'Selesai' => $services->where('status', 'Selesai')->count(),
        ];

        return view('bengkel.dashboard', compact('services', 'counts'));
    }
}