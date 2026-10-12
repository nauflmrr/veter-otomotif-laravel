<?php
require_once 'lib.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_nama = $_SESSION['user_nama'] ?? 'Pelanggan';

// Filter status dari URL
$filter = $_GET['filter'] ?? 'semua';
$allowed_filters = ['semua', 'Pendaftaran', 'Dikerjakan', 'Selesai'];
if (!in_array($filter, $allowed_filters)) {
    $filter = 'semua';
}

// Hitung jumlah servis pelanggan per status
$counts = [
    'semua' => 0,
    'Pendaftaran' => 0,
    'Dikerjakan' => 0,
    'Selesai' => 0,
];
$stmt = $pdo->prepare("SELECT status, COUNT(*) AS jml FROM services WHERE user_id = ? GROUP BY status");
$stmt->execute([$user_id]);
foreach ($stmt->fetchAll() as $row) {
    $counts[$row['status']] = (int) $row['jml'];
    $counts['semua'] += (int) $row['jml'];
}

// Query servis milik pelanggan ini
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
    WHERE s.user_id = ?
";
if ($filter !== 'semua') {
    $sql .= " AND s.status = ? ";
}
$sql .= " ORDER BY s.id DESC";

$stmt = $pdo->prepare($sql);
if ($filter !== 'semua') {
    $stmt->execute([$user_id, $filter]);
} else {
    $stmt->execute([$user_id]);
}
$services = $stmt->fetchAll();

renderHeader("Riwayat Servis Saya");
?>

<div class="max-w-5xl mx-auto">

    <div class="mb-8">
        <h2 class="text-3xl font-extrabold text-slate-900">Riwayat Servis Saya</h2>
        <p class="text-slate-500 text-sm">Halo <?= htmlspecialchars($user_nama); ?>, berikut daftar booking servis kendaraan kamu.</p>
    </div>

    <!-- Tab Filter -->
    <div class="flex flex-wrap gap-2 mb-6">
        <?php
        $tabs = [
            'semua' => ['label' => 'Semua', 'warna' => 'slate'],
            'Pendaftaran' => ['label' => 'Pendaftaran', 'warna' => 'amber'],
            'Dikerjakan' => ['label' => 'Dikerjakan', 'warna' => 'blue'],
            'Selesai' => ['label' => 'Selesai', 'warna' => 'emerald'],
        ];
        foreach ($tabs as $key => $tab):
            $isActive = ($filter === $key);
            $baseClass = "px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-2 ";
            if ($isActive) {
                $activeClass = [
                    'slate' => 'bg-slate-800 text-white shadow-md',
                    'amber' => 'bg-amber-500 text-white shadow-md',
                    'blue' => 'bg-blue-600 text-white shadow-md',
                    'emerald' => 'bg-emerald-600 text-white shadow-md',
                ][$tab['warna']];
                $finalClass = $baseClass . $activeClass;
            } else {
                $finalClass = $baseClass . "bg-white border border-slate-200 text-slate-600 hover:bg-slate-50";
            }
        ?>
            <a href="?filter=<?= urlencode($key); ?>" class="<?= $finalClass; ?>">
                <?= $tab['label']; ?>
                <span class="text-xs px-2 py-0.5 rounded-full <?= $isActive ? 'bg-white/25' : 'bg-slate-100 text-slate-600'; ?>">
                    <?= $counts[$key]; ?>
                </span>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Daftar servis -->
    <?php if (empty($services)): ?>
        <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
            <div class="text-5xl mb-3">🔧</div>
            <p class="text-slate-500 mb-4">
                <?= $filter === 'semua' 
                    ? 'Kamu belum pernah booking servis.' 
                    : 'Belum ada servis dengan status "' . htmlspecialchars($filter) . '".'; ?>
            </p>
            <?php if ($filter === 'semua'): ?>
                <a href="bengkel.php" class="inline-block bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-2.5 rounded-lg transition">
                    Booking Servis Sekarang
                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($services as $s): 
                $statusWarna = [
                    'Pendaftaran' => 'bg-amber-100 text-amber-800',
                    'Dikerjakan' => 'bg-blue-100 text-blue-800',
                    'Selesai' => 'bg-emerald-100 text-emerald-800',
                ][$s['status']] ?? 'bg-slate-100 text-slate-800';

                // Siapin nomor WA teknisi
                $no_wa_teknisi = '';
                if (!empty($s['telepon_teknisi'])) {
                    $no_wa_teknisi = preg_replace('/[^0-9]/', '', $s['telepon_teknisi']);
                    if (substr($no_wa_teknisi, 0, 1) === '0') {
                        $no_wa_teknisi = '62' . substr($no_wa_teknisi, 1);
                    }
                }
                $pesan_wa = "Halo " . ($s['nama_teknisi'] ?? 'Teknisi') . ", saya " . $s['nama_pemilik'] . ", mau tanya soal servis kendaraan " . $s['nomor_polisi'] . " (" . ($s['nama_paket'] ?? $s['jenis_servis']) . ") dengan status " . $s['status'] . ".";
            ?>
                <div class="bg-white rounded-2xl border border-slate-200 p-6 hover:shadow-md transition">
                    <div class="flex flex-wrap justify-between items-start gap-4 mb-4">
                        <div>
                            <div class="flex items-center gap-3 mb-1">
                                <h3 class="text-lg font-bold text-slate-900">
                                    <?= htmlspecialchars($s['nomor_polisi']); ?>
                                </h3>
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full <?= $statusWarna; ?>">
                                    <?= $s['status']; ?>
                                </span>
                            </div>
                            <p class="text-sm text-slate-500">
                                <?= htmlspecialchars($s['tipe_kendaraan']); ?> · 
                                <?= date('d M Y', strtotime($s['tanggal_servis'])); ?>
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-slate-400 uppercase font-semibold">Biaya</div>
                            <?php if ($s['biaya_final']): ?>
                                <div class="text-2xl font-extrabold text-emerald-600">
                                    Rp <?= number_format($s['biaya_final'], 0, ',', '.'); ?>
                                </div>
                                <div class="text-xs text-slate-400 line-through">
                                    Estimasi: Rp <?= number_format($s['estimasi_biaya'] ?? 0, 0, ',', '.'); ?>
                                </div>
                            <?php else: ?>
                                <div class="text-xl font-bold text-red-600">
                                    Rp <?= number_format($s['estimasi_biaya'] ?? 0, 0, ',', '.'); ?>
                                </div>
                                <div class="text-xs text-slate-400">Estimasi</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 pt-4 border-t border-slate-100">
                        <div>
                            <div class="text-xs text-slate-400 uppercase font-semibold mb-1">Paket Servis</div>
                            <div class="text-sm font-medium text-slate-800">
                                <?= htmlspecialchars($s['nama_paket'] ?? $s['jenis_servis']); ?>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-slate-400 uppercase font-semibold mb-1">Teknisi</div>
                            <div class="text-sm font-medium text-slate-800">
                                <?php if ($s['nama_teknisi']): ?>
                                    🔧 <?= htmlspecialchars($s['nama_teknisi']); ?>
                                    <span class="text-xs text-slate-400">(<?= htmlspecialchars($s['keahlian_teknisi'] ?? '-'); ?>)</span>
                                <?php else: ?>
                                    <span class="text-slate-400 italic">Belum ditentukan</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php if ($s['keluhan']): ?>
                        <div class="mb-4 p-3 bg-slate-50 rounded-lg">
                            <div class="text-xs text-slate-400 uppercase font-semibold mb-1">Keluhan</div>
                            <p class="text-sm text-slate-600"><?= htmlspecialchars($s['keluhan']); ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Tombol aksi -->
                    <div class="flex flex-wrap gap-2 pt-4 border-t border-slate-100">
                        <?php if ($no_wa_teknisi): ?>
                            <a href="https://wa.me/<?= $no_wa_teknisi; ?>?text=<?= urlencode($pesan_wa); ?>" 
                               target="_blank"
                               class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                                💬 Chat WA Teknisi
                            </a>
                        <?php endif; ?>

                        <a href="invoice.php?id=<?= $s['id']; ?>" 
                           target="_blank"
                           class="inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-800 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                            🧾 Lihat Invoice
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<?php renderFooter(); ?>