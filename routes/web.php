<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetLowValueController;
use App\Http\Controllers\AssetArrivalController;
use App\Http\Controllers\AssetClassController;
use App\Http\Controllers\AssetSubClassController;
use App\Http\Controllers\AssetNameController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\RegisterAssetController;
use App\Http\Controllers\TransferAssetController;
use App\Http\Controllers\DisposalAssetController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CompanyUserController;
use App\Http\Controllers\DepreciationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PersonInChargeController;
use App\Http\Controllers\InsuranceController;
use App\Http\Controllers\HistoryController;

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
    return view('auth.login');
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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

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
        Route::get('/create', 'createCompany')->name('create')->middleware('can:is-dev');
        Route::post('/store', 'storeCompany')->name('store')->middleware('can:is-dev');
        Route::get('/profile', 'editProfile')->name('edit');
        Route::patch('/profile', 'update')->name('update');
        Route::put('/profile/signature', 'updateSignature')->name('updateSignature');
        Route::delete('/profile', 'destroy')->name('destroy');
    });

    // --- Export Data ---
    Route::get('/asset-class/export-excel', [AssetClassController::class, 'exportExcel'])->name('asset-class.export');
    Route::get('/asset-sub-class/export-excel', [AssetSubClassController::class, 'exportExcel'])->name('asset-sub-class.export');
    Route::get('/asset-name/export-excel', [AssetNameController::class, 'exportExcel'])->name('asset-name.export');
    Route::get('/asset/export-excel', [AssetController::class, 'exportExcel'])->name('asset.export');
    Route::get('/assetLVA/export-excel', [AssetLowValueController::class, 'exportExcel'])->name('assetLVA.export');
    Route::get('/location/export-excel', [LocationController::class, 'exportExcel'])->name('location.export');
    Route::get('/department/export-excel', [DepartmentController::class, 'exportExcel'])->name('department.export');
    Route::get('/depreciation/export-excel', [DepreciationController::class, 'exportExcelCommercial'])->name('commercial.export');
    Route::get('/depreciation/fiscal/export-excel', [DepreciationController::class, 'exportExcelFiscal'])->name('fiscal.export');

    //Start Depre Commercial
    Route::post('/asset/depre/{asset}', [DepreciationController::class, 'depre'])->name('depreciation.depre');
    Route::post('/depreciation/run-all', [DepreciationController::class, 'runAll'])->name('depreciation.runAll');
    Route::get('/depreciation/status', [DepreciationController::class, 'getStatus'])->name('depreciation.status');
    Route::post('/depreciation/clear-status', [DepreciationController::class, 'clearStatus'])->name('depreciation.clearStatus');
    Route::get('/depreciation/stream', [DepreciationController::class, 'stream'])->name('depreciation.stream');
    //Start Depre Fiscal
    Route::get('/depreciation/fiscal', [DepreciationController::class, 'indexFiscal'])->name('depreciationFiscal.index');
    //Start Register
    Route::post('/register-asset/{register_asset}/approve', [RegisterAssetController::class, 'approve'])->name('register-asset.approve');
    Route::get('/register-asset/{register_asset}/export-pdf', [RegisterAssetController::class, 'exportPdf'])->name('register-asset.exportPdf');
    Route::get('/register-asset/trash', [RegisterAssetController::class, 'trash'])->name('register-asset.trash');
    Route::put('/register-asset/{register_asset}/restore', [RegisterAssetController::class, 'restore'])->name('register-asset.restore');
    //Start Transfer
    Route::post('/transfer-asset/{transfer_asset}/approve', [TransferAssetController::class, 'approve'])->name('transfer-asset.approve');
    Route::get('/transfer-asset/{transfer_asset}/export-pdf', [TransferAssetController::class, 'exportPdf'])->name('transfer-asset.exportPdf');
    Route::get('/transfer-asset/trash', [TransferAssetController::class, 'trash'])->name('transfer-asset.trash');
    Route::put('/transfer-asset/{transfer_asset}/restore', [TransferAssetController::class, 'restore'])->name('transfer-asset.restore');
    //Start Diposal
    Route::post('/disposal-asset/{disposal_asset}/approve', [DisposalAssetController::class, 'approve'])->name('disposal-asset.approve');
    Route::get('/disposal-asset/{disposal_asset}/export-pdf', [DisposalAssetController::class, 'exportPdf'])->name('disposal-asset.exportPdf');
    Route::get('/disposal-asset/trash', [DisposalAssetController::class, 'trash'])->name('disposal-asset.trash');
    Route::put('/disposal-asset/{disposal_asset}/restore', [DisposalAssetController::class, 'restore'])->name('disposal-asset.restore');
    //Start Company
    Route::post('/company/switch', [CompanyController::class, 'switch'])->name('company.switch');
    //Start Profile
    Route::put('/profile/signature', [ProfileController::class, 'updateSignature'])->name('profile.updateSignature');

    // --- Core Application Resources ---
    Route::resource('asset-class', AssetClassController::class);
    Route::resource('asset-sub-class', AssetSubClassController::class);
    Route::resource('asset-name', AssetNameController::class);
    Route::resource('asset', AssetController::class);
    Route::resource('assetLVA', AssetLowValueController::class);
    Route::resource('assetArrival', AssetArrivalController::class)->middleware('can:is-admin');
    Route::resource('depreciation', DepreciationController::class)->except('show');
    Route::resource('location', LocationController::class)->except('show');
    Route::resource('department', DepartmentController::class)->except('show');
    Route::resource('register-asset', RegisterAssetController::class);
    Route::resource('transfer-asset', TransferAssetController::class);
    Route::resource('disposal-asset', DisposalAssetController::class);
    Route::resource('company-user', CompanyUserController::class);
    Route::resource('company', CompanyController::class);
    Route::resource('person-in-charge', PersonInChargeController::class);
    Route::resource('insurance', InsuranceController::class);
    Route::resource('history', HistoryController::class);

    // --- Import Data ---
    Route::post('/asset-class/import', [AssetClassController::class, 'importExcel'])->name('asset-class.import');
    Route::post('/asset-sub-class/import', [AssetSubClassController::class, 'importExcel'])->name('asset-sub-class.import');
    Route::post('/asset-name/import', [AssetNameController::class, 'importExcel'])->name('asset-name.import');
    Route::post('/asset/import', [AssetController::class, 'importExcel'])->name('asset.import');
    Route::post('/assetLVA/import', [AssetLowValueController::class, 'importExcel'])->name('assetLVA.import');
    Route::post('/location/import', [LocationController::class, 'importExcel'])->name('location.import');
    Route::post('/department/import', [DepartmentController::class, 'importExcel'])->name('department.import');

    // --- API Data Datatables ---
    Route::get('api/asset', [AssetController::class, 'datatables'])->name('api.asset');
    Route::get('api/assetLVA', [AssetLowValueController::class, 'datatables'])->name('api.assetLVA');
    Route::get('api/assetArrival', [AssetArrivalController::class, 'datatables'])->name('api.assetArrival');
    Route::get('api/asset-name', [AssetNameController::class, 'datatables'])->name('api.asset-name');
    Route::get('api/asset-sub-class', [AssetSubClassController::class, 'datatables'])->name('api.asset-sub-class');
    Route::get('api/asset-class', [AssetClassController::class, 'datatables'])->name('api.asset-class');
    Route::get('api/location', [LocationController::class, 'datatables'])->name('api.location');
    Route::get('api/department', [DepartmentController::class, 'datatables'])->name('api.department');
    Route::get('api/company-user', [CompanyUserController::class, 'datatables'])->name('api.company-user');
    Route::get('api/register-asset', [RegisterAssetController::class, 'datatables'])->name('api.register-asset');
    Route::get('api/register-asset-canceled', [RegisterAssetController::class, 'datatablesCanceled'])->name('api.register-asset-canceled');
    Route::get('api/transfer-asset', [TransferAssetController::class, 'datatables'])->name('api.transfer-asset');
    Route::get('api/transfer-asset-canceled', [TransferAssetController::class, 'datatablesCanceled'])->name('api.transfer-asset-canceled');
    Route::get('api/disposal-asset', [DisposalAssetController::class, 'datatables'])->name('api.disposal-asset');
    Route::get('api/disposal-asset-canceled', [DisposalAssetController::class, 'datatablesCanceled'])->name('api.disposal-asset-canceled');
    Route::get('api/person-in-charge', [PersonInChargeController::class, 'datatables'])->name('api.person-in-charge');
    Route::get('api/insurance', [InsuranceController::class, 'datatables'])->name('api.insurance');
    Route::get('api/history', [HistoryController::class, 'datatables'])->name('api.history');

    Route::get('api/disposal-asset-find', [DisposalAssetController::class, 'datatablesAsset'])->name('api.disposal-asset-find');
    Route::get('api/company-users/search', [PersonInChargeController::class, 'search'])->name('api.company-users.search');
});