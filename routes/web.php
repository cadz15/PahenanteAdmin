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

Route::middleware('auth')->group(function () {
    Route::get('/supplier-create', [PahenanteController::class, 'createSupplier'])->name('supplier.create');
    Route::post('/supplier-create', [PahenanteController::class, 'storeSupplier'])->name('supplier.store');
    Route::get('/supplier-edit/{id}', [PahenanteController::class, 'supplierList'])->name('supplier.edit');
    Route::post('/supplier-list', [PahenanteController::class, 'supplierList'])->name('supplier.list');
    Route::delete('/supplier-delete/{id}', [PahenanteController::class, 'supplierList'])->name('supplier.destroy');

    Route::get('/supplier-items/{id}', [PahenanteController::class, 'itemList'])->name('item.list');

});

require __DIR__.'/auth.php';
