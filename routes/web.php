<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| This file defines all web routes for the application. Each route maps a URL
| to a controller action. Routes are grouped and protected by middleware as needed.
| Use this file to manage navigation and access control for the web interface.
*/

// Redirect the root URL to the login page for unauthenticated users
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes: show login form, handle login, and logout
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes: only accessible to authenticated users via ApiAuthentication middleware
Route::middleware([\App\Http\Middleware\ApiAuthentication::class])->group(function () {
    // Dashboard route: main admin panel landing page
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // User management: CRUD operations for users
    Route::resource('users', UserController::class);
    
    // Role management: CRUD operations for roles
    Route::resource('roles', RoleController::class);
    
    // Division management: CRUD operations for divisions
    Route::resource('divisions', DivisionController::class);
    
    // Position management: CRUD operations for positions
    Route::resource('positions', PositionController::class);
    
    // Profile: view and update authenticated user's profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
});