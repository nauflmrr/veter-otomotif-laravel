@extends('layouts.app-custom')

@section('title', 'Dashboard Admin')

@section('content')
<h1 class="text-3xl font-bold text-slate-900 mb-2">Dashboard Admin</h1>
<p class="text-slate-500 mb-6">Kelola booking servis masuk dari pelanggan.</p>

@if(session('sukses'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">
        {{ session('sukses') }}
    </div>
@endif

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
                <th class="p-3">Status</th>
                <th class="p-3">Aksi</th>
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
                    <td class="p-3">
                        <form method="POST" action="{{ route('servis.updateStatus', $s->id) }}">
                            @csrf
                            <select name="status" onchange="this.form.submit()" 
                                class="px-2 py-1 border rounded text-xs">
                                @foreach(['Pendaftaran', 'Dikerjakan', 'Selesai'] as $opt)
                                    <option value="{{ $opt }}" {{ $s->status === $opt ? 'selected' : '' }}>
                                        {{ $opt }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </td>
                    <td class="p-3">
                        <a href="{{ route('invoice', $s->id) }}" target="_blank"
                           class="bg-slate-700 hover:bg-slate-800 text-white text-xs font-semibold px-3 py-1.5 rounded transition inline-block">
                            🧾 Invoice
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="p-4 text-center text-slate-400">Belum ada booking.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection