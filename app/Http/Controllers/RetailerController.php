<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Retailer;
use App\Models\Stock;
use Illuminate\Http\Request;

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

        Retailer::create([
            'name' => $request->name
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

        return redirect('/retailers')->with('success', 'Retailer updated successfully!');
    }

    public function destroy(Retailer $retailer)
    {
        $retailer->delete();
        
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

        return redirect('/retailers')->with('success', 'Stock updated successfully!');
    }

    public function deleteStock(Stock $stock)
    {
        $stock->delete();
        
        return redirect('/retailers')->with('success', 'Stock deleted successfully!');
    }
}
