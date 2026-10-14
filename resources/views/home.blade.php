@extends('layouts.app-custom')

@section('title', 'Beranda')

@section('content')

<div class="bg-gradient-to-r from-red-600 to-red-800 text-white rounded-2xl p-12 mb-8">
    <h1 class="text-4xl font-bold mb-3">Selamat Datang di PT Veter Otomotif</h1>
    <p class="text-lg opacity-90 mb-6">Dealer mobil terpercaya & bengkel resmi dengan layanan purna jual lengkap.</p>
    <div class="flex gap-3">
        <a href="/legacy/dealer.php" class="bg-white text-red-600 font-bold px-6 py-3 rounded-lg hover:bg-slate-100">
            Lihat Katalog Mobil
        </a>
        <a href="/bengkel" class="bg-red-900 text-white font-bold px-6 py-3 rounded-lg hover:bg-red-950">
            Booking Servis
        </a>
    </div>
</div>

<h2 class="text-2xl font-bold text-slate-900 mb-4">Layanan Kami</h2>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

    <a href="/legacy/dealer.php" class="bg-white p-6 rounded-xl border hover:shadow-lg transition">
        <div class="text-3xl mb-3">🚗</div>
        <h3 class="font-bold text-slate-900 mb-1">Katalog Mobil</h3>
        <p class="text-xs text-slate-500">Lihat koleksi mobil terbaru & spesifikasinya</p>
    </a>

    <a href="/legacy/garage.php" class="bg-white p-6 rounded-xl border hover:shadow-lg transition">
        <div class="text-3xl mb-3">💰</div>
        <h3 class="font-bold text-slate-900 mb-1">Simulasi Kredit</h3>
        <p class="text-xs text-slate-500">Hitung cicilan bulanan sesuai budget kamu</p>
    </a>

    <a href="/bengkel" class="bg-white p-6 rounded-xl border hover:shadow-lg transition">
        <div class="text-3xl mb-3">🔧</div>
        <h3 class="font-bold text-slate-900 mb-1">Booking Servis</h3>
        <p class="text-xs text-slate-500">Jadwalkan servis berkala tanpa antre</p>
    </a>

    <a href="/dashboard" class="bg-white p-6 rounded-xl border hover:shadow-lg transition">
        <div class="text-3xl mb-3">📊</div>
        <h3 class="font-bold text-slate-900 mb-1">Dashboard Admin</h3>
        <p class="text-xs text-slate-500">Kelola booking servis & data pelanggan</p>
    </a>

</div>

<h2 class="text-2xl font-bold text-slate-900 mb-4">Kenapa Pilih Kami?</h2>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="bg-white p-6 rounded-xl border">
        <h3 class="font-bold text-slate-900 mb-2">✅ Dealer Resmi</h3>
        <p class="text-sm text-slate-500">Terdaftar sebagai dealer resmi dengan garansi penuh.</p>
    </div>
    <div class="bg-white p-6 rounded-xl border">
        <h3 class="font-bold text-slate-900 mb-2">⚡ Layanan Cepat</h3>
        <p class="text-sm text-slate-500">Booking servis online, tanpa antre panjang.</p>
    </div>
    <div class="bg-white p-6 rounded-xl border">
        <h3 class="font-bold text-slate-900 mb-2">🛠️ Teknisi Ahli</h3>
        <p class="text-sm text-slate-500">Ditangani teknisi berpengalaman & bersertifikat.</p>
    </div>
</div>

@endsection