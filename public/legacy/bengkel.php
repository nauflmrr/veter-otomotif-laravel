<?php
require_once 'lib.php';

$pesan = '';
$error = '';

// Ambil data master untuk dropdown
$service_types = $pdo->query("SELECT * FROM service_types ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
$technicians = $pdo->query("SELECT * FROM technicians WHERE aktif = 1 ORDER BY nama")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_servis'])) {
    $nama = trim($_POST['nama_pemilik']);
    $telepon = trim($_POST['telepon']);
    $nopol = trim($_POST['nomor_polisi']);
    $tipe = trim($_POST['tipe_kendaraan']);
    $service_type_id = (int) $_POST['service_type_id'];
    $technician_id = (int) $_POST['technician_id'];
    $tanggal = $_POST['tanggal_servis'];
    $keluhan = trim($_POST['keluhan']);
    $user_id = $_SESSION['user_id'] ?? null;

    // Ambil nama & estimasi biaya dari service_types (server-side, biar gak bisa dimanipulasi)
    $stmt = $pdo->prepare("SELECT nama, estimasi_biaya FROM service_types WHERE id = ?");
    $stmt->execute([$service_type_id]);
    $st = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$st) {
        $error = "Paket servis tidak valid.";
    } else {
        $jenis = $st['nama'];
        $estimasi = $st['estimasi_biaya'];

        $stmt = $pdo->prepare("INSERT INTO services 
            (user_id, technician_id, service_type_id, estimasi_biaya, nama_pemilik, telepon, nomor_polisi, tipe_kendaraan, jenis_servis, tanggal_servis, keluhan) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$user_id, $technician_id, $service_type_id, $estimasi, $nama, $telepon, $nopol, $tipe, $jenis, $tanggal, $keluhan])) {
            $pesan = "Pendaftaran servis terkonfirmasi. Teknisi: " . htmlspecialchars($technicians[array_search($technician_id, array_column($technicians, 'id'))]['nama'] ?? '-') . ". Estimasi biaya: Rp " . number_format($estimasi, 0, ',', '.') . ". Silakan datang sesuai jadwal.";
        } else {
            $error = "Gagal menyimpan booking. Coba lagi.";
        }
    }
}

renderHeader("Layanan Bengkel & Servis");
?>

<div class="max-w-2xl mx-auto bg-white p-8 rounded-2xl border border-slate-200 shadow-sm">
    <h2 class="text-2xl font-bold text-slate-900 mb-2">Penjadwalan Servis Kendaraan</h2>
    <p class="text-slate-500 text-sm mb-6">Booking antrean perbaikan berkala dan penggantian suku cadang tanpa antre.</p>

    <?php if ($pesan): ?>
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">
            <?= htmlspecialchars($pesan); ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
            <?= htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Pemilik</label>
                <input type="text" name="nama_pemilik" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor WhatsApp</label>
                <input type="tel" name="telepon" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-red-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Polisi (Plat)</label>
                <input type="text" name="nomor_polisi" placeholder="B 1234 ABC" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Model / Tipe Mobil</label>
                <input type="text" name="tipe_kendaraan" placeholder="Fortuner 2022" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-red-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Paket Servis</label>
                <select name="service_type_id" id="service_type_id" required 
                    class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-red-500"
                    onchange="updateEstimasi()">
                    <option value="">-- Pilih Paket Servis --</option>
                    <?php foreach ($service_types as $st): ?>
                        <option value="<?= $st['id']; ?>" data-biaya="<?= $st['estimasi_biaya']; ?>">
                            <?= htmlspecialchars($st['nama']); ?> — Rp <?= number_format($st['estimasi_biaya'], 0, ',', '.'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Teknisi</label>
                <select name="technician_id" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">-- Pilih Teknisi --</option>
                    <?php foreach ($technicians as $t): ?>
                        <option value="<?= $t['id']; ?>">
                            <?= htmlspecialchars($t['nama']); ?><?= $t['keahlian'] ? ' — ' . htmlspecialchars($t['keahlian']) : ''; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Estimasi biaya otomatis -->
        <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg">
            <div class="flex justify-between items-center">
                <span class="text-sm font-semibold text-slate-700">Estimasi Biaya:</span>
                <span id="estimasi_display" class="text-lg font-bold text-red-600">Rp 0</span>
            </div>
            <p class="text-xs text-slate-500 mt-1">*Estimasi bisa berubah setelah pemeriksaan langsung oleh teknisi.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Rencana Servis</label>
                <input type="date" name="tanggal_servis" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-red-500">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan / Keluhan Tambahan</label>
            <textarea name="keluhan" rows="3" class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-red-500" placeholder="Deskripsikan kendala mesin atau kebutuhan perbaikan"></textarea>
        </div>

        <button type="submit" name="submit_servis" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition shadow-md">Konfirmasi Booking Bengkel</button>
    </form>
</div>

<script>
function updateEstimasi() {
    const select = document.getElementById('service_type_id');
    const opt = select.options[select.selectedIndex];
    const biaya = opt.getAttribute('data-biaya') || 0;
    const display = document.getElementById('estimasi_display');
    display.textContent = 'Rp ' + parseInt(biaya).toLocaleString('id-ID');
}
</script>

<?php renderFooter(); ?>