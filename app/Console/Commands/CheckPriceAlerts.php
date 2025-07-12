<?php

namespace App\Console\Commands;

use App\Models\PriceAlert;
use App\Models\Product;
use App\Notifications\PriceDropAlert;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckPriceAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alerts:check-prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check all active price alerts and trigger notifications for price drops';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔔 Checking price alerts...');
        
        $activeAlerts = PriceAlert::with(['product', 'user'])
            ->where('is_active', true)
            ->get();

        if ($activeAlerts->isEmpty()) {
            $this->info('No active price alerts found.');
            return 0;
        }

        $this->info("Found {$activeAlerts->count()} active price alerts.");
        $this->newLine();

        $triggeredCount = 0;

        foreach ($activeAlerts as $alert) {
            $product = $alert->product;
            
            // Get the lowest current price for this product
            $lowestPrice = $product->stock
                ->where('in_stock', true)
                ->min('price') ?? 0;

            if ($lowestPrice === 0) {
                $this->line("⚠️  {$product->name}: No stock available");
                continue;
            }

            $this->line("📦 {$product->name}: Current lowest price ₹" . number_format($lowestPrice / 100, 2));

            // Check if alert should be triggered
            if ($alert->shouldTrigger($lowestPrice)) {
                $this->line("🔔 ALERT TRIGGERED: Price dropped below ₹" . number_format($alert->target_price_in_rupees, 2));
                
                // Trigger the alert
                $alert->trigger();
                
                // Send notification to user
                $currentPriceInRupees = $lowestPrice / 100;
                $alert->user->notify(new PriceDropAlert($alert, $currentPriceInRupees));
                
                $this->line("📧 Notification sent to user: " . $alert->user->email);
                
                // Log the alert
                Log::info("Price alert triggered for {$product->name}", [
                    'user_id' => $alert->user_id,
                    'product_id' => $product->id,
                    'target_price' => $alert->target_price_in_rupees,
                    'current_price' => $currentPriceInRupees,
                    'notification_sent' => true,
                ]);

                $triggeredCount++;
            } else {
                $this->line("✅ Price above target (₹" . number_format($alert->target_price_in_rupees, 2) . ")");
            }
        }

        $this->newLine();
        
        if ($triggeredCount > 0) {
            $this->info("🎉 {$triggeredCount} price alerts triggered!");
        } else {
            $this->info("✅ No price alerts triggered.");
        }

        return 0;
    }
}
