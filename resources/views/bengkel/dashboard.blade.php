@extends('layouts.app-custom')

@section('title', 'Dashboard Admin')

@section('content')
<h1 class="text-3xl font-bold text-slate-900 mb-2">Dashboard Admin</h1>
<p class="text-slate-500 mb-6">Kelola booking servis masuk dari pelanggan.</p>

<div class="grid grid-cols-4 gap-4 mb-8">
    <div class="bg-white p-4 rounded-xl border">
        <div class="text-xs text-slate-500">Total</div>
        <div class="text-2xl font-bold text-slate-900">{{ $counts['semua'] }}</div>
    </div>
    <div class="bg-amber-50 p-4 rounded-xl border border-amber-200">
        <div class="text-xs text-amber-700">Pendaftaran</div>
        <div class="text-2xl font-bold text-amber-800">{{ $counts['Pendaftaran'] }}</div>
    </div>
    <div class="bg-blue-50 p-4 rounded-xl border border-blue-200">
        <div class="text-xs text-blue-700">Dikerjakan</div>
        <div class="text-2xl font-bold text-blue-800">{{ $counts['Dikerjakan'] }}</div>
    </div>
    <div class="bg-emerald-50 p-4 rounded-xl border border-emerald-200">
        <div class="text-xs text-emerald-700">Selesai</div>
        <div class="text-2xl font-bold text-emerald-800">{{ $counts['Selesai'] }}</div>
    </div>
</div>

<div class="bg-white rounded-xl border overflow-x-auto">
    <table class="w-full text-left text-sm">
        <thead class="bg-slate-50 border-b font-semibold">
            <tr>
                <th class="p-3">Pemilik</th>
                <th class="p-3">Plat / Model</th>
                <th class="p-3">Paket Servis</th>
                <th class="p-3">Teknisi</th>
                <th class="p-3">Estimasi</th>
                <th class="p-3">Tanggal</th>
                <th class="p-3">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($services as $s)
                <tr>
                    <td class="p-3">
                        <div class="font-medium">{{ $s->nama_pemilik }}</div>
                        <div class="text-xs text-slate-400">{{ $s->telepon }}</div>
                    </td>
                    <td class="p-3">
                        <div>{{ $s->nomor_polisi }}</div>
                        <div class="text-xs text-slate-400">{{ $s->tipe_kendaraan }}</div>
                    </td>
                    <td class="p-3">{{ $s->jenis_servis }}</td>
                    <td class="p-3">
                        @if($s->technician)
                            <div class="font-medium">🔧 {{ $s->technician->nama }}</div>
                            <div class="text-xs text-slate-400">{{ $s->technician->keahlian }}</div>
                        @else
                            <span class="text-xs text-slate-400 italic">-</span>
                        @endif
                    </td>
                    <td class="p-3 font-semibold text-red-600">
                        Rp {{ number_format($s->estimasi_biaya ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="p-3">{{ $s->tanggal_servis }}</td>
                    <td class="p-3">
                        @php
                            $warna = [
                                'Pendaftaran' => 'bg-amber-100 text-amber-800',
                                'Dikerjakan' => 'bg-blue-100 text-blue-800',
                                'Selesai' => 'bg-emerald-100 text-emerald-800',
                            ][$s->status] ?? 'bg-slate-100';
                        @endphp
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $warna }}">
                            {{ $s->status }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="p-4 text-center text-slate-400">Belum ada booking.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection