<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CertificateController;

// Public routes
Route::get('/certificates/{certificateNumber}', [CertificateController::class, 'show'])->name('certificates.show');
Route::get('/certificates/{certificateNumber}/download-qr', [CertificateController::class, 'downloadQrCode'])->name('certificates.downloadQr');


    Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates.index');
    Route::get('/certificates/create', [CertificateController::class, 'create'])->name('certificates.create');
    Route::post('/certificates', [CertificateController::class, 'store'])->name('certificates.store');


