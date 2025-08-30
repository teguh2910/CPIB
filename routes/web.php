<?php

use App\Http\Controllers\Api\PartyLookupController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImportBarangController;
use App\Http\Controllers\ImportDokumenController;
use App\Http\Controllers\ImportKemasanController;
use App\Http\Controllers\ImportNotificationController;
use App\Http\Controllers\ImportPetiKemasController;
use App\Http\Controllers\PartyController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login.form'));

// AUTH (manual)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// IMPORT NOTIFICATIONS (protected)
Route::middleware('auth.session')->group(function () {
    // Main Import Notification CRUD
    Route::resource('import', ImportNotificationController::class)->parameters([
        'import' => 'importNotification',
    ]);
    // Import DOKUMEN
    Route::post('dokumen', [ImportDokumenController::class, 'store'])->name('dokumen.store');
    Route::get('dokumen/{id}', [ImportDokumenController::class, 'edit'])->name('dokumen.edit');
    Route::put('dokumen/{id}', [ImportDokumenController::class, 'update'])->name('dokumen.update');
    Route::delete('dokumen/{dokumen}', [ImportDokumenController::class, 'destroy'])->name('dokumen.destroy');
    // Import KEMASAN
    Route::post('kemasan', [ImportKemasanController::class, 'store_kemasan'])->name('kemasan.store');
    Route::get('kemasan/{id}/edit', [ImportKemasanController::class, 'edit_kemasan'])->name('kemasan.edit');
    Route::put('kemasan/{id}', [ImportKemasanController::class, 'update_kemasan'])->name('kemasan.update');
    Route::delete('kemasan/{kemasan}', [ImportKemasanController::class, 'destroy_kemasan'])->name('kemasan.destroy');
    // Import PETI KEMAS
    Route::post('petikemas', [ImportPetiKemasController::class, 'store_petikemas'])->name('petikemas.store');
    Route::get('petikemas/{id}/edit', [ImportPetiKemasController::class, 'edit_petikemas'])->name('petikemas.edit');
    Route::put('petikemas/{id}', [ImportPetiKemasController::class, 'update_petikemas'])->name('petikemas.update');
    Route::delete('petikemas/{petikemas}', [ImportPetiKemasController::class, 'destroy_petikemas'])->name('petikemas.destroy');

    // Import BARANG
    Route::get('barang', [ImportBarangController::class, 'index'])->name('barang.index');
    Route::get('barang/create', [ImportBarangController::class, 'create'])->name('barang.create');
    Route::post('barang', [ImportBarangController::class, 'store'])->name('barang.store');
    // CSV/Excel upload
    Route::post('barang/upload', [ImportBarangController::class, 'upload'])->name('barang.upload');
    // Update all barang derived/calculated fields
    Route::get('barang/template', [ImportBarangController::class, 'templateCsv'])->name('barang.template');
    Route::get('barang/{id}/edit', [ImportBarangController::class, 'edit'])->name('barang.edit');
    Route::put('barang/{id}', [ImportBarangController::class, 'update'])->name('barang.update');
    Route::delete('barang/{barang}', [ImportBarangController::class, 'destroy'])->name('barang.destroy');

    Route::post('pungutan', [ImportPungutanController::class, 'store'])->name('pungutan.store');

    Route::get('/ajax/party/search', [PartyLookupController::class, 'search'])
        ->name('ajax.party.search'); // ?type=pengirim|penjual&q=abc
    Route::get('/ajax/party/{id}', [PartyLookupController::class, 'show'])
        ->name('ajax.party.show');

    // CRUD Master
    Route::resource('/master/parties', PartyController::class)->names([
        'index' => 'parties.index',
        'create' => 'parties.create',
        'store' => 'parties.store',
        'edit' => 'parties.edit',
        'update' => 'parties.update',
        'destroy' => 'parties.destroy',
    ]);

});
