<?php

namespace App\Notifications;

use App\Models\Product;
use App\Models\Stock;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProductBackInStock extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Product $product,
        public Stock $stock
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $price = '$' . number_format($this->stock->price / 100, 2);
        
        return (new MailMessage)
            ->subject("🎉 {$this->product->name} is back in stock!")
            ->line("Great news! {$this->product->name} is now back in stock at {$this->stock->retailer->name}.")
            ->line("Price: {$price}")
            ->action('View Product', $this->stock->url)
            ->line('Thank you for using our stock tracker!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'retailer_name' => $this->stock->retailer->name,
            'price' => $this->stock->price,
            'url' => $this->stock->url,
        ];
    }
}
