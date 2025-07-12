<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\StockHistory;
use App\Models\Product;
use App\Models\Retailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = StockHistory::with(['stock.product', 'stock.retailer']);

        // Filter by product
        if ($request->filled('product_id')) {
            $query->whereHas('stock', function($q) use ($request) {
                $q->where('product_id', $request->product_id);
            });
        }

        // Filter by retailer
        if ($request->filled('retailer_id')) {
            $query->whereHas('stock', function($q) use ($request) {
                $q->where('retailer_id', $request->retailer_id);
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('changed_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('changed_at', '<=', $request->date_to . ' 23:59:59');
        }

        $history = $query->latest('changed_at')->paginate(20);
        $products = Product::all();
        $retailers = Retailer::all();

        // Get trends data
        $trends = $this->getTrends($request);

        return view('stock-history.index', compact('history', 'products', 'retailers', 'trends'));
    }

    public function show(Stock $stock)
    {
        $history = $stock->history()->latest('changed_at')->get();
        $trends = $this->getStockTrends($stock);

        return view('stock-history.show', compact('stock', 'history', 'trends'));
    }

    private function getTrends(Request $request)
    {
        $query = StockHistory::with(['stock.product', 'stock.retailer']);

        if ($request->filled('product_id')) {
            $query->whereHas('stock', function($q) use ($request) {
                $q->where('product_id', $request->product_id);
            });
        }

        if ($request->filled('retailer_id')) {
            $query->whereHas('stock', function($q) use ($request) {
                $q->where('retailer_id', $request->retailer_id);
            });
        }

        // Price trends over time
        $priceTrends = $query->select(
            DB::raw('DATE(changed_at) as date'),
            DB::raw('AVG(price) as avg_price'),
            DB::raw('MIN(price) as min_price'),
            DB::raw('MAX(price) as max_price')
        )
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        // Stock status changes
        $statusChanges = $query->select(
            DB::raw('DATE(changed_at) as date'),
            DB::raw('COUNT(CASE WHEN in_stock = 1 THEN 1 END) as in_stock_count'),
            DB::raw('COUNT(CASE WHEN in_stock = 0 THEN 1 END) as out_of_stock_count')
        )
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return [
            'price_trends' => $priceTrends,
            'status_changes' => $statusChanges,
        ];
    }

    private function getStockTrends(Stock $stock)
    {
        $history = $stock->history()->orderBy('changed_at')->get();

        $priceTrends = $history->map(function($entry) {
            return [
                'date' => $entry->changed_at->format('Y-m-d'),
                'price' => $entry->price / 100, // Convert from paise to rupees
            ];
        });

        $statusChanges = $history->map(function($entry) {
            return [
                'date' => $entry->changed_at->format('Y-m-d'),
                'in_stock' => $entry->in_stock,
            ];
        });

        return [
            'price_trends' => $priceTrends,
            'status_changes' => $statusChanges,
        ];
    }
} 