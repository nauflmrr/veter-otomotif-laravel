<?php
require_once 'lib.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Handle update biaya_final + status
$pesan = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_servis'])) {
    $id = (int) $_POST['service_id'];
    $biaya_final = $_POST['biaya_final'] !== '' ? (int) $_POST['biaya_final'] : null;
    $status = $_POST['status'];
    $allowed_status = ['Pendaftaran', 'Dikerjakan', 'Selesai'];

    if (in_array($status, $allowed_status)) {
        $stmt = $pdo->prepare("UPDATE services SET biaya_final = ?, status = ? WHERE id = ?");
        if ($stmt->execute([$biaya_final, $status, $id])) {
            $pesan = "Data servis #$id berhasil diupdate.";
        }
    }
}

// Filter status dari URL
$filter = $_GET['filter'] ?? 'semua';
$allowed_filters = ['semua', 'Pendaftaran', 'Dikerjakan', 'Selesai'];
if (!in_array($filter, $allowed_filters)) {
    $filter = 'semua';
}

$testDrives = $pdo->query("SELECT t.*, c.nama_mobil FROM test_drive t LEFT JOIN cars c ON t.car_id = c.id ORDER BY t.id DESC")->fetchAll();

// Hitung jumlah per status (buat badge di tab)
$counts = [
    'semua' => 0,
    'Pendaftaran' => 0,
    'Dikerjakan' => 0,
    'Selesai' => 0,
];
$countRows = $pdo->query("SELECT status, COUNT(*) AS jml FROM services GROUP BY status")->fetchAll();
foreach ($countRows as $row) {
    $counts[$row['status']] = (int) $row['jml'];
    $counts['semua'] += (int) $row['jml'];
}

// Query servis dengan filter
$sql = "
    SELECT s.*, 
           t.nama AS nama_teknisi, 
           t.keahlian AS keahlian_teknisi,
           st.nama AS nama_paket
    FROM services s
    LEFT JOIN technicians t ON s.technician_id = t.id
    LEFT JOIN service_types st ON s.service_type_id = st.id
";
if ($filter !== 'semua') {
    $sql .= " WHERE s.status = ? ";
}
$sql .= " ORDER BY s.id DESC";

$stmt = $pdo->prepare($sql);
if ($filter !== 'semua') {
    $stmt->execute([$filter]);
} else {
    $stmt->execute();
}
$services = $stmt->fetchAll();

renderHeader("Admin Dashboard");
?>

<div class="mb-8">
    <h2 class="text-3xl font-extrabold text-slate-900">Dashboard Pusat Kontrol Admin</h2>
    <p class="text-slate-500 text-sm">Monitoring permohonan test drive dan antrean perbaikan unit bengkel.</p>
</div>

<?php if ($pesan): ?>
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">
        <?= htmlspecialchars($pesan); ?>
    </div>
<?php endif; ?>

<div class="mb-12">
    <h3 class="text-lg font-bold text-slate-800 mb-4 border-l-4 border-red-600 pl-3">Jadwal Masuk Test Drive</h3>
    <div class="overflow-x-auto bg-white rounded-xl border border-slate-200">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 text-slate-800 font-semibold border-b">
                <tr>
                    <th class="p-3">Nama</th>
                    <th class="p-3">WhatsApp</th>
                    <th class="p-3">Unit Mobil</th>
                    <th class="p-3">Jadwal</th>
                    <th class="p-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($testDrives)): ?>
                    <tr><td colspan="5" class="p-4 text-center text-slate-400">Belum ada booking test drive.</td></tr>
                <?php else: foreach ($testDrives as $t): ?>
                    <tr>
                        <td class="p-3 font-medium text-slate-900"><?= htmlspecialchars($t['nama_lengkap']); ?></td>
                        <td class="p-3"><?= htmlspecialchars($t['telepon']); ?></td>
                        <td class="p-3"><?= htmlspecialchars($t['nama_mobil'] ?? '-'); ?></td>
                        <td class="p-3"><?= $t['tanggal'] . ' (' . substr($t['jam'], 0, 5) . ')'; ?></td>
                        <td class="p-3"><span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800"><?= $t['status']; ?></span></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div>
    <h3 class="text-lg font-bold text-slate-800 mb-4 border-l-4 border-slate-800 pl-3">Antrean Servis Bengkel</h3>

    <!-- Tab Filter -->
    <div class="flex flex-wrap gap-2 mb-4">
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

    <div class="overflow-x-auto bg-white rounded-xl border border-slate-200">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 text-slate-800 font-semibold border-b">
                <tr>
                    <th class="p-3">Pemilik</th>
                    <th class="p-3">Plat / Model</th>
                    <th class="p-3">Paket Servis</th>
                    <th class="p-3">Teknisi</th>
                    <th class="p-3">Estimasi</th>
                    <th class="p-3">Biaya Final</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($services)): ?>
                    <tr><td colspan="8" class="p-4 text-center text-slate-400">Belum ada servis dengan status "<?= htmlspecialchars($filter); ?>".</td></tr>
                <?php else: foreach ($services as $s): 
                    $statusWarna = [
                        'Pendaftaran' => 'bg-amber-100 text-amber-800',
                        'Dikerjakan' => 'bg-blue-100 text-blue-800',
                        'Selesai' => 'bg-emerald-100 text-emerald-800',
                    ][$s['status']] ?? 'bg-slate-100 text-slate-800';
                ?>
                    <tr class="align-top">
                        <td class="p-3">
                            <div class="font-medium text-slate-900"><?= htmlspecialchars($s['nama_pemilik']); ?></div>
                            <div class="text-xs text-slate-400"><?= htmlspecialchars($s['telepon']); ?></div>
                        </td>
                        <td class="p-3">
                            <div><?= htmlspecialchars($s['nomor_polisi']); ?></div>
                            <div class="text-xs text-slate-400"><?= htmlspecialchars($s['tipe_kendaraan']); ?></div>
                        </td>
                        <td class="p-3">
                            <div><?= htmlspecialchars($s['nama_paket'] ?? $s['jenis_servis']); ?></div>
                            <div class="text-xs text-slate-400"><?= $s['tanggal_servis']; ?></div>
                        </td>
                        <td class="p-3">
                            <?php if ($s['nama_teknisi']): ?>
                                <div class="font-medium text-slate-900">🔧 <?= htmlspecialchars($s['nama_teknisi']); ?></div>
                                <div class="text-xs text-slate-400"><?= htmlspecialchars($s['keahlian_teknisi'] ?? '-'); ?></div>
                            <?php else: ?>
                                <span class="text-xs text-slate-400 italic">Belum dipilih</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-3 font-semibold text-red-600">
                            Rp <?= number_format($s['estimasi_biaya'] ?? 0, 0, ',', '.'); ?>
                        </td>
                        <td class="p-3">
                            <form method="POST" class="space-y-2">
                                <input type="hidden" name="service_id" value="<?= $s['id']; ?>">
                                <input type="number" name="biaya_final" 
                                    value="<?= $s['biaya_final'] ?? ''; ?>" 
                                    placeholder="0"
                                    class="w-28 px-2 py-1 border rounded text-xs outline-none focus:ring-2 focus:ring-red-500">
                        </td>
                        <td class="p-3">
                                <select name="status" class="px-2 py-1 border rounded text-xs outline-none focus:ring-2 focus:ring-red-500">
                                    <?php foreach (['Pendaftaran', 'Dikerjakan', 'Selesai'] as $opt): ?>
                                        <option value="<?= $opt; ?>" <?= $s['status'] === $opt ? 'selected' : ''; ?>>
                                            <?= $opt; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="mt-1">
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full <?= $statusWarna; ?>">
                                        <?= $s['status']; ?>
                                    </span>
                                </div>
                        </td>
                        <td class="p-3">
        <div class="space-y-2">
            <button type="submit" name="update_servis" 
                class="w-full bg-red-600 hover:bg-red-700 text-white text-xs font-semibold px-3 py-1.5 rounded transition">
                Update
            </button>
    </form>

    <!-- Tombol WA ke pelanggan -->
    <?php 
        $no_wa = preg_replace('/[^0-9]/', '', $s['telepon']);
        if (substr($no_wa, 0, 1) === '0') {
            $no_wa = '62' . substr($no_wa, 1);
        }
        $pesan_wa = "Halo " . $s['nama_pemilik'] . ", servis kendaraan " . $s['nomor_polisi'] . " (" . ($s['nama_paket'] ?? $s['jenis_servis']) . ") dengan status " . $s['status'] . ". Ada yang bisa kami bantu?";
    ?>
    <a href="https://wa.me/<?= $no_wa; ?>?text=<?= urlencode($pesan_wa); ?>" 
       target="_blank"
       class="block w-full text-center bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-3 py-1.5 rounded transition">
        💬 Chat WA
    </a>

    <!-- Tombol Invoice -->
    <a href="invoice.php?id=<?= $s['id']; ?>" 
       target="_blank"
       class="block w-full text-center bg-slate-700 hover:bg-slate-800 text-white text-xs font-semibold px-3 py-1.5 rounded transition">
        🧾 Invoice
    </a>
        </div>
</td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php renderFooter(); ?>