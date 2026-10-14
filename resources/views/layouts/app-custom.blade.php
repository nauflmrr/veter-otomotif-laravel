<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'PT Veter Otomotif') | Dealer Mobil</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 flex flex-col min-h-screen">

    {{-- Navbar --}}
    <header class="bg-slate-900 text-white sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="/" class="text-xl font-bold tracking-wider text-red-500">
                VETER <span class="text-white">OTOMOTIF</span>
            </a>
            <nav class="flex space-x-6 text-sm font-medium items-center">
                <a href="/" class="hover:text-red-400">Beranda</a>
                <a href="/bengkel" class="hover:text-red-400">Booking Servis</a>
                <a href="/legacy/dealer.php" class="hover:text-red-400">Katalog Mobil</a>
                <a href="/legacy/garage.php" class="hover:text-red-400">Simulasi Kredit</a>

                @auth
                    @if(auth()->user()->role === 'admin')
                        <a href="/dashboard" class="text-yellow-400 hover:text-yellow-300">Dashboard Admin</a>
                    @else
                        <a href="/riwayat" class="hover:text-red-400">Riwayat Servis</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button class="text-red-400 hover:text-red-300">Keluar</button>
                    </form>
                @else
                    <a href="/login" class="bg-red-600 px-3 py-1 rounded text-white hover:bg-red-700">Masuk</a>
                @endauth
            </nav>
        </div>
    </header>

    {{-- Content --}}
    <main class="flex-grow max-w-7xl mx-auto px-4 py-8 w-full">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-slate-900 text-slate-400 py-6 border-t border-slate-800 text-center text-sm">
        <div class="max-w-7xl mx-auto px-4">
            <p>&copy; 2026 PT Veter Otomotif. Seluruh hak cipta dilindungi.</p>
            <p class="text-xs text-slate-500 mt-1">Jl Hasan No 98, Kebon Nanas, Jakarta Barat | Telp: 021-10090</p>
        </div>
    </footer>

</body>
</html>