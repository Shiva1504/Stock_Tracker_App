<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\User;
use App\Notifications\ProductBackInStock;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckStockStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $products = Product::with('stock.retailer')->get();

        foreach ($products as $product) {
            foreach ($product->stock as $stock) {
                $this->checkStockForRetailer($product, $stock);
            }
        }
    }

    private function checkStockForRetailer(Product $product, $stock): void
    {
        try {
            // This is a simplified check - in a real application, you'd implement
            // specific scrapers for each retailer or use their APIs
            $response = Http::timeout(10)->get($stock->url);
            
            // For demo purposes, we'll simulate stock status changes
            // In reality, you'd parse the response to determine stock status
            $isInStock = $this->parseStockStatus($response, $stock->retailer->name);
            
            if ($isInStock && !$stock->in_stock) {
                // Product came back in stock
                $stock->update(['in_stock' => true]);
                
                // Send notifications to all users
                $users = User::all();
                foreach ($users as $user) {
                    $user->notify(new ProductBackInStock($product, $stock));
                }
                
                Log::info("Product {$product->name} is back in stock at {$stock->retailer->name}");
            } elseif (!$isInStock && $stock->in_stock) {
                // Product went out of stock
                $stock->update(['in_stock' => false]);
                Log::info("Product {$product->name} is out of stock at {$stock->retailer->name}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to check stock for {$product->name} at {$stock->retailer->name}: " . $e->getMessage());
        }
    }

    private function parseStockStatus($response, string $retailerName): bool
    {
        // This is a simplified implementation
        // In a real application, you'd have specific parsers for each retailer
        
        $body = strtolower($response->body());
        
        // Common out-of-stock indicators
        $outOfStockIndicators = [
            'out of stock',
            'sold out',
            'unavailable',
            'backorder',
            'pre-order'
        ];
        
        foreach ($outOfStockIndicators as $indicator) {
            if (str_contains($body, $indicator)) {
                return false;
            }
        }
        
        // Common in-stock indicators
        $inStockIndicators = [
            'add to cart',
            'buy now',
            'in stock',
            'available'
        ];
        
        foreach ($inStockIndicators as $indicator) {
            if (str_contains($body, $indicator)) {
                return true;
            }
        }
        
        // Default to false if we can't determine
        return false;
    }
}
