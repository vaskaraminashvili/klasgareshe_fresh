<?php

use App\Livewire\Home;
use App\Livewire\Play;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');
Route::get('/play/{session}', Play::class)->name('play');
