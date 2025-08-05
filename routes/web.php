<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\AssetClassController;
use App\Http\Controllers\AssetSubClassController;
use App\Http\Controllers\AssetNameController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\RegisterAssetController;
use App\Http\Controllers\TransferAssetController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CompanyUserController;

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

//======================================================================
// PUBLIC & GUEST ROUTES
//======================================================================

// Welcome Page
Route::get('/', function () {
    return view('welcome');
});

// Google Authentication Routes
Route::controller(GoogleController::class)->group(function () {
    Route::get('/auth/google/redirect', 'redirectToGoogle')->name('auth.google');
    Route::get('/auth/google/callback', 'handleGoogleCallback');
});


//======================================================================
// STANDARD AUTHENTICATION ROUTES (Login, Register, etc.)
//======================================================================

require __DIR__.'/auth.php';


//======================================================================
// AUTHENTICATED APPLICATION ROUTES
//======================================================================

Route::middleware(['auth', 'verified'])->group(function () {

    // --- Dashboard ---
    Route::get('/dashboard', function () {
        return view('index');
    })->name('dashboard');

    // --- User Profile ---
    Route::controller(ProfileController::class)->name('profile.')->group(function () {
        Route::get('/profile', 'edit')->name('edit');
        Route::patch('/profile', 'update')->name('update');
        Route::delete('/profile', 'destroy')->name('destroy');
    });

    // --- User Onboarding ---
    // Routes for new users to create their first company.
    Route::controller(UserController::class)->prefix('onboard')->name('onboard.')->group(function () {
        Route::get('/', 'onboard')->name('index');
        Route::get('/create', 'createCompany')->name('create');
        Route::post('/store', 'storeCompany')->name('store');
    });

    // --- Core Application Resources ---
    // All the main CRUD functionalities for asset management.
    Route::resource('asset-class', AssetClassController::class);
    Route::resource('asset-sub-class', AssetSubClassController::class);
    Route::resource('asset-name', AssetNameController::class);
    Route::resource('location', LocationController::class);
    Route::resource('department', DepartmentController::class);
    Route::resource('register-asset', RegisterAssetController::class);
    Route::resource('transfer-asset', TransferAssetController::class);
    Route::resource('company-user', CompanyUserController::class);
    //COMPANY
    Route::resource('company', CompanyController::class);
    Route::post('/company/switch', [App\Http\Controllers\CompanyController::class, 'switch'])->name('company.switch');

});