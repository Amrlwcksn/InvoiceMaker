<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('customers', CustomerController::class);

Route::resource('invoices', InvoiceController::class);

Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
