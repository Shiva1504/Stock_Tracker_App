<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Retailer;
use App\Models\Stock;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

class RetailerController extends Controller
{
    public function index(Request $request)
    {
        $query = Retailer::query();
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('stock.product', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by stock status
        if ($request->filled('stock_status')) {
            $status = $request->stock_status;
            if ($status === 'in_stock') {
                $query->whereHas('stock', function($q) {
                    $q->where('in_stock', true);
                });
            } elseif ($status === 'out_of_stock') {
                $query->whereHas('stock', function($q) {
                    $q->where('in_stock', false);
                });
            }
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        
        if ($sortBy === 'stock_count') {
            $query->withCount('stock')->orderBy('stock_count', $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }
        
        $retailers = $query->get();
        $products = Product::all();
        
        return view('retailers.index', compact('retailers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:retailers,name'
        ]);

        $retailer = Retailer::create([
            'name' => $request->name
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'created',
            'subject_type' => 'Retailer',
            'subject_id' => $retailer->id,
            'description' => 'Created retailer: ' . $retailer->name,
        ]);

        return redirect('/retailers');
    }

    public function edit(Retailer $retailer)
    {
        return view('retailers.edit', compact('retailer'));
    }

    public function update(Request $request, Retailer $retailer)
    {
        $request->validate([
            'name' => 'required|unique:retailers,name,' . $retailer->id
        ]);

        $retailer->update([
            'name' => $request->name
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'updated',
            'subject_type' => 'Retailer',
            'subject_id' => $retailer->id,
            'description' => 'Updated retailer: ' . $retailer->name,
        ]);

        return redirect('/retailers')->with('success', 'Retailer updated successfully!');
    }

    public function destroy(Retailer $retailer)
    {
        $retailerName = $retailer->name;
        $retailerId = $retailer->id;
        $retailer->delete();
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'deleted',
            'subject_type' => 'Retailer',
            'subject_id' => $retailerId,
            'description' => 'Deleted retailer: ' . $retailerName,
        ]);
        
        return redirect('/retailers')->with('success', 'Retailer deleted successfully!');
    }

    public function addStock(Request $request, Retailer $retailer)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'price' => 'required|numeric|min:0',
            'url' => 'required|url',
            'sku' => 'nullable|string',
            // 'in_stock' => 'required|boolean' // Remove required, handle default below
        ]);

        $inStock = $request->has('in_stock') ? (bool)$request->in_stock : false;

        $stock = new Stock([
            'price' => $request->price * 100, // Store in paise (INR cents)
            'url' => $request->url,
            'sku' => $request->sku,
            'in_stock' => $inStock
        ]);

        $retailer->addStock(Product::find($request->product_id), $stock);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'created',
            'subject_type' => 'Stock',
            'subject_id' => $stock->id,
            'description' => 'Added stock for product ID ' . $stock->product_id . ' at retailer ID ' . $stock->retailer_id,
        ]);

        return redirect('/retailers')->with('success', 'Stock added successfully!');
    }

    public function editStock(Stock $stock)
    {
        $products = Product::all();
        return view('retailers.edit-stock', compact('stock', 'products'));
    }

    public function updateStock(Request $request, Stock $stock)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'price' => 'required|numeric|min:0',
            'url' => 'required|url',
            'sku' => 'nullable|string',
            // 'in_stock' => 'required|boolean' // Remove required, handle default below
        ]);

        $inStock = $request->has('in_stock') ? (bool)$request->in_stock : false;

        $stock->update([
            'product_id' => $request->product_id,
            'price' => $request->price * 100, // Store in paise (INR cents)
            'url' => $request->url,
            'sku' => $request->sku,
            'in_stock' => $inStock
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'updated',
            'subject_type' => 'Stock',
            'subject_id' => $stock->id,
            'description' => 'Updated stock for product ID ' . $stock->product_id . ' at retailer ID ' . $stock->retailer_id,
        ]);

        return redirect('/retailers')->with('success', 'Stock updated successfully!');
    }

    public function deleteStock(Stock $stock)
    {
        $stockId = $stock->id;
        $productId = $stock->product_id;
        $retailerId = $stock->retailer_id;
        $stock->delete();
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'deleted',
            'subject_type' => 'Stock',
            'subject_id' => $stockId,
            'description' => 'Deleted stock for product ID ' . $productId . ' at retailer ID ' . $retailerId,
        ]);
        
        return redirect('/retailers')->with('success', 'Stock deleted successfully!');
    }
}
