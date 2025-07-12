<?php

namespace Database\Seeders;

use App\Models\Stock;
use App\Models\StockHistory;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class StockHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stocks = Stock::all();

        foreach ($stocks as $stock) {
            // Create initial history entry
            StockHistory::create([
                'stock_id' => $stock->id,
                'price' => $stock->price,
                'in_stock' => $stock->in_stock,
                'changed_at' => $stock->created_at,
            ]);

            // Create some sample historical changes
            $this->createSampleHistory($stock);
        }
    }

    private function createSampleHistory(Stock $stock)
    {
        $basePrice = $stock->price;
        $currentInStock = $stock->in_stock;
        $currentDate = $stock->created_at;

        // Create 3-5 historical entries over the past 30 days
        $numEntries = rand(3, 5);
        
        for ($i = 1; $i <= $numEntries; $i++) {
            // Random date within the last 30 days
            $randomDays = rand(1, 30);
            $randomHours = rand(0, 23);
            $randomMinutes = rand(0, 59);
            
            $historyDate = $currentDate->copy()->subDays($randomDays)->addHours($randomHours)->addMinutes($randomMinutes);
            
            // Random price change (±20% of base price)
            $priceChange = rand(-20, 20) / 100;
            $newPrice = max(100, $basePrice + ($basePrice * $priceChange)); // Minimum ₹1
            
            // Random stock status change (30% chance)
            $newInStock = rand(1, 10) <= 3 ? !$currentInStock : $currentInStock;
            
            StockHistory::create([
                'stock_id' => $stock->id,
                'price' => (int)$newPrice,
                'in_stock' => $newInStock,
                'changed_at' => $historyDate,
            ]);

            // Update current values for next iteration
            $currentInStock = $newInStock;
            $basePrice = $newPrice;
        }
    }
} 