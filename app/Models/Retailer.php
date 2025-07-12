<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retailer extends Model
{
    public function addStock(Product $product, Stock $stock)
    {
        $stock->product_id = $product->id;
        $stock->retailer_id = $this->id;
        $stock->save();
    }

    public function stock()
    {
        return $this->hasMany(Stock::class, 'retailer_id');
    }
}
