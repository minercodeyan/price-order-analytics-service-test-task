<?php

use App\Http\Controllers\PriceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderStatisticsController;
use Illuminate\Support\Facades\Route;


Route::get('/price', [PriceController::class, 'getPrice']);

Route::get('/orders/statistics', [OrderStatisticsController::class, 'index']);

Route::get('/orders/{id}', [OrderController::class, 'show']);

Route::post('/soap/order', [OrderController::class, 'soapStore']);

//manticore search basic *
Route::get('/search/orders', [OrderController::class, 'search']);
