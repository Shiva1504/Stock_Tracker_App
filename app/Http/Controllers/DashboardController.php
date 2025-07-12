<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Retailer;
use App\Models\Stock;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_products' => Product::count(),
            'total_retailers' => Retailer::count(),
            'total_stock_entries' => Stock::count(),
            'in_stock_count' => Stock::where('in_stock', true)->count(),
            'out_of_stock_count' => Stock::where('in_stock', false)->count(),
        ];

        $recentProducts = Product::with('stock.retailer')
            ->latest()
            ->take(5)
            ->get();

        $productsWithStock = Product::with(['stock' => function($query) {
            $query->where('in_stock', true);
        }, 'stock.retailer'])
        ->whereHas('stock', function($query) {
            $query->where('in_stock', true);
        })
        ->get();

        $productsOutOfStock = Product::with(['stock' => function($query) {
            $query->where('in_stock', false);
        }, 'stock.retailer'])
        ->whereDoesntHave('stock', function($query) {
            $query->where('in_stock', true);
        })
        ->get();

        return view('dashboard.index', compact('stats', 'recentProducts', 'productsWithStock', 'productsOutOfStock'));
    }
}
