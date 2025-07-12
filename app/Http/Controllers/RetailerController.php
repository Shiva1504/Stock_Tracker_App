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

        return redirect('/retailers');
    }
}
