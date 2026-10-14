<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $nomor_invoice }} - PT Veter Otomotif</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .print-area { box-shadow: none !important; border: none !important; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-10 px-4">

    <div class="max-w-3xl mx-auto mb-4 flex justify-between items-center no-print">
        <a href="{{ auth()->user()->role === 'admin' ? '/dashboard' : '/riwayat' }}" 
           class="text-sm text-slate-600 hover:text-slate-900 font-semibold">← Kembali</a>
        <button onclick="window.print()" 
            class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition shadow">
            🖨️ Print / Simpan PDF
        </button>
    </div>

    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-sm border border-slate-200 p-10 print-area">

        <div class="flex justify-between items-start border-b-2 border-slate-800 pb-6 mb-6">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">PT Veter Otomotif</h1>
                <p class="text-sm text-slate-500">Dealer & Bengkel Resmi</p>
                <p class="text-xs text-slate-400 mt-1">Jl Hasan No 98, Kebon Nanas, Jakarta Barat<br>Telp: 021-10090</p>
            </div>
            <div class="text-right">
                <h2 class="text-xl font-bold text-red-600">INVOICE</h2>
                <p class="text-sm text-slate-700 mt-1">No: <strong>{{ $nomor_invoice }}</strong></p>
                <p class="text-xs text-slate-500">Tanggal: {{ date('d M Y', strtotime($servis->tanggal_servis)) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="text-xs font-bold text-slate-500 uppercase mb-2">Ditagihkan Kepada</h3>
                <p class="font-semibold text-slate-900">{{ $servis->nama_pemilik }}</p>
                <p class="text-sm text-slate-600">{{ $servis->telepon }}</p>
            </div>
            <div>
                <h3 class="text-xs font-bold text-slate-500 uppercase mb-2">Kendaraan</h3>
                <p class="font-semibold text-slate-900">{{ $servis->nomor_polisi }}</p>
                <p class="text-sm text-slate-600">{{ $servis->tipe_kendaraan }}</p>
            </div>
        </div>

        <table class="w-full text-sm mb-6">
            <thead>
                <tr class="border-b-2 border-slate-300">
                    <th class="text-left py-2 font-bold text-slate-700">Deskripsi</th>
                    <th class="text-right py-2 font-bold text-slate-700">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-slate-100">
                    <td class="py-3">
                        <div class="font-semibold text-slate-900">{{ $servis->jenis_servis }}</div>
                        @if($servis->serviceType && $servis->serviceType->deskripsi)
                            <div class="text-xs text-slate-500 mt-1">{{ $servis->serviceType->deskripsi }}</div>
                        @endif
                        @if($servis->keluhan)
                            <div class="text-xs text-slate-500 mt-1 italic">Catatan: {{ $servis->keluhan }}</div>
                        @endif
                    </td>
                    <td class="py-3 text-right font-semibold text-slate-900">
                        Rp {{ number_format($servis->estimasi_biaya ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="border-t-2 border-slate-300">
                    <td class="py-2 text-right font-bold text-slate-800 text-lg">TOTAL:</td>
                    <td class="py-2 text-right font-extrabold text-red-600 text-lg">
                        Rp {{ number_format($servis->estimasi_biaya ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>

        <div class="grid grid-cols-2 gap-6 mb-8 pt-4 border-t border-slate-200">
            <div>
                <h3 class="text-xs font-bold text-slate-500 uppercase mb-2">Teknisi</h3>
                @if($servis->technician)
                    <p class="font-semibold text-slate-900">{{ $servis->technician->nama }}</p>
                    <p class="text-sm text-slate-600">{{ $servis->technician->keahlian }}</p>
                @else
                    <p class="text-sm text-slate-400 italic">Belum ditentukan</p>
                @endif
            </div>
            <div>
                <h3 class="text-xs font-bold text-slate-500 uppercase mb-2">Status Servis</h3>
                @php
                    $statusWarna = [
                        'Pendaftaran' => 'bg-amber-100 text-amber-800',
                        'Dikerjakan' => 'bg-blue-100 text-blue-800',
                        'Selesai' => 'bg-emerald-100 text-emerald-800',
                    ][$servis->status] ?? 'bg-slate-100';
                @endphp
                <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full {{ $statusWarna }}">
                    {{ $servis->status }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6 pt-6 border-t border-slate-200">
            <div>
                <p class="text-xs text-slate-500 mb-16">Catatan: Invoice ini adalah bukti resmi servis di PT Veter Otomotif.</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-slate-500 mb-16">Hormat kami,</p>
                <p class="font-semibold text-slate-900 border-t border-slate-300 pt-1 inline-block">PT Veter Otomotif</p>
            </div>
        </div>

    </div>

</body>
</html>