<?php

use App\Livewire\About;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Profile;
use App\Livewire\Auth\Register;
use App\Livewire\ChooseGrade;
use App\Livewire\Home;
use App\Livewire\Play;
use Illuminate\Support\Facades\Route;

Route::get('/', About::class)->name('welcome');
Route::get('/games', Home::class)->name('home');
Route::get('/play/{session}', Play::class)->name('play');

Route::middleware('guest')->group(function () {
    Route::get('/register', Register::class)->name('register');
    Route::get('/login', Login::class)->name('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', Profile::class)->name('profile');
    Route::get('/grade', ChooseGrade::class)->name('grade');
});
