@extends('layouts.app-custom')

@section('title', 'Booking Servis')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 rounded-2xl border border-slate-200 shadow-sm">
    <h2 class="text-2xl font-bold text-slate-900 mb-2">Penjadwalan Servis Kendaraan</h2>
    <p class="text-slate-500 text-sm mb-6">Booking antrean perbaikan berkala dan penggantian suku cadang tanpa antre.</p>

    @if(session('sukses'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">
            {{ session('sukses') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('bengkel.store') }}" class="space-y-4">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Pemilik</label>
                <input type="text" name="nama_pemilik" value="{{ old('nama_pemilik') }}" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor WhatsApp</label>
                <input type="tel" name="telepon" value="{{ old('telepon') }}" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-red-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Polisi (Plat)</label>
                <input type="text" name="nomor_polisi" value="{{ old('nomor_polisi') }}" placeholder="B 1234 ABC" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Model / Tipe Mobil</label>
                <input type="text" name="tipe_kendaraan" value="{{ old('tipe_kendaraan') }}" placeholder="Fortuner 2022" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-red-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Paket Servis</label>
                <select name="service_type_id" id="service_type_id" required 
                    class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-red-500"
                    onchange="updateEstimasi()">
                    <option value="">-- Pilih Paket Servis --</option>
                    @foreach($serviceTypes as $st)
                        <option value="{{ $st->id }}" data-biaya="{{ $st->estimasi_biaya }}">
                            {{ $st->nama }} — Rp {{ number_format($st->estimasi_biaya, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Teknisi</label>
                <select name="technician_id" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">-- Pilih Teknisi --</option>
                    @foreach($technicians as $t)
                        <option value="{{ $t->id }}">
                            {{ $t->nama }}{{ $t->keahlian ? ' — ' . $t->keahlian : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg">
            <div class="flex justify-between items-center">
                <span class="text-sm font-semibold text-slate-700">Estimasi Biaya:</span>
                <span id="estimasi_display" class="text-lg font-bold text-red-600">Rp 0</span>
            </div>
            <p class="text-xs text-slate-500 mt-1">*Estimasi bisa berubah setelah pemeriksaan langsung oleh teknisi.</p>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Rencana Servis</label>
            <input type="date" name="tanggal_servis" value="{{ old('tanggal_servis') }}" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-red-500">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan / Keluhan Tambahan</label>
            <textarea name="keluhan" rows="3" class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-red-500" placeholder="Deskripsikan kendala mesin atau kebutuhan perbaikan">{{ old('keluhan') }}</textarea>
        </div>

        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition shadow-md">
            Konfirmasi Booking Bengkel
        </button>
    </form>
</div>

<script>
function updateEstimasi() {
    const select = document.getElementById('service_type_id');
    const opt = select.options[select.selectedIndex];
    const biaya = opt.getAttribute('data-biaya') || 0;
    document.getElementById('estimasi_display').textContent = 'Rp ' + parseInt(biaya).toLocaleString('id-ID');
}
</script>
@endsection