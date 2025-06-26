<?php

use App\Http\Controllers\PahenanteController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/images/{filename}', [PahenanteController::class, 'getFile'])->name('get-file');

Route::post('/mobile-update', [PahenanteController::class, 'mobileUpdate']);

Route::middleware('auth')->group(function () {
    Route::post('/supplier-create', [PahenanteController::class, 'storeSupplier'])->name('supplier.store');
    Route::get('/supplier-edit/{id}', [PahenanteController::class, 'showUpdateSupplier'])->name('supplier.edit');
    Route::post('/supplier-edit/{id}', [PahenanteController::class, 'updateSupplier'])->name('supplier.update');
    Route::post('/supplier-list', [PahenanteController::class, 'supplierList'])->name('supplier.list');
    Route::delete('/supplier-delete/{id}', [PahenanteController::class, 'destroySupplier'])->name('supplier.destroy');

    Route::get('/supplier-items/{id}', [PahenanteController::class, 'itemList'])->name('item.list');
    Route::post('/item-list/{id}', [PahenanteController::class, 'items'])->name('items.list');
    Route::post('/item-create/{id}', [PahenanteController::class, 'itemStore'])->name('items.store');
    Route::get('/item-edit/{id}', [PahenanteController::class, 'itemEdit'])->name('items.edit');
    Route::post('/item-edit/{id}', [PahenanteController::class, 'itemUpdate'])->name('items.update');
    Route::delete('/item-delete/{id}', [PahenanteController::class, 'itemDelete'])->name('items.destroy');

    Route::get('/qr-update', [PahenanteController::class, 'qrUpdate'])->name('qr');

});


require __DIR__.'/auth.php';
