<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImportNotificationWizardController;
use App\Http\Controllers\Api\PartyLookupController;
use App\Http\Controllers\PartyController;

Route::get('/', fn() => redirect()->route('login.form'));

// AUTH (manual)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// WIZARD (protected)
Route::middleware('auth.session')->group(function () {
    Route::get('/import/wizard/{step?}', [ImportNotificationWizardController::class, 'show'])
        ->where('step', 'header|entitas|dokumen|pengangkut|kemasan|transaksi|barang|pungutan|pernyataan')
        ->name('wizard.show');

    Route::post('/import/wizard/{step}', [ImportNotificationWizardController::class, 'store'])
        ->where('step', 'header|entitas|dokumen|pengangkut|kemasan|transaksi|barang|pungutan|pernyataan')
        ->name('wizard.store');
    // LIST & DETAIL
    Route::get('/import', [ImportNotificationWizardController::class, 'index'])->name('import.index');
    Route::get('/import/{id}', [ImportNotificationWizardController::class, 'showDetail'])->name('import.show');

    // EDIT (buka kembali wizard bermodal data DB jadi draft session)
    Route::get('/import/{id}/edit', [ImportNotificationWizardController::class, 'edit'])->name('import.edit');

    // EXPORT PDF
    Route::get('/import/{id}/export/pdf', [ImportNotificationWizardController::class, 'exportPdf'])->name('import.export.pdf');
    
    Route::get('/ajax/party/search', [PartyLookupController::class, 'search'])
    ->name('ajax.party.search'); // ?type=pengirim|penjual&q=abc
    Route::get('/ajax/party/{id}', [PartyLookupController::class, 'show'])
        ->name('ajax.party.show');

    // CRUD Master
    Route::resource('/master/parties', PartyController::class)->names([
        'index'  => 'parties.index',
        'create' => 'parties.create',
        'store'  => 'parties.store',
        'edit'   => 'parties.edit',
        'update' => 'parties.update',
        'destroy'=> 'parties.destroy',
    ]);
});
