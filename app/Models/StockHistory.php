<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    use HasFactory;

    protected $table = 'stock_history';

    protected $fillable = [
        'stock_id',
        'price',
        'in_stock',
        'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
} 