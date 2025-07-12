<?php

namespace App\Console\Commands;

use App\Models\Stock;
use App\Models\StockHistory;
use Illuminate\Console\Command;

class InitializeStockHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:initialize-history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize stock history for existing stock entries';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Initializing stock history for existing entries...');

        $stocks = Stock::all();
        
        if ($stocks->isEmpty()) {
            $this->warn('No stock entries found.');
            return 1;
        }

        $bar = $this->output->createProgressBar($stocks->count());
        $bar->start();

        foreach ($stocks as $stock) {
            // Check if history already exists
            $existingHistory = StockHistory::where('stock_id', $stock->id)->count();
            
            if ($existingHistory === 0) {
                // Create initial history entry
                StockHistory::create([
                    'stock_id' => $stock->id,
                    'price' => $stock->price,
                    'in_stock' => $stock->in_stock,
                    'changed_at' => $stock->created_at,
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        
        $historyCount = StockHistory::count();
        $this->info("Stock history initialized! Total history entries: {$historyCount}");
        
        return 0;
    }
} 