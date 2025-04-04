<?php

use Azuriom\Plugin\DiscordAuth\Controllers\DiscordAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your plugin. These
| routes are loaded by the RouteServiceProvider of your plugin within
| a group which contains the "web" middleware group and your plugin name
| as prefix. Now create something great!
|
*/

Route::get('/login', [DiscordAuthController::class, 'redirectToProvider'])->name('login');
Route::get('/callback', [DiscordAuthController::class, 'handleProviderCallback']);
Route::get('/username', [DiscordAuthController::class, 'username'])->name('username');
Route::post('/register-username', [DiscordAuthController::class, 'registerUsername'])->name('register-username');
