<?php
require_once 'lib.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = (int) ($_GET['id'] ?? 0);
$is_admin = ($_SESSION['user_role'] ?? '') === 'admin';
$user_id = $_SESSION['user_id'];

if ($id <= 0) {
    die("ID servis tidak valid.");
}

// Ambil data servis + join teknisi & paket
$sql = "
    SELECT s.*, 
           t.nama AS nama_teknisi, 
           t.keahlian AS keahlian_teknisi,
           t.telepon AS telepon_teknisi,
           st.nama AS nama_paket,
           st.deskripsi AS deskripsi_paket
    FROM services s
    LEFT JOIN technicians t ON s.technician_id = t.id
    LEFT JOIN service_types st ON s.service_type_id = st.id
    WHERE s.id = ?
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$servis = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$servis) {
    die("Data servis tidak ditemukan.");
}

// Proteksi: pelanggan cuma bisa lihat invoice miliknya sendiri
if (!$is_admin && $servis['user_id'] != $user_id) {
    die("Anda tidak berhak mengakses invoice ini.");
}

$nomor_invoice = 'INV-' . date('Ymd', strtotime($servis['tanggal_servis'] ?? 'now')) . '-' . str_pad($servis['id'], 4, '0', STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice <?= $nomor_invoice; ?> - PT Veter Otomotif</title>
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

    <!-- Tombol aksi (gak muncul pas di-print) -->
    <div class="max-w-3xl mx-auto mb-4 flex justify-between items-center no-print">
        <a href="<?= $is_admin ? 'admin.php' : 'garage.php'; ?>" 
           class="text-sm text-slate-600 hover:text-slate-900 font-semibold">
            ← Kembali
        </a>
        <button onclick="window.print()" 
            class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition shadow">
            🖨️ Print / Simpan PDF
        </button>
    </div>

    <!-- Area invoice -->
    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-sm border border-slate-200 p-10 print-area">

        <!-- Kop -->
        <div class="flex justify-between items-start border-b-2 border-slate-800 pb-6 mb-6">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">PT Veter Otomotif</h1>
                <p class="text-sm text-slate-500">Dealer & Bengkel Resmi</p>
                <p class="text-xs text-slate-400 mt-1">Jl. Contoh No. 123, Jakarta<br>Telp: (021) 1234-5678</p>
            </div>
            <div class="text-right">
                <h2 class="text-xl font-bold text-red-600">INVOICE</h2>
                <p class="text-sm text-slate-700 mt-1">No: <strong><?= $nomor_invoice; ?></strong></p>
                <p class="text-xs text-slate-500">Tanggal: <?= date('d M Y', strtotime($servis['tanggal_servis'])); ?></p>
            </div>
        </div>

        <!-- Info pelanggan & kendaraan -->
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="text-xs font-bold text-slate-500 uppercase mb-2">Ditagihkan Kepada</h3>
                <p class="font-semibold text-slate-900"><?= htmlspecialchars($servis['nama_pemilik']); ?></p>
                <p class="text-sm text-slate-600"><?= htmlspecialchars($servis['telepon']); ?></p>
            </div>
            <div>
                <h3 class="text-xs font-bold text-slate-500 uppercase mb-2">Kendaraan</h3>
                <p class="font-semibold text-slate-900"><?= htmlspecialchars($servis['nomor_polisi']); ?></p>
                <p class="text-sm text-slate-600"><?= htmlspecialchars($servis['tipe_kendaraan']); ?></p>
            </div>
        </div>

        <!-- Detail servis -->
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
                        <div class="font-semibold text-slate-900"><?= htmlspecialchars($servis['nama_paket'] ?? $servis['jenis_servis']); ?></div>
                        <div class="text-xs text-slate-500 mt-1"><?= htmlspecialchars($servis['deskripsi_paket'] ?? ''); ?></div>
                        <?php if ($servis['keluhan']): ?>
                            <div class="text-xs text-slate-500 mt-1 italic">Catatan: <?= htmlspecialchars($servis['keluhan']); ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="py-3 text-right font-semibold text-slate-900">
                        Rp <?= number_format($servis['estimasi_biaya'] ?? 0, 0, ',', '.'); ?>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="border-t-2 border-slate-300">
                    <td class="py-2 text-right font-semibold text-slate-700">Estimasi Biaya:</td>
                    <td class="py-2 text-right font-semibold text-slate-900">
                        Rp <?= number_format($servis['estimasi_biaya'] ?? 0, 0, ',', '.'); ?>
                    </td>
                </tr>
                <?php if ($servis['biaya_final']): ?>
                <tr>
                    <td class="py-2 text-right font-bold text-slate-800 text-lg">TOTAL AKHIR:</td>
                    <td class="py-2 text-right font-extrabold text-red-600 text-lg">
                        Rp <?= number_format($servis['biaya_final'], 0, ',', '.'); ?>
                    </td>
                </tr>
                <?php endif; ?>
            </tfoot>
        </table>

        <!-- Status & teknisi -->
        <div class="grid grid-cols-2 gap-6 mb-8 pt-4 border-t border-slate-200">
            <div>
                <h3 class="text-xs font-bold text-slate-500 uppercase mb-2">Teknisi</h3>
                <?php if ($servis['nama_teknisi']): ?>
                    <p class="font-semibold text-slate-900"><?= htmlspecialchars($servis['nama_teknisi']); ?></p>
                    <p class="text-sm text-slate-600"><?= htmlspecialchars($servis['keahlian_teknisi'] ?? '-'); ?></p>
                <?php else: ?>
                    <p class="text-sm text-slate-400 italic">Belum ditentukan</p>
                <?php endif; ?>
            </div>
            <div>
                <h3 class="text-xs font-bold text-slate-500 uppercase mb-2">Status Servis</h3>
                <?php
                $statusWarna = [
                    'Pendaftaran' => 'bg-amber-100 text-amber-800',
                    'Dikerjakan' => 'bg-blue-100 text-blue-800',
                    'Selesai' => 'bg-emerald-100 text-emerald-800',
                ][$servis['status']] ?? 'bg-slate-100 text-slate-800';
                ?>
                <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full <?= $statusWarna; ?>">
                    <?= $servis['status']; ?>
                </span>
            </div>
        </div>

        <!-- Tanda tangan & catatan -->
        <div class="grid grid-cols-2 gap-6 pt-6 border-t border-slate-200">
            <div>
                <p class="text-xs text-slate-500 mb-16">Catatan: Invoice ini adalah bukti resmi servis di PT Veter Otomotif. Simpan untuk keperluan garansi.</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-slate-500 mb-16">Hormat kami,</p>
                <p class="font-semibold text-slate-900 border-t border-slate-300 pt-1 inline-block">PT Veter Otomotif</p>
            </div>
        </div>

    </div>

</body>
</html>