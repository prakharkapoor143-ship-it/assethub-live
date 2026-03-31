<?php

use App\Http\Controllers\AccessoryController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ComponentController;
use App\Http\Controllers\ConsumableController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TimelineController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/alerts', [AlertController::class, 'index'])->name('alerts.index');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/timeline', [TimelineController::class, 'index'])->name('timeline.index');

    Route::resource('assets', AssetController::class)->except(['show']);
    Route::resource('accessories', AccessoryController::class)->except(['show']);
    Route::resource('consumables', ConsumableController::class)->except(['show']);
    Route::resource('components', ComponentController::class)->except(['show']);
    Route::resource('licenses', LicenseController::class)->except(['show'])->parameters(['licenses' => 'license']);
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('suppliers', SupplierController::class)->except(['show']);
    Route::resource('companies', CompanyController::class)->except(['show']);
    Route::resource('locations', LocationController::class)->except(['show']);
    Route::resource('employees', EmployeeController::class)->except(['show']);
    Route::resource('users', UserController::class)->except(['show'])->middleware('role:admin');

    Route::get('assets/export/csv', [AssetController::class, 'exportCsv'])->name('assets.export.csv');
    Route::post('assets/import/csv', [AssetController::class, 'importCsv'])->name('assets.import.csv')->middleware('role:admin,manager');

    Route::get('accessories/export/csv', [AccessoryController::class, 'exportCsv'])->name('accessories.export.csv');
    Route::post('accessories/import/csv', [AccessoryController::class, 'importCsv'])->name('accessories.import.csv')->middleware('role:admin,manager');

    Route::get('components/export/csv', [ComponentController::class, 'exportCsv'])->name('components.export.csv');
    Route::post('components/import/csv', [ComponentController::class, 'importCsv'])->name('components.import.csv')->middleware('role:admin,manager');

    Route::get('consumables/export/csv', [ConsumableController::class, 'exportCsv'])->name('consumables.export.csv');
    Route::post('consumables/import/csv', [ConsumableController::class, 'importCsv'])->name('consumables.import.csv')->middleware('role:admin,manager');

    Route::get('accessories/{accessory}/checkout', [AccessoryController::class, 'checkoutForm'])->name('accessories.checkout.form');
    Route::post('accessories/{accessory}/checkout', [AccessoryController::class, 'checkout'])->name('accessories.checkout');
    Route::get('accessories/{accessory}/checkin', [AccessoryController::class, 'checkinForm'])->name('accessories.checkin.form');
    Route::post('accessories/{accessory}/checkin', [AccessoryController::class, 'checkin'])->name('accessories.checkin');
    Route::get('accessories/{accessory}/history', [AccessoryController::class, 'history'])->name('accessories.history');

    Route::get('components/{component}/allocate', [ComponentController::class, 'allocateForm'])->name('components.allocate.form');
    Route::post('components/{component}/allocate', [ComponentController::class, 'allocate'])->name('components.allocate');
    Route::get('components/{component}/release', [ComponentController::class, 'releaseForm'])->name('components.release.form');
    Route::post('components/{component}/release', [ComponentController::class, 'release'])->name('components.release');
    Route::get('components/{component}/history', [ComponentController::class, 'history'])->name('components.history');

    Route::get('consumables/{consumable}/consume', [ConsumableController::class, 'consumeForm'])->name('consumables.consume.form');
    Route::post('consumables/{consumable}/consume', [ConsumableController::class, 'consume'])->name('consumables.consume');
    Route::get('consumables/{consumable}/restock', [ConsumableController::class, 'restockForm'])->name('consumables.restock.form');
    Route::post('consumables/{consumable}/restock', [ConsumableController::class, 'restock'])->name('consumables.restock');
    Route::get('consumables/{consumable}/history', [ConsumableController::class, 'history'])->name('consumables.history');
});
