<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAllRead(Request $request)
    {
        $user = auth()->user() ?? \App\Models\User::first();
        if ($user) {
            // Get all unread notifications before marking them as read
            $unreadNotifications = $user->unreadNotifications;
            
            // Mark notifications as read
            $unreadNotifications->markAsRead();
            
            // Deactivate price alerts for the notifications that were just marked as read
            foreach ($unreadNotifications as $notification) {
                if ($notification->type === 'App\Notifications\PriceDropAlert') {
                    $priceAlertId = $notification->data['price_alert_id'] ?? null;
                    if ($priceAlertId) {
                        $priceAlert = \App\Models\PriceAlert::find($priceAlertId);
                        if ($priceAlert && $priceAlert->user_id === $user->id) {
                            $priceAlert->update(['is_active' => false]);
                        }
                    }
                }
            }
        }
        return redirect()->back()->with('success', 'Notifications marked as read and price alerts deactivated.');
    }
} 