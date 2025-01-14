<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\mejaController;
use App\Http\Controllers\menuController;
use App\Http\Controllers\userController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register',[AuthController::class,'register']);
Route::post('login', [AuthController::class,'login']);
Route::post('refresh', [AuthController::class,'refresh']);
Route::post('logout', [AuthController::class,'logout']);

Route::apiResource('meja', mejaController::class);

Route::apiResource('menu', menuController::class);

Route::apiResource('user', userController::class);

Route::middleware(['auth:api'])->group(function () {
    Route::post('/transaksi', [TransaksiController::class, 'store']);
    Route::get('/transaksi', [TransaksiController::class, 'index']);
    Route::get('/transaksi/cetak-nota/{id}', [TransaksiController::class, 'cetakNota']);
    Route::get('/transaksi/kasir/{id}', [TransaksiController::class, 'getTransaksiByKasir']);
    Route::post('/transaksi/tanggal', [TransaksiController::class, 'getTransaksiByTanggal']);
});