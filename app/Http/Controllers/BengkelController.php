<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Technician;
use App\Models\ServiceType;
use Illuminate\Http\Request;

class BengkelController extends Controller
{
    public function index()
    {
        $serviceTypes = ServiceType::orderBy('id')->get();
        $technicians = Technician::where('aktif', 1)->orderBy('nama')->get();
        return view('bengkel.index', compact('serviceTypes', 'technicians'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pemilik' => 'required|string|max:100',
            'telepon' => 'required|string|max:20',
            'nomor_polisi' => 'required|string|max:20',
            'tipe_kendaraan' => 'required|string|max:100',
            'service_type_id' => 'required|exists:service_types,id',
            'technician_id' => 'required|exists:technicians,id',
            'tanggal_servis' => 'required|date',
            'keluhan' => 'nullable|string',
        ]);

        $serviceType = ServiceType::find($validated['service_type_id']);
        $validated['user_id'] = auth()->id();
        $validated['status'] = 'Pendaftaran';
        $validated['jenis_servis'] = $serviceType->nama;
        $validated['estimasi_biaya'] = $serviceType->estimasi_biaya;

        Service::create($validated);

        $teknisi = Technician::find($validated['technician_id']);
        $pesan = "Booking servis berhasil! Teknisi: {$teknisi->nama}. Estimasi biaya: Rp " . number_format($serviceType->estimasi_biaya, 0, ',', '.');
        return redirect()->route('bengkel.index')->with('sukses', $pesan);
    }

    public function dashboard()
    {
        $services = Service::with(['technician', 'serviceType'])->orderBy('id', 'desc')->get();
        $counts = [
            'semua' => $services->count(),
            'Pendaftaran' => $services->where('status', 'Pendaftaran')->count(),
            'Dikerjakan' => $services->where('status', 'Dikerjakan')->count(),
            'Selesai' => $services->where('status', 'Selesai')->count(),
        ];
        return view('bengkel.dashboard', compact('services', 'counts'));
    }

    public function riwayat()
    {
        $user_id = auth()->id();
        $services = Service::with(['technician', 'serviceType'])
            ->where('user_id', $user_id)->orderBy('id', 'desc')->get();
        $counts = [
            'semua' => $services->count(),
            'Pendaftaran' => $services->where('status', 'Pendaftaran')->count(),
            'Dikerjakan' => $services->where('status', 'Dikerjakan')->count(),
            'Selesai' => $services->where('status', 'Selesai')->count(),
        ];
        return view('bengkel.riwayat', compact('services', 'counts'));
    }

    // Invoice
    public function invoice($id)
    {
        $servis = Service::with(['technician', 'serviceType'])->findOrFail($id);

        // Proteksi: admin bisa lihat semua, customer cuma miliknya
        $user = auth()->user();
        if ($user->role !== 'admin' && $servis->user_id != $user->id) {
            abort(403, 'Anda tidak berhak mengakses invoice ini.');
        }

        $nomor_invoice = 'INV-' . date('Ymd', strtotime($servis->tanggal_servis)) . '-' . str_pad($servis->id, 4, '0', STR_PAD_LEFT);
        return view('bengkel.invoice', compact('servis', 'nomor_invoice'));
    }

    // Update status (admin)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Pendaftaran,Dikerjakan,Selesai',
        ]);
        $servis = Service::findOrFail($id);
        $servis->update(['status' => $request->status]);
        return redirect()->route('dashboard')->with('sukses', "Status servis #{$id} diubah jadi {$request->status}.");
    }
}