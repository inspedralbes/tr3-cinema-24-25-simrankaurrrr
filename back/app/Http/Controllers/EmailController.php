<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\HelloMail;
use Illuminate\Support\Facades\Mail;  // Aquí importamos Mail

class EmailController extends Controller
{
    public function sendWelcomeEmail(){
$toEmail='gestioncine0@gmail.com';
$message ='welcome';

Mail::to($toEmail)->send(new HelloMail($message));
    }
}
