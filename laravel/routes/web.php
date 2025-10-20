<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MusicController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Admin\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ============================================
// Public Routes
// ============================================

// Landing Page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication Routes
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');

    // Register
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

    // Password Reset
    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

// ============================================
// Protected Routes (Require Authentication)
// ============================================

Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Music Entries (Resource Routes)
    Route::resource('music', MusicController::class);
    Route::post('/music/{id}/toggle-favorite', [MusicController::class, 'toggleFavorite'])->name('music.toggle-favorite');

    // Playlists (Form-based routes matching custom PHP)
    Route::get('/playlists', [PlaylistController::class, 'index'])->name('playlists.index');
    Route::get('/playlists/create', [PlaylistController::class, 'create'])->name('playlists.create');
    Route::post('/playlists/create', [PlaylistController::class, 'store'])->name('playlists.store');
    Route::get('/playlists/{id}', [PlaylistController::class, 'show'])->name('playlists.show');
    Route::get('/playlists/{id}/edit', [PlaylistController::class, 'edit'])->name('playlists.edit');
    Route::post('/playlists/{id}/edit', [PlaylistController::class, 'update'])->name('playlists.update');
    Route::post('/playlists/{id}/delete', [PlaylistController::class, 'destroy'])->name('playlists.destroy');
    
    // Playlist AJAX/API Routes (form data or JSON) - MUST be authenticated
    Route::post('/playlists/add-track', [PlaylistController::class, 'addTrack'])->name('playlists.add-track');
    Route::post('/playlists/remove-track', [PlaylistController::class, 'removeTrack'])->name('playlists.remove-track');
});

// ============================================
// Admin Routes (Require Admin Role)
// ============================================

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // User Management
    Route::get('/users', [AdminController::class, 'userList'])->name('users');
    Route::get('/users/{id}', [AdminController::class, 'userDetail'])->name('users.detail');
    Route::get('/users/{id}/music', [AdminController::class, 'userMusic'])->name('users.music');
    Route::post('/users/update', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');

    // Password Reset Approval
    Route::post('/reset-requests/approve', [AdminController::class, 'approveResetRequest'])->name('reset.approve');
    Route::post('/users/{id}/reset-password', [AdminController::class, 'resetUserPassword'])->name('users.reset-password');

    // System Management
    Route::get('/system-health', [AdminController::class, 'systemHealth'])->name('system.health');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings/update', [AdminController::class, 'updateSettings'])->name('settings.update');
});
