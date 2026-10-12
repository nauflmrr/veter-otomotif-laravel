<?php
require_once 'lib.php';

// Kalau udah login, redirect ke beranda
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['user_role'] ?? '';
    header("Location: " . ($role === 'admin' ? 'admin.php' : 'index.php'));
    exit;
}

$error_login = '';
$error_register = '';
$sukses_register = '';
$active_tab = 'login'; // default tab

// ============ PROSES LOGIN ============
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $active_tab = 'login';
    $email = trim($_POST['login_email'] ?? '');
    $password = $_POST['login_password'] ?? '';

    if (empty($email) || empty($password)) {
        $error_login = "Email dan password wajib diisi.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nama'] = $user['nama'];
            $_SESSION['user_role'] = $user['role'];
            
            header("Location: " . ($user['role'] === 'admin' ? 'admin.php' : 'index.php'));
            exit;
        } else {
            $error_login = "Email atau password salah.";
        }
    }
}

// ============ PROSES REGISTER ============
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $active_tab = 'register';
    $nama = trim($_POST['reg_nama'] ?? '');
    $email = trim($_POST['reg_email'] ?? '');
    $password = $_POST['reg_password'] ?? '';
    $konfirmasi = $_POST['reg_konfirmasi'] ?? '';

    if (empty($nama) || empty($email) || empty($password)) {
        $error_register = "Semua field wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_register = "Format email tidak valid.";
    } elseif (strlen($password) < 6) {
        $error_register = "Password minimal 6 karakter.";
    } elseif ($password !== $konfirmasi) {
        $error_register = "Password dan konfirmasi tidak cocok.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $error_register = "Email sudah terdaftar. Silakan login.";
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, 'customer')");
            
            if ($stmt->execute([$nama, $email, $hash])) {
                $sukses_register = "Akun berhasil dibuat! Silakan login dengan akun baru kamu.";
                $active_tab = 'login'; // pindah ke tab login setelah sukses
                $nama = $email = '';
            } else {
                $error_register = "Gagal membuat akun. Coba lagi.";
            }
        }
    }
}

renderHeader("Masuk / Daftar");
?>

<div class="max-w-md mx-auto bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

    <!-- Tab -->
    <div class="flex border-b border-slate-200">
        <button type="button" id="tab-login" onclick="switchTab('login')"
            class="flex-1 py-4 text-sm font-bold transition">
            Masuk
        </button>
        <button type="button" id="tab-register" onclick="switchTab('register')"
            class="flex-1 py-4 text-sm font-bold transition">
            Daftar
        </button>
    </div>

    <!-- Panel LOGIN -->
    <div id="panel-login" class="p-8">
        <h2 class="text-2xl font-bold text-slate-900 mb-2">Selamat Datang Kembali</h2>
        <p class="text-slate-500 text-sm mb-6">Login untuk mengakses akun kamu.</p>

        <?php if ($sukses_register): ?>
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">
                <?= htmlspecialchars($sukses_register); ?>
            </div>
        <?php endif; ?>

        <?php if ($error_login): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
                <?= htmlspecialchars($error_login); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <input type="hidden" name="action" value="login">
            
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
                <input type="email" name="login_email" required 
                    class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Password</label>
                <input type="password" name="login_password" required 
                    class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <button type="submit" 
                class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition shadow-md">
                Masuk
            </button>
        </form>
    </div>

    <!-- Panel REGISTER -->
    <div id="panel-register" class="p-8 hidden">
        <h2 class="text-2xl font-bold text-slate-900 mb-2">Buat Akun Baru</h2>
        <p class="text-slate-500 text-sm mb-6">Daftar untuk booking servis & test drive.</p>

        <?php if ($error_register): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
                <?= htmlspecialchars($error_register); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <input type="hidden" name="action" value="register">

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" name="reg_nama" value="<?= htmlspecialchars($nama ?? ''); ?>" required 
                    class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
                <input type="email" name="reg_email" value="<?= htmlspecialchars($email ?? ''); ?>" required 
                    class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Password</label>
                <input type="password" name="reg_password" required minlength="6"
                    class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-red-500">
                <p class="text-xs text-slate-400 mt-1">Minimal 6 karakter.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Konfirmasi Password</label>
                <input type="password" name="reg_konfirmasi" required minlength="6"
                    class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <button type="submit" 
                class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition shadow-md">
                Daftar Sekarang
            </button>
        </form>
    </div>

</div>

<script>
const ACTIVE_TAB = '<?= $active_tab; ?>';

function switchTab(tab) {
    const panelLogin = document.getElementById('panel-login');
    const panelRegister = document.getElementById('panel-register');
    const tabLogin = document.getElementById('tab-login');
    const tabRegister = document.getElementById('tab-register');

    const activeClass = 'flex-1 py-4 text-sm font-bold transition text-red-600 border-b-2 border-red-600 bg-red-50/50';
    const inactiveClass = 'flex-1 py-4 text-sm font-bold transition text-slate-500 hover:text-slate-800 border-b-2 border-transparent';

    if (tab === 'login') {
        panelLogin.classList.remove('hidden');
        panelRegister.classList.add('hidden');
        tabLogin.className = activeClass;
        tabRegister.className = inactiveClass;
    } else {
        panelLogin.classList.add('hidden');
        panelRegister.classList.remove('hidden');
        tabLogin.className = inactiveClass;
        tabRegister.className = activeClass;
    }
}

// Aktifkan tab sesuai state saat halaman load
switchTab(ACTIVE_TAB);
</script>

<?php renderFooter(); ?>