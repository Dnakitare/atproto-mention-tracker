<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KeywordController;
use App\Http\Controllers\NotificationSettingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\MentionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AlertController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/keyword/{keyword}', [DashboardController::class, 'keywordMentions'])->name('dashboard.keyword');
    
    // Keywords
    Route::resource('keywords', KeywordController::class);
    
    // Settings
    Route::get('/settings/notifications', [NotificationSettingController::class, 'edit'])->name('settings.notifications');
    Route::put('/settings/notifications', [NotificationSettingController::class, 'update'])->name('settings.notifications.update');

    // Mentions
    Route::get('/mentions', [MentionController::class, 'index'])->name('mentions.index');
    Route::get('/mentions/{mention}', [MentionController::class, 'show'])->name('mentions.show');
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('alerts', AlertController::class);
});