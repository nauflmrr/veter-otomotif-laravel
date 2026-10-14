<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BengkelController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/bengkel', [BengkelController::class, 'index'])->name('bengkel.index');
Route::post('/bengkel', [BengkelController::class, 'store'])->name('bengkel.store');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [BengkelController::class, 'dashboard'])->name('dashboard');
    Route::post('/dashboard/status/{id}', [BengkelController::class, 'updateStatus'])->name('servis.updateStatus');
    Route::get('/riwayat', [BengkelController::class, 'riwayat'])->name('riwayat');
    Route::get('/invoice/{id}', [BengkelController::class, 'invoice'])->name('invoice');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/legacy/{file}', function ($file) {
    $path = public_path('legacy/' . $file);
    if (!file_exists($path)) { abort(404); }
    ob_start();
    include $path;
    return ob_get_clean();
})->where('file', '.*');

require __DIR__.'/auth.php';