<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RetailerController;
use App\Http\Controllers\StockStatusController;
use App\Http\Controllers\StockHistoryController;
use App\Http\Controllers\BulkUpdateController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index']);
Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);
Route::get('/products/search', [ProductController::class, 'search']);
Route::get('/products/{product}/edit', [ProductController::class, 'edit']);
Route::put('/products/{product}', [ProductController::class, 'update']);
Route::delete('/products/{product}', [ProductController::class, 'destroy']);

Route::get('/retailers', [RetailerController::class, 'index']);
Route::post('/retailers', [RetailerController::class, 'store']);
Route::get('/retailers/{retailer}/edit', [RetailerController::class, 'edit']);
Route::put('/retailers/{retailer}', [RetailerController::class, 'update']);
Route::delete('/retailers/{retailer}', [RetailerController::class, 'destroy']);
Route::post('/retailers/{retailer}/stock', [RetailerController::class, 'addStock']);

// Stock management routes
Route::get('/stock/{stock}/edit', [RetailerController::class, 'editStock']);
Route::put('/stock/{stock}', [RetailerController::class, 'updateStock']);
Route::delete('/stock/{stock}', [RetailerController::class, 'deleteStock']);

// Enhanced Stock Status routes
Route::get('/stock-status', [StockStatusController::class, 'index']);
Route::post('/stock-status/check', [StockStatusController::class, 'checkStock']);

// Stock History routes
Route::get('/stock-history', [StockHistoryController::class, 'index']);
Route::get('/stock-history/{stock}', [StockHistoryController::class, 'show']);

// Bulk Update routes
Route::get('/bulk-update', [BulkUpdateController::class, 'index']);
Route::post('/bulk-update', [BulkUpdateController::class, 'update']);
Route::get('/bulk-update/stocks', [BulkUpdateController::class, 'getStocks']);
