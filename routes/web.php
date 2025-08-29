<?php

use App\Http\Controllers\Api\PartyLookupController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImportBarangController;
use App\Http\Controllers\ImportHeaderController;
use App\Http\Controllers\ImportKemasanController;
use App\Http\Controllers\ImportNotificationController;
use App\Http\Controllers\ImportPetiKemasController;
use App\Http\Controllers\ImportPungutanController;
use App\Http\Controllers\ImportTransaksiController;
use App\Http\Controllers\ImportDokumenController;
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
    Route::post('dokumen', [ImportDokumenController::class, 'store'])->name('dokumen.store');

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
