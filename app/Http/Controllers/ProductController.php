<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $products = Product::when($search, function($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        })->get();
        
        return view('products.index', compact('products', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:products,name',
            'low_stock_threshold' => 'required|integer|min:1',
        ]);

        $product = Product::create([
            'name' => $request->name,
            'low_stock_threshold' => $request->low_stock_threshold,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'action' => 'created',
            'subject_type' => 'Product',
            'subject_id' => $product->id,
            'description' => 'Created product: ' . $product->name,
        ]);

        return redirect('/products')->with('success', 'Product added successfully!');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|unique:products,name,' . $product->id
        ]);

        $product->update([
            'name' => $request->name
        ]);

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'action' => 'updated',
            'subject_type' => 'Product',
            'subject_id' => $product->id,
            'description' => 'Updated product: ' . $product->name,
        ]);

        return redirect('/products')->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        $productName = $product->name;
        $productId = $product->id;
        $product->delete();
        
        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'action' => 'deleted',
            'subject_type' => 'Product',
            'subject_id' => $productId,
            'description' => 'Deleted product: ' . $productName,
        ]);
        
        return redirect('/products')->with('success', 'Product deleted successfully!');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        
        $products = Product::when($search, function($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        })->get();

        return response()->json([
            'success' => true,
            'data' => $products,
            'count' => $products->count()
        ]);
    }
}
