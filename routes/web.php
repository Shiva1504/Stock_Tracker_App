<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\RetailerController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);

Route::get('/retailers', [RetailerController::class, 'index']);
Route::post('/retailers', [RetailerController::class, 'store']);
Route::post('/retailers/{retailer}/stock', [RetailerController::class, 'addStock']);
