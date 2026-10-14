@extends('layouts.app-custom')

@section('title', 'Riwayat Servis')

@section('content')

<h1 class="text-3xl font-bold text-slate-900 mb-2">Riwayat Servis Saya</h1>
<p class="text-slate-500 mb-6">Halo {{ auth()->user()->name }}, berikut daftar booking servis kendaraan kamu.</p>

<div class="flex flex-wrap gap-2 mb-6">
    @foreach(['semua' => 'Semua', 'Pendaftaran' => 'Pendaftaran', 'Dikerjakan' => 'Dikerjakan', 'Selesai' => 'Selesai'] as $key => $label)
        <a href="?filter={{ $key }}" 
           class="px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-2
                  {{ request('filter', 'semua') === $key 
                     ? 'bg-slate-800 text-white shadow-md' 
                     : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            {{ $label }}
            <span class="text-xs px-2 py-0.5 rounded-full {{ request('filter', 'semua') === $key ? 'bg-white/25' : 'bg-slate-100' }}">
                {{ $counts[$key] }}
            </span>
        </a>
    @endforeach
</div>

@php
    $filter = request('filter', 'semua');
    $filtered = $filter === 'semua' ? $services : $services->where('status', $filter);
@endphp

@if($filtered->isEmpty())
    <div class="bg-white rounded-2xl border p-12 text-center">
        <div class="text-5xl mb-3">🔧</div>
        <p class="text-slate-500 mb-4">Belum ada servis dengan status "{{ $filter }}".</p>
        <a href="/bengkel" class="inline-block bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-2.5 rounded-lg transition">
            Booking Servis Sekarang
        </a>
    </div>
@else
    <div class="space-y-4">
        @foreach($filtered as $s)
            @php
                $statusWarna = [
                    'Pendaftaran' => 'bg-amber-100 text-amber-800',
                    'Dikerjakan' => 'bg-blue-100 text-blue-800',
                    'Selesai' => 'bg-emerald-100 text-emerald-800',
                ][$s->status] ?? 'bg-slate-100';
            @endphp
            <div class="bg-white rounded-2xl border p-6 hover:shadow-md transition">
                <div class="flex flex-wrap justify-between items-start gap-4 mb-4">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <h3 class="text-lg font-bold text-slate-900">{{ $s->nomor_polisi }}</h3>
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $statusWarna }}">
                                {{ $s->status }}
                            </span>
                        </div>
                        <p class="text-sm text-slate-500">{{ $s->tipe_kendaraan }} · {{ $s->tanggal_servis }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-slate-400 uppercase font-semibold">Estimasi Biaya</div>
                        <div class="text-xl font-bold text-red-600">
                            Rp {{ number_format($s->estimasi_biaya ?? 0, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-slate-100">
                    <div>
                        <div class="text-xs text-slate-400 uppercase font-semibold mb-1">Paket Servis</div>
                        <div class="text-sm font-medium text-slate-800">{{ $s->jenis_servis }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-400 uppercase font-semibold mb-1">Teknisi</div>
                        <div class="text-sm font-medium text-slate-800">
                            @if($s->technician)
                                🔧 {{ $s->technician->nama }} 
                                <span class="text-xs text-slate-400">({{ $s->technician->keahlian }})</span>
                            @else
                                <span class="text-slate-400 italic">Belum ditentukan</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="pt-4 mt-4 border-t border-slate-100">
                    <a href="{{ route('invoice', $s->id) }}" target="_blank"
                       class="inline-block bg-slate-700 hover:bg-slate-800 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                        🧾 Lihat Invoice
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@endif

@endsection