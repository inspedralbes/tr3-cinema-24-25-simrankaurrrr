<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Mail\HelloMail;
use App\Http\Controllers\PagosController;  

Route::get('/download-pdf/{pdfPath}', [PagosController::class, 'downloadPDF'])->name('download.pdf');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/verificar-qr', [PagosController::class, 'verificarQR'])->name('verificar.qr');

Route::get('/mail', [EmailController::class, 'sendWelcomeEmail'] );
