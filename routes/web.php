<?php

use App\Http\Controllers\Api\PartyLookupController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImportBarangController;
use App\Http\Controllers\ImportDokumenController;
use App\Http\Controllers\ImportKemasanController;
use App\Http\Controllers\ImportNotificationController;
use App\Http\Controllers\ImportPetiKemasController;
use App\Http\Controllers\ImportPungutanController;
use App\Http\Controllers\PartyController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login.form'));

// AUTH (manual)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Temporary test route without authentication
Route::get('/test/pelabuhan-tujuan', [ImportNotificationController::class, 'searchPelabuhanTujuan'])
    ->name('test.pelabuhan-tujuan.search');

// AJAX route for kurs (temporary - without auth for testing)
Route::get('/ajax/kurs', [ImportNotificationController::class, 'getKurs'])
    ->name('ajax.kurs');

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

    // AJAX route for pelabuhan search
    Route::get('/ajax/pelabuhan/search', [ImportNotificationController::class, 'searchPelabuhan'])
        ->name('ajax.pelabuhan.search');

    // AJAX route for TPS search
    Route::get('/ajax/tps/search', [ImportNotificationController::class, 'searchTps'])
        ->name('ajax.tps.search');

    // AJAX route for pelabuhan tujuan search
    Route::get('/ajax/pelabuhan-tujuan/search', [ImportNotificationController::class, 'searchPelabuhanTujuan'])
        ->name('ajax.pelabuhan-tujuan.search');

    // AJAX route for negara search
    Route::get('/ajax/negara/search', [ImportNotificationController::class, 'searchNegara'])
        ->name('ajax.negara.search');

    // CRUD Master
    Route::resource('/master/parties', PartyController::class)->names([
        'index' => 'parties.index',
        'create' => 'parties.create',
        'store' => 'parties.store',
        'edit' => 'parties.edit',
        'update' => 'parties.update',
        'destroy' => 'parties.destroy',
    ]);
    Route::get('/master/parties/template', [PartyController::class, 'downloadTemplate'])->name('parties.template');
    Route::post('/master/parties/upload-excel', [PartyController::class, 'uploadExcel'])->name('parties.upload-excel');

    // Export all tables to Excel (each table -> separate sheet)
    Route::get('export/all', [ImportNotificationController::class, 'exportAll'])->name('import.exportAll');
    // Export data for a single ImportNotification (by id) - each related table -> separate sheet
    Route::get('export/{importNotification}', [ImportNotificationController::class, 'exportByNotification'])
        ->whereNumber('importNotification')
        ->name('import.export');
    // Export JSON for a single ImportNotification (nested structure)
    Route::get('export/{importNotification}/json', [ImportNotificationController::class, 'exportJsonByNotification'])
        ->whereNumber('importNotification')
        ->name('import.export.json');

});
