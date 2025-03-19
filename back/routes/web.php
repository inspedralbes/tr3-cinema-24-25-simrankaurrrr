<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Mail\HelloMail;
use App\Http\Controllers\EmailController;  

Route::get('/', function () {
    return view('welcome');
});

Route::get('/mail', [EmailController::class, 'sendWelcomeEmail'] );