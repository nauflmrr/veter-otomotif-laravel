<?php
require_once 'lib.php';

// Kalau udah login, redirect ke beranda
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$sukses = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $konfirmasi = $_POST['konfirmasi'];

    // Validasi
    if (empty($nama) || empty($email) || empty($password)) {
        $error = "Semua field wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter.";
    } elseif ($password !== $konfirmasi) {
        $error = "Password dan konfirmasi tidak cocok.";
    } else {
        // Cek email udah terdaftar atau belum
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $error = "Email sudah terdaftar. Silakan login atau gunakan email lain.";
        } else {
            // Insert user baru dengan role 'customer'
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, 'customer')");
            
            if ($stmt->execute([$nama, $email, $hash])) {
                $sukses = "Akun berhasil dibuat! Silakan login.";
                $nama = $email = '';
            } else {
                $error = "Gagal membuat akun. Coba lagi.";
            }
        }
    }
}

renderHeader("Daftar Akun");
?>

<div class="max-w-md mx-auto bg-white p-8 rounded-2xl border border-slate-200 shadow-sm">
    <h2 class="text-2xl font-bold text-slate-900 mb-2">Daftar Akun Pelanggan</h2>
    <p class="text-slate-500 text-sm mb-6">Buat akun untuk booking servis & test drive.</p>

    <?php if ($error): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
            <?= htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if ($sukses): ?>
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">
            <?= htmlspecialchars($sukses); ?>
            <a href="login.php" class="font-semibold underline ml-1">Login sekarang →</a>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($nama ?? ''); ?>" required 
                class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-red-500">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($email ?? ''); ?>" required 
                class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-red-500">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Password</label>
            <input type="password" name="password" required minlength="6"
                class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-red-500">
            <p class="text-xs text-slate-400 mt-1">Minimal 6 karakter.</p>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Konfirmasi Password</label>
            <input type="password" name="konfirmasi" required minlength="6"
                class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-red-500">
        </div>

        <button type="submit" name="register" 
            class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition shadow-md">
            Daftar Sekarang
        </button>
    </form>

    <p class="text-center text-sm text-slate-500 mt-6">
        Sudah punya akun? 
        <a href="login.php" class="text-red-600 font-semibold hover:underline">Login di sini</a>
    </p>
</div>

<?php renderFooter(); ?>