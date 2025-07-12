<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

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

        Product::create([
            'name' => $request->name,
            'low_stock_threshold' => $request->low_stock_threshold,
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

        return redirect('/products')->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        
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
