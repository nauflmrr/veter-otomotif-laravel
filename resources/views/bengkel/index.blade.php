<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Booking Bengkel - PT Veter Otomotif</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50">
    <div class="max-w-2xl mx-auto py-12 px-4">
        <h1 class="text-3xl font-bold text-slate-900 mb-6">Booking Servis Kendaraan</h1>

        @if(session('sukses'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg">
                {{ session('sukses') }}
            </div>
        @endif

        <form method="POST" action="{{ route('bengkel.store') }}" class="bg-white p-6 rounded-xl shadow space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-semibold mb-1">Nama Pemilik</label>
                <input type="text" name="nama_pemilik" required class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Telepon</label>
                <input type="text" name="telepon" required class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Nomor Polisi</label>
                <input type="text" name="nomor_polisi" required class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Tipe Kendaraan</label>
                <input type="text" name="tipe_kendaraan" required class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Jenis Servis</label>
                <select name="jenis_servis" required class="w-full px-3 py-2 border rounded-lg">
                    <option value="Servis Berkala 10.000 KM">Servis Berkala 10.000 KM</option>
                    <option value="Ganti Oli & Tune Up">Ganti Oli & Tune Up</option>
                    <option value="Pengecekan Rem & Kaki-kaki">Pengecekan Rem & Kaki-kaki</option>
                    <option value="General Overhaul">General Overhaul</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Tanggal Servis</label>
                <input type="date" name="tanggal_servis" required class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Keluhan</label>
                <textarea name="keluhan" class="w-full px-3 py-2 border rounded-lg" rows="3"></textarea>
            </div>

            <button type="submit" class="w-full bg-red-600 text-white font-bold py-3 rounded-lg hover:bg-red-700">
                Booking Sekarang
            </button>
        </form>
    </div>
</body>
</html>