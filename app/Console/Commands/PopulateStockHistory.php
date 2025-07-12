<?php

namespace App\Console\Commands;

use App\Models\Stock;
use App\Models\StockHistory;
use Illuminate\Console\Command;
use Carbon\Carbon;

class PopulateStockHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:populate-history {--force : Force populate even if history exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate stock history for existing stock entries';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Populating stock history...');

        $stocks = Stock::all();
        
        if ($stocks->isEmpty()) {
            $this->warn('No stock entries found. Please add some stock first.');
            return 1;
        }

        $bar = $this->output->createProgressBar($stocks->count());
        $bar->start();

        foreach ($stocks as $stock) {
            // Check if history already exists
            $existingHistory = StockHistory::where('stock_id', $stock->id)->count();
            
            if ($existingHistory > 0 && !$this->option('force')) {
                $bar->advance();
                continue;
            }

            // Clear existing history if force option is used
            if ($this->option('force')) {
                StockHistory::where('stock_id', $stock->id)->delete();
            }

            // Create initial history entry
            StockHistory::create([
                'stock_id' => $stock->id,
                'price' => $stock->price,
                'in_stock' => $stock->in_stock,
                'changed_at' => $stock->created_at,
            ]);

            // Create sample historical changes
            $this->createSampleHistory($stock);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Stock history populated successfully!');
        
        return 0;
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