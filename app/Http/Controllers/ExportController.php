<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Product;
use App\Models\Retailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller
{
    public function index()
    {
        $products = Product::all();
        $retailers = Retailer::all();

        return view('export.index', compact('products', 'retailers'));
    }

    public function exportCsv(Request $request)
    {
        $stocks = $this->getFilteredStocks($request);

        $filename = 'stock_data_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($stocks) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'Product Name',
                'Retailer Name',
                'Price (₹)',
                'Stock Status',
                'SKU',
                'Product URL',
                'Last Updated'
            ]);

            // Add data rows
            foreach ($stocks as $stock) {
                fputcsv($file, [
                    $stock->product->name,
                    $stock->retailer->name,
                    number_format($stock->price / 100, 2),
                    $stock->in_stock ? 'In Stock' : 'Out of Stock',
                    $stock->sku ?: 'N/A',
                    $stock->url,
                    $stock->updated_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportExcel(Request $request)
    {
        $stocks = $this->getFilteredStocks($request);

        $filename = 'stock_data_' . date('Y-m-d_H-i-s') . '.xlsx';

        // For simplicity, we'll create a CSV with .xlsx extension
        // In a real application, you'd use a library like PhpSpreadsheet
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($stocks) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers (Excel can read CSV)
            fputcsv($file, [
                'Product Name',
                'Retailer Name',
                'Price (₹)',
                'Stock Status',
                'SKU',
                'Product URL',
                'Last Updated'
            ]);

            // Add data rows
            foreach ($stocks as $stock) {
                fputcsv($file, [
                    $stock->product->name,
                    $stock->retailer->name,
                    number_format($stock->price / 100, 2),
                    $stock->in_stock ? 'In Stock' : 'Out of Stock',
                    $stock->sku ?: 'N/A',
                    $stock->url,
                    $stock->updated_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportStockHistory(Request $request)
    {
        $query = \App\Models\StockHistory::with(['stock.product', 'stock.retailer']);

        // Apply filters
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

        if ($request->filled('date_from')) {
            $query->where('changed_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('changed_at', '<=', $request->date_to . ' 23:59:59');
        }

        $history = $query->latest('changed_at')->get();

        $filename = 'stock_history_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($history) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'Product Name',
                'Retailer Name',
                'Price (₹)',
                'Stock Status',
                'Change Date',
                'Change Time'
            ]);

            // Add data rows
            foreach ($history as $entry) {
                fputcsv($file, [
                    $entry->stock->product->name,
                    $entry->stock->retailer->name,
                    number_format($entry->price / 100, 2),
                    $entry->in_stock ? 'In Stock' : 'Out of Stock',
                    $entry->changed_at->format('Y-m-d'),
                    $entry->changed_at->format('H:i:s')
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportActivityLog(Request $request)
    {
        $query = \App\Models\ActivityLog::with('user');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $activities = $query->latest()->get();

        $filename = 'activity_log_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($activities) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'User',
                'Action',
                'Subject Type',
                'Subject ID',
                'Description',
                'Date & Time'
            ]);

            // Add data rows
            foreach ($activities as $activity) {
                fputcsv($file, [
                    $activity->user ? $activity->user->name : 'System',
                    ucfirst($activity->action),
                    $activity->subject_type,
                    $activity->subject_id,
                    $activity->description,
                    $activity->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    private function getFilteredStocks(Request $request)
    {
        $query = Stock::with(['product', 'retailer']);

        // Apply filters
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('retailer_id')) {
            $query->where('retailer_id', $request->retailer_id);
        }

        if ($request->filled('stock_status')) {
            $query->where('in_stock', $request->stock_status === 'in_stock');
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min * 100);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max * 100);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('product', function($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('retailer', function($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%");
                })
                ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        return $query->get();
    }
} 