<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class CheckStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:check {product? : The name of the product to check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check stock status for products';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $productName = $this->argument('product');

        if ($productName) {
            $product = Product::where('name', $productName)->first();
            
            if (!$product) {
                $this->error("Product '{$productName}' not found.");
                return 1;
            }

            $this->checkProductStock($product);
        } else {
            $products = Product::all();
            
            if ($products->isEmpty()) {
                $this->info('No products found.');
                return 0;
            }

            $this->info('Checking stock for all products...');
            $this->newLine();

            foreach ($products as $product) {
                $this->checkProductStock($product);
                $this->newLine();
            }
        }

        return 0;
    }

    private function checkProductStock(Product $product)
    {
        $this->info("📦 {$product->name}");
        
        if ($product->stock->isEmpty()) {
            $this->warn('  No stock information available.');
            return;
        }

        $inStockCount = $product->stock->where('in_stock', true)->count();
        $totalCount = $product->stock->count();

        if ($inStockCount > 0) {
            $this->info("  ✅ In stock at {$inStockCount} retailer(s)");
        } else {
            $this->error("  ❌ Out of stock at all {$totalCount} retailer(s)");
        }

        foreach ($product->stock as $stock) {
            $status = $stock->in_stock ? '✅' : '❌';
            $price = '$' . number_format($stock->price / 100, 2);
            $this->line("    {$status} {$stock->retailer->name} - {$price}");
        }
    }
}
