<?php

use App\Http\Controllers\QRCodeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('qrcode.index');
});

Route::get('/qrcode', [QRCodeController::class, 'index'])->name('qrcode.index');
Route::post('/qrcode/generate', [QRCodeController::class, 'generate'])->name('qrcode.generate');
Route::get('/qrcode/{id}/download', [QRCodeController::class, 'download'])->name('qrcode.download');
