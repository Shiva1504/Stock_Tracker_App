<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'low_stock_threshold',
    ];

    public function inStock()
    {
        return $this->stock()->where('in_stock', true)->exists();
    }

    public function stock()
    {
        return $this->hasMany(Stock::class);
    }

    public function isLowStock()
    {
        return $this->inStockCount() <= $this->low_stock_threshold;
    }

    public function inStockCount()
    {
        return $this->stock->where('in_stock', true)->count();
    }
}
