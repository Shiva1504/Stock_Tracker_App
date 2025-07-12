<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = 'stock';

    protected $fillable = [
        'product_id',
        'retailer_id',
        'price',
        'url',
        'sku',
        'in_stock'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function retailer()
    {
        return $this->belongsTo(Retailer::class);
    }

    public function history()
    {
        return $this->hasMany(StockHistory::class);
    }

    public function recordHistory()
    {
        $this->history()->create([
            'price' => $this->price,
            'in_stock' => $this->in_stock,
            'changed_at' => now(),
        ]);
    }
}
