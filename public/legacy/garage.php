<?php
require_once 'lib.php';
$stmt = $pdo->query("SELECT id, nama_mobil, harga FROM cars");
$cars = $stmt->fetchAll();
renderHeader("Simulasi Kredit Kendaraan");
?>

<div class="max-w-3xl mx-auto bg-white p-8 rounded-2xl border border-slate-200 shadow-sm">
    <h2 class="text-2xl font-bold text-slate-900 mb-2">Kalkulator Simulasi Kredit Mobil</h2>
    <p class="text-slate-500 text-sm mb-6">Hitung perkiraan angsuran bulanan berdasarkan uang muka (DP) dan tenor pembiayaan.</p>

    <div class="space-y-5">
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Pilih Model Kendaraan</label>
            <select id="pilihanMobil" onchange="updateHarga()" class="w-full p-2.5 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-red-500">
                <option value="0">-- Pilih Mobil --</option>
                <?php foreach ($cars as $c): ?>
                    <option value="<?= $c['harga']; ?>"><?= htmlspecialchars($c['nama_mobil']); ?> (Rp <?= number_format($c['harga'], 0, ',', '.'); ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Uang Muka (DP %)</label>
                <select id="persenDp" class="w-full p-2.5 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-red-500">
                    <option value="0.20">20%</option>
                    <option value="0.25">25%</option>
                    <option value="0.30">30%</option>
                    <option value="0.50">50%</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Jangka Waktu (Tenor)</label>
                <select id="tenor" class="w-full p-2.5 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-red-500">
                    <option value="12">12 Bulan (1 Tahun)</option>
                    <option value="24">24 Bulan (2 Tahun)</option>
                    <option value="36">36 Bulan (3 Tahun)</option>
                    <option value="48">48 Bulan (4 Tahun)</option>
                    <option value="60">60 Bulan (5 Tahun)</option>
                </select>
            </div>
        </div>

        <button onclick="hitungKredit()" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition shadow-md">Kalkulasi Angsuran</button>
    </div>

    <div id="hasilKalkulasi" class="mt-8 pt-6 border-t border-slate-100 hidden">
        <h4 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-4">Estimasi Rincian Pembayaran</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
            <div class="p-4 bg-slate-50 rounded-xl">
                <span class="text-xs text-slate-500 block mb-1">Total Uang Muka (DP)</span>
                <span id="labelDp" class="text-lg font-bold text-slate-900">-</span>
            </div>
            <div class="p-4 bg-slate-50 rounded-xl">
                <span class="text-xs text-slate-500 block mb-1">Pokok Hutang</span>
                <span id="labelPokok" class="text-lg font-bold text-slate-900">-</span>
            </div>
            <div class="p-4 bg-red-50 rounded-xl border border-red-100">
                <span class="text-xs text-red-600 font-semibold block mb-1">Angsuran / Bulan</span>
                <span id="labelAngsuran" class="text-xl font-extrabold text-red-600">-</span>
            </div>
        </div>
    </div>
</div>

<script>
function hitungKredit() {
    const harga = parseFloat(document.getElementById('pilihanMobil').value);
    if (!harga || harga <= 0) {
        alert('Silakan tentukan model kendaraan terlebih dahulu.');
        return;
    }
    const persenDp = parseFloat(document.getElementById('persenDp').value);
    const tenorBulan = parseInt(document.getElementById('tenor').value);
    const bungaPerTahun = 0.08; // Estimasi bunga flat 8% per tahun

    const nominalDp = harga * persenDp;
    const pokokPinjaman = harga - nominalDp;
    const totalBunga = pokokPinjaman * (bungaPerTahun * (tenorBulan / 12));
    const totalHutang = pokokPinjaman + totalBunga;
    const angsuranPerBulan = totalHutang / tenorBulan;

    const formatter = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 });

    document.getElementById('labelDp').innerText = formatter.format(nominalDp);
    document.getElementById('labelPokok').innerText = formatter.format(pokokPinjaman);
    document.getElementById('labelAngsuran').innerText = formatter.format(angsuranPerBulan);
    document.getElementById('hasilKalkulasi').classList.remove('hidden');
}
</script>

<?php renderFooter(); ?>