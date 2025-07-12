<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RetailerController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index']);
Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);
Route::get('/products/{product}/edit', [ProductController::class, 'edit']);
Route::put('/products/{product}', [ProductController::class, 'update']);
Route::delete('/products/{product}', [ProductController::class, 'destroy']);

Route::get('/retailers', [RetailerController::class, 'index']);
Route::post('/retailers', [RetailerController::class, 'store']);
Route::get('/retailers/{retailer}/edit', [RetailerController::class, 'edit']);
Route::put('/retailers/{retailer}', [RetailerController::class, 'update']);
Route::delete('/retailers/{retailer}', [RetailerController::class, 'destroy']);
Route::post('/retailers/{retailer}/stock', [RetailerController::class, 'addStock']);
