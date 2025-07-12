<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'target_price',
        'is_active',
        'last_triggered_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_triggered_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function shouldTrigger($currentPrice)
    {
        // Only trigger if alert is active and current price is at or below target
        if (!$this->is_active) {
            return false;
        }

        // Check if current price is at or below target price
        if ($currentPrice > $this->target_price) {
            return false;
        }

        // Prevent spam by checking if we've triggered recently (within 24 hours)
        if ($this->last_triggered_at && $this->last_triggered_at->diffInHours(now()) < 24) {
            return false;
        }

        return true;
    }

    public function trigger()
    {
        $this->update([
            'last_triggered_at' => now()
        ]);
    }

    public function getTargetPriceInRupeesAttribute()
    {
        return $this->target_price / 100;
    }
}
