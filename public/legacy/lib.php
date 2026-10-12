<?php
require_once 'config.php';

function renderHeader($title = "PT Veter Otomotif") {
    $isLoggedIn = isset($_SESSION['user_id']);
    $role = $_SESSION['user_role'] ?? '';
    echo '<!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . htmlspecialchars($title) . ' | PT Veter Otomotif</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-slate-50 text-slate-800 flex flex-col min-h-screen">
        <header class="bg-slate-900 text-white sticky top-0 z-50 shadow-md">
            <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
                <a href="index.php" class="text-xl font-bold tracking-wider text-red-500">VETER <span class="text-white">OTOMOTIF</span></a>
                <nav class="flex space-x-6 text-sm font-medium">
                    <a href="index.php" class="hover:text-red-400">Beranda</a>
                    <a href="dealer.php" class="hover:text-red-400">Katalog Mobil</a>
                    <a href="garage.php" class="hover:text-red-400">Simulasi Kredit</a>
                    <a href="bengkel.php" class="hover:text-red-400">Booking Bengkel</a>';
                    if ($isLoggedIn && $role === 'customer') {
                        echo '<a href="riwayat.php" class="hover:text-red-400">Riwayat Servis</a>';
                    }
                    if ($isLoggedIn && $role === 'admin') {
                        echo '<a href="admin.php" class="text-yellow-400 hover:text-yellow-300">Admin Panel</a>';
                    }
                    if ($isLoggedIn) {
                        echo '<a href="logout.php" class="text-red-400 hover:text-red-300">Keluar</a>';
                    } else {
                        echo '<a href="login.php" class="bg-red-600 px-3 py-1 rounded text-white hover:bg-red-700">Masuk</a>';
                    }
    echo '      </nav>
            </div>
        </header>
        <main class="flex-grow max-w-7xl mx-auto px-4 py-8 w-full">';
}

function renderFooter() {
    echo '</main>
        <footer class="bg-slate-900 text-slate-400 py-6 border-t border-slate-800 text-center text-sm">
            <div class="max-w-7xl mx-auto px-4">
                <p>&copy; 2026 PT Veter Otomotif. Seluruh hak cipta dilindungi.</p>
                <p class="text-xs text-slate-500 mt-1">Jl Hasan No 98, Kebon Nanas, Jakarta Barat | Telp: 021-10090</p>
            </div>
        </footer>
    </body>
    </html>';
}
?>