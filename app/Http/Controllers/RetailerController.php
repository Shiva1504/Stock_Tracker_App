<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Retailer;
use App\Models\Stock;
use Illuminate\Http\Request;

class RetailerController extends Controller
{
    public function index()
    {
        $retailers = Retailer::all();
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
            'in_stock' => 'required|boolean'
        ]);

        $stock = new Stock([
            'price' => $request->price * 100, // Store in paise (INR cents)
            'url' => $request->url,
            'sku' => $request->sku,
            'in_stock' => $request->in_stock
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
            'in_stock' => 'required|boolean'
        ]);

        $stock->update([
            'product_id' => $request->product_id,
            'price' => $request->price * 100, // Store in paise (INR cents)
            'url' => $request->url,
            'sku' => $request->sku,
            'in_stock' => $request->in_stock
        ]);

        return redirect('/retailers')->with('success', 'Stock updated successfully!');
    }

    public function deleteStock(Stock $stock)
    {
        $stock->delete();
        
        return redirect('/retailers')->with('success', 'Stock deleted successfully!');
    }
}
