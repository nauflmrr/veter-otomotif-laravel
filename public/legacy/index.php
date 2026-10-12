<?php
require_once 'lib.php';

$pesan = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_testdrive'])) {
    $car_id = intval($_POST['car_id']);
    $nama = trim($_POST['nama_lengkap']);
    $telepon = trim($_POST['telepon']);
    $tanggal = $_POST['tanggal'];
    $jam = $_POST['jam'];
    $user_id = $_SESSION['user_id'] ?? null;

    $stmt = $pdo->prepare("INSERT INTO test_drive (user_id, car_id, nama_lengkap, telepon, tanggal, jam) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$user_id, $car_id, $nama, $telepon, $tanggal, $jam])) {
        $pesan = "Jadwal Test Drive berhasil dikirim. Tim sales kami akan segera menghubungi Anda.";
    }
}

$stmt = $pdo->query("SELECT * FROM cars ORDER BY id DESC");
$cars = $stmt->fetchAll();

renderHeader("Katalog Mobil");
?>

<div class="mb-8">
    <h2 class="text-3xl font-extrabold text-slate-900">Katalog Unit Kendaraan</h2>
    <p class="text-slate-500">Pilih armada favorit Anda dan jadwalkan uji kemudi (*test drive*) secara langsung.</p>
</div>

<?php if ($pesan): ?>
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">
        <?= htmlspecialchars($pesan); ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <?php foreach ($cars as $car): ?>
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm flex flex-col">
            <img src="<?= htmlspecialchars($car['gambar']); ?>" alt="<?= htmlspecialchars($car['nama_mobil']); ?>" class="w-full h-48 object-cover">
            <div class="p-5 flex-grow flex flex-col justify-between">
                <div>
                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 mb-2"><?= htmlspecialchars($car['kategori']); ?></span>
                    <h3 class="text-lg font-bold text-slate-900"><?= htmlspecialchars($car['nama_mobil']); ?></h3>
                    <p class="text-red-600 font-extrabold text-xl mt-1">Rp <?= number_format($car['harga'], 0, ',', '.'); ?></p>
                    <div class="mt-3 text-xs text-slate-500 space-y-1">
                        <p>Tahun: <?= $car['tahun']; ?> | Transmisi: <?= $car['transmisi']; ?></p>
                        <p>Bahan Bakar: <?= $car['bahan_bakar']; ?> | Stok: <?= $car['stok']; ?> Unit</p>
                    </div>
                    <p class="mt-3 text-slate-600 text-xs line-clamp-2"><?= htmlspecialchars($car['deskripsi']); ?></p>
                </div>
                <div class="mt-5">
                    <button onclick="bukaModal(<?= $car['id']; ?>, '<?= htmlspecialchars(addslashes($car['nama_mobil'])); ?>')" class="w-full text-center bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold py-2.5 rounded-lg transition">Booking Test Drive</button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div id="modalTestDrive" class="fixed inset-0 bg-slate-900/60 hidden items-center justify-center p-4 z-50">
    <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl relative">
        <h3 class="text-xl font-bold text-slate-900 mb-2">Formulir Booking Test Drive</h3>
        <p id="labelMobil" class="text-sm text-red-600 font-semibold mb-4"></p>
        <form method="POST" class="space-y-4">
            <input type="hidden" name="car_id" id="modalCarId">
            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-red-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Nomor WhatsApp</label>
                <input type="tel" name="telepon" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-red-500 outline-none">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-red-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Jam</label>
                    <input type="time" name="jam" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-red-500 outline-none">
                </div>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="button" onclick="tutupModal()" class="w-1/2 bg-slate-100 text-slate-700 py-2 rounded-lg text-sm font-semibold hover:bg-slate-200">Batal</button>
                <button type="submit" name="submit_testdrive" class="w-1/2 bg-red-600 text-white py-2 rounded-lg text-sm font-semibold hover:bg-red-700">Kirim Jadwal</button>
            </div>
        </form>
    </div>
</div>

<script>
function bukaModal(id, nama) {
    document.getElementById('modalCarId').value = id;
    document.getElementById('labelMobil').innerText = 'Unit: ' + nama;
    document.getElementById('modalTestDrive').classList.remove('hidden');
    document.getElementById('modalTestDrive').classList.add('flex');
}
function tutupModal() {
    document.getElementById('modalTestDrive').classList.remove('flex');
    document.getElementById('modalTestDrive').classList.add('hidden');
}
</script>

<?php renderFooter(); ?>