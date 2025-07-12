<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Retailer;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockStatusController extends Controller
{
    public function index()
    {
        $stats = [
            'total_products' => Product::count(),
            'total_retailers' => Retailer::count(),
            'total_stock_entries' => Stock::count(),
            'in_stock_count' => Stock::where('in_stock', true)->count(),
            'out_of_stock_count' => Stock::where('in_stock', false)->count(),
            'lowest_price_product' => $this->getLowestPriceProduct(),
            'highest_price_product' => $this->getHighestPriceProduct(),
            'most_available_product' => $this->getMostAvailableProduct(),
        ];

        $products = Product::with(['stock' => function($query) {
            $query->with('retailer');
        }])->get();

        $retailers = Retailer::with(['stock' => function($query) {
            $query->with('product');
        }])->get();

        $stockAnalytics = $this->getStockAnalytics();

        return view('stock-status.index', compact('stats', 'products', 'retailers', 'stockAnalytics'));
    }

    public function checkStock(Request $request)
    {
        $productId = $request->input('product_id');
        $retailerId = $request->input('retailer_id');
        $priceRange = $request->input('price_range');
        $stockStatus = $request->input('stock_status');

        $query = Stock::with(['product', 'retailer']);

        if ($productId) {
            $query->where('product_id', $productId);
        }

        if ($retailerId) {
            $query->where('retailer_id', $retailerId);
        }

        if ($stockStatus !== null) {
            $query->where('in_stock', $stockStatus);
        }

        if ($priceRange) {
            $this->applyPriceFilter($query, $priceRange);
        }

        $results = $query->get();

        return response()->json([
            'success' => true,
            'data' => $results,
            'count' => $results->count()
        ]);
    }

    private function getLowestPriceProduct()
    {
        return Stock::with('product')
            ->where('in_stock', true)
            ->orderBy('price', 'asc')
            ->first();
    }

    private function getHighestPriceProduct()
    {
        return Stock::with('product')
            ->orderBy('price', 'desc')
            ->first();
    }

    private function getMostAvailableProduct()
    {
        return Product::withCount(['stock' => function($query) {
            $query->where('in_stock', true);
        }])
        ->orderBy('stock_count', 'desc')
        ->first();
    }

    private function getStockAnalytics()
    {
        return [
            'price_distribution' => $this->getPriceDistribution(),
            'retailer_performance' => $this->getRetailerPerformance(),
            'stock_trends' => $this->getStockTrends(),
        ];
    }

    private function getPriceDistribution()
    {
        return Stock::selectRaw('
            CASE 
                WHEN price <= 5000 THEN "Under ₹50"
                WHEN price <= 10000 THEN "₹50 - ₹100"
                WHEN price <= 25000 THEN "₹100 - ₹250"
                WHEN price <= 50000 THEN "₹250 - ₹500"
                ELSE "Above ₹500"
            END as price_range,
            COUNT(*) as count
        ')
        ->groupBy('price_range')
        ->orderBy('price')
        ->get();
    }

    private function getRetailerPerformance()
    {
        return Retailer::withCount(['stock' => function($query) {
            $query->where('in_stock', true);
        }])
        ->withCount('stock')
        ->get()
        ->map(function($retailer) {
            $retailer->availability_rate = $retailer->stock_count > 0 
                ? round(($retailer->stock_count / $retailer->stock_count) * 100, 1)
                : 0;
            return $retailer;
        });
    }

    private function getStockTrends()
    {
        return Stock::selectRaw('
            DATE(created_at) as date,
            COUNT(*) as total_stock,
            SUM(CASE WHEN in_stock = 1 THEN 1 ELSE 0 END) as in_stock_count
        ')
        ->groupBy('date')
        ->orderBy('date', 'desc')
        ->limit(7)
        ->get();
    }

    private function applyPriceFilter($query, $priceRange)
    {
        switch ($priceRange) {
            case 'under_50':
                $query->where('price', '<=', 5000);
                break;
            case '50_100':
                $query->whereBetween('price', [5000, 10000]);
                break;
            case '100_250':
                $query->whereBetween('price', [10000, 25000]);
                break;
            case '250_500':
                $query->whereBetween('price', [25000, 50000]);
                break;
            case 'above_500':
                $query->where('price', '>', 50000);
                break;
        }
    }
} 