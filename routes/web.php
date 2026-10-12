<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BengkelController;  // ← TAMBAH DI SINI
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [\App\Http\Controllers\BengkelController::class, 'dashboard'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// ============ ROUTE KUSTOM VETER OTOMOTIF ============

// Modul Bengkel (Laravel proper)
Route::get('/bengkel', [BengkelController::class, 'index'])->name('bengkel.index');
Route::post('/bengkel', [BengkelController::class, 'store'])->name('bengkel.store');

// Wrapper PHP native (sementara)
Route::get('/legacy/{file}', function ($file) {
    $path = public_path('legacy/' . $file);
    if (!file_exists($path)) {
        abort(404);
    }
    ob_start();
    include $path;
    return ob_get_clean();
})->where('file', '.*');