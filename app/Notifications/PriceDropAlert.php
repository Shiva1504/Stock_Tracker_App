<?php

namespace App\Notifications;

use App\Models\PriceAlert;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PriceDropAlert extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public PriceAlert $priceAlert,
        public float $currentPrice
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database']; // Only database channel for now
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $product = $this->priceAlert->product;
        $targetPrice = '₹' . number_format($this->priceAlert->target_price_in_rupees, 2);
        $currentPriceFormatted = '₹' . number_format($this->currentPrice, 2);
        $savings = $this->priceAlert->target_price_in_rupees - $this->currentPrice;
        $savingsFormatted = '₹' . number_format($savings, 2);
        
        return (new MailMessage)
            ->subject("🔔 Price Drop Alert: {$product->name} is now below your target!")
            ->greeting("Hello!")
            ->line("Great news! The price of {$product->name} has dropped below your target price.")
            ->line("Your target price: {$targetPrice}")
            ->line("Current lowest price: {$currentPriceFormatted}")
            ->line("Potential savings: {$savingsFormatted}")
            ->action('View Product Details', url('/products/' . $product->id))
            ->line('This alert will be checked again in 24 hours to prevent spam.')
            ->line('Thank you for using our Stock Tracker!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        // Get the lowest price stock entry with URL for this product
        $lowestPriceStock = $this->priceAlert->product->stock
            ->where('in_stock', true)
            ->sortBy('price')
            ->first();
        
        return [
            'price_alert_id' => $this->priceAlert->id,
            'product_id' => $this->priceAlert->product->id,
            'product_name' => $this->priceAlert->product->name,
            'target_price' => $this->priceAlert->target_price_in_rupees,
            'current_price' => $this->currentPrice,
            'savings' => $this->priceAlert->target_price_in_rupees - $this->currentPrice,
            'product_url' => $lowestPriceStock ? $lowestPriceStock->url : null,
            'retailer_name' => $lowestPriceStock ? $lowestPriceStock->retailer->name : null,
        ];
    }
} 