<?php

declare(strict_types=1);

use App\Http\Controllers\DebitCardController;
use App\Http\Controllers\DebitCardTransactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')
    ->group(function () {
        // Debit card endpoints
        Route::apiResource('debit-cards', DebitCardController::class);

        // Debit card transactions endpoints
        Route::prefix('debit-card-transactions')->as('debit-card-transactions.')->group(function () {
            Route::get('', [DebitCardTransactionController::class, 'index'])->name('index');
            Route::post('', [DebitCardTransactionController::class, 'store'])->name('store');
            Route::get('{debitCardTransaction}', [DebitCardTransactionController::class, 'show'])->name('show');
        });
    });
