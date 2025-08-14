<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\AssetClassController;
use App\Http\Controllers\AssetSubClassController;
use App\Http\Controllers\AssetNameController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\RegisterAssetController;
use App\Http\Controllers\TransferAssetController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CompanyUserController;
use App\Http\Controllers\DepreciationController;

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
    //Start Asset
    Route::resource('asset-class', AssetClassController::class);
    Route::resource('asset-sub-class', AssetSubClassController::class);
    Route::resource('asset-name', AssetNameController::class);
    Route::resource('asset', AssetController::class);
    //Start Depre
    Route::post('/asset/depre/{asset}', [DepreciationController::class, 'depre'])->name('depreciation.depre');
    Route::resource('depreciation', DepreciationController::class);

    Route::resource('location', LocationController::class);
    Route::resource('department', DepartmentController::class);
    //Start Register
    Route::resource('register-asset', RegisterAssetController::class);
    Route::post('/register-asset/{register_asset}/approve', [RegisterAssetController::class, 'approve'])->name('register-asset.approve');

    Route::resource('transfer-asset', TransferAssetController::class);
    Route::resource('company-user', CompanyUserController::class);
    //COMPANY
    Route::resource('company', CompanyController::class);
    Route::post('/company/switch', [CompanyController::class, 'switch'])->name('company.switch');

    // --- Import Data ---
    Route::get('/asset-class/import', [AssetClassController::class, 'showImportForm'])->name('asset-class.import.form');
    Route::post('/asset-class/import', [AssetClassController::class, 'importExcel'])->name('asset-class.import');

    Route::get('/asset-sub-class/import', [AssetSubClassController::class, 'showImportForm'])->name('asset-sub-class.import.form');
    Route::post('/asset-sub-class/import', [AssetSubClassController::class, 'importExcel'])->name('asset-sub-class.import');

    Route::get('/asset-name/import', [AssetNameController::class, 'showImportForm'])->name('asset-name.import.form');
    Route::post('/asset-name/import', [AssetNameController::class, 'importExcel'])->name('asset-name.import');

    // --- API Data ---
    Route::get('api/asset-name', [AssetNameController::class, 'datatables'])->name('api.asset-name');
    Route::get('api/asset-sub-class', [AssetSubClassController::class, 'datatables'])->name('api.asset-sub-class');
    Route::get('api/asset-class', [AssetClassController::class, 'datatables'])->name('api.asset-class');
    Route::get('api/location', [LocationController::class, 'datatables'])->name('api.location');
});