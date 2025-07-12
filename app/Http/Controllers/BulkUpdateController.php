<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Product;
use App\Models\Retailer;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BulkUpdateController extends Controller
{
    public function index(Request $request)
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

        $stocks = $query->paginate(20);
        $products = Product::all();
        $retailers = Retailer::all();

        return view('bulk-update.index', compact('stocks', 'products', 'retailers'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'stock_ids' => 'required|array|min:1',
            'stock_ids.*' => 'exists:stock,id',
            'update_type' => 'required|in:status,price,percentage,url',
            'new_value' => 'required_unless:update_type,status',
            'new_status' => 'required_if:update_type,status|boolean',
        ]);

        $stockIds = $request->stock_ids;
        $updateType = $request->update_type;
        $updatedCount = 0;

        DB::beginTransaction();

        try {
            foreach ($stockIds as $stockId) {
                $stock = Stock::find($stockId);
                
                if (!$stock) continue;

                $oldValues = [
                    'price' => $stock->price,
                    'in_stock' => $stock->in_stock,
                    'url' => $stock->url,
                ];

                switch ($updateType) {
                    case 'status':
                        $stock->update(['in_stock' => $request->new_status]);
                        break;

                    case 'price':
                        $newPrice = (float)$request->new_value * 100; // Convert to paise
                        $stock->update(['price' => $newPrice]);
                        break;

                    case 'percentage':
                        $percentage = (float)$request->new_value;
                        $newPrice = $stock->price * (1 + $percentage / 100);
                        $stock->update(['price' => (int)$newPrice]);
                        break;

                    case 'url':
                        $stock->update(['url' => $request->new_value]);
                        break;
                }

                // Record history
                $stock->recordHistory();

                // Log activity
                ActivityLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'bulk_updated',
                    'subject_type' => 'Stock',
                    'subject_id' => $stock->id,
                    'description' => $this->getBulkUpdateDescription($updateType, $oldValues, $stock),
                ]);

                $updatedCount++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully updated {$updatedCount} stock entries.",
                'updated_count' => $updatedCount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating stock entries.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getStocks(Request $request)
    {
        $query = Stock::with(['product', 'retailer']);

        // Apply same filters as index
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('retailer_id')) {
            $query->where('retailer_id', $request->retailer_id);
        }

        if ($request->filled('stock_status')) {
            $query->where('in_stock', $request->stock_status === 'in_stock');
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

        $stocks = $query->get();

        return response()->json([
            'success' => true,
            'data' => $stocks,
            'count' => $stocks->count()
        ]);
    }

    private function getBulkUpdateDescription($updateType, $oldValues, $stock)
    {
        switch ($updateType) {
            case 'status':
                $status = $stock->in_stock ? 'In Stock' : 'Out of Stock';
                return "Bulk updated status to: {$status}";
            
            case 'price':
                $oldPrice = '₹' . number_format($oldValues['price'] / 100, 2);
                $newPrice = '₹' . number_format($stock->price / 100, 2);
                return "Bulk updated price from {$oldPrice} to {$newPrice}";
            
            case 'percentage':
                $oldPrice = '₹' . number_format($oldValues['price'] / 100, 2);
                $newPrice = '₹' . number_format($stock->price / 100, 2);
                return "Bulk updated price by percentage: {$oldPrice} → {$newPrice}";
            
            case 'url':
                return "Bulk updated product URL";
            
            default:
                return "Bulk updated stock entry";
        }
    }
} 