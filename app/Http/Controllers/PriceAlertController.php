<?php

namespace App\Http\Controllers;

use App\Models\PriceAlert;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

class PriceAlertController extends Controller
{
    public function index()
    {
        $priceAlerts = PriceAlert::with(['product', 'user'])
            ->where('user_id', auth()->id() ?? 1)
            ->orderBy('created_at', 'desc')
            ->get();

        $products = Product::all();

        return view('price-alerts.index', compact('priceAlerts', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'target_price' => 'required|numeric|min:0.01',
        ]);

        $userId = auth()->id() ?? 1;

        // Check if user already has an alert for this product
        $existingAlert = PriceAlert::where('user_id', $userId)
            ->where('product_id', $request->product_id)
            ->first();

        if ($existingAlert) {
            return redirect()->back()->with('error', 'You already have a price alert for this product.');
        }

        $priceAlert = PriceAlert::create([
            'user_id' => $userId,
            'product_id' => $request->product_id,
            'target_price' => $request->target_price * 100, // Convert to paise
        ]);

        // Check if current price already meets the target and stock is available
        $this->checkAndTriggerAlert($priceAlert);

        ActivityLog::create([
            'user_id' => $userId,
            'action' => 'created',
            'subject_type' => 'PriceAlert',
            'subject_id' => $priceAlert->id,
            'description' => 'Created price alert for ' . $priceAlert->product->name . ' at ₹' . number_format($request->target_price, 2),
        ]);

        return redirect()->back()->with('success', 'Price alert created successfully!');
    }

    public function update(Request $request, PriceAlert $priceAlert)
    {
        $userId = auth()->id() ?? 1;
        
        // Ensure user owns this alert
        if ($priceAlert->user_id !== $userId) {
            abort(403);
        }

        $request->validate([
            'target_price' => 'required|numeric|min:0.01',
            'is_active' => 'nullable',
        ]);

        $oldPrice = $priceAlert->target_price_in_rupees;
        
        $priceAlert->update([
            'target_price' => $request->target_price * 100,
            'is_active' => $request->has('is_active'),
        ]);

        ActivityLog::create([
            'user_id' => $userId,
            'action' => 'updated',
            'subject_type' => 'PriceAlert',
            'subject_id' => $priceAlert->id,
            'description' => 'Updated price alert for ' . $priceAlert->product->name . ' from ₹' . number_format($oldPrice, 2) . ' to ₹' . number_format($request->target_price, 2),
        ]);

        return redirect()->back()->with('success', 'Price alert updated successfully!');
    }

    public function destroy(PriceAlert $priceAlert)
    {
        $userId = auth()->id() ?? 1;
        
        // Ensure user owns this alert
        if ($priceAlert->user_id !== $userId) {
            abort(403);
        }

        $productName = $priceAlert->product->name;
        $priceAlert->delete();

        ActivityLog::create([
            'user_id' => $userId,
            'action' => 'deleted',
            'subject_type' => 'PriceAlert',
            'subject_id' => $priceAlert->id,
            'description' => 'Deleted price alert for ' . $productName,
        ]);

        return redirect()->back()->with('success', 'Price alert deleted successfully!');
    }

    public function toggle(PriceAlert $priceAlert)
    {
        $userId = auth()->id() ?? 1;
        
        // Ensure user owns this alert
        if ($priceAlert->user_id !== $userId) {
            abort(403);
        }

        $wasActive = $priceAlert->is_active;
        $priceAlert->update([
            'is_active' => !$priceAlert->is_active
        ]);

        // If we're activating the alert, reset the last_triggered_at and check if it should trigger immediately
        if (!$wasActive && $priceAlert->is_active) {
            $priceAlert->update(['last_triggered_at' => null]);
            // Check if current price already meets the target and stock is available
            $this->checkAndTriggerAlert($priceAlert);
        }

        $status = $priceAlert->is_active ? 'activated' : 'deactivated';
        
        ActivityLog::create([
            'user_id' => $userId,
            'action' => 'updated',
            'subject_type' => 'PriceAlert',
            'subject_id' => $priceAlert->id,
            'description' => $status . ' price alert for ' . $priceAlert->product->name,
        ]);

        return redirect()->back()->with('success', 'Price alert ' . $status . ' successfully!');
    }

    /**
     * Check if a price alert should be triggered immediately based on current conditions
     */
    private function checkAndTriggerAlert(PriceAlert $priceAlert)
    {
        $product = $priceAlert->product;
        
        // Get the lowest current price for this product
        $lowestPrice = $product->stock
            ->where('in_stock', true)
            ->min('price') ?? 0;

        // Check if stock is available and price meets target
        if ($lowestPrice > 0 && $priceAlert->shouldTrigger($lowestPrice)) {
            // Trigger the alert immediately
            $priceAlert->trigger();
            
            // Send notification to user
            $currentPriceInRupees = $lowestPrice / 100;
            $priceAlert->user->notify(new \App\Notifications\PriceDropAlert($priceAlert, $currentPriceInRupees));
            
            // Log the immediate trigger
            \Log::info("Price alert triggered immediately for {$product->name}", [
                'user_id' => $priceAlert->user_id,
                'product_id' => $product->id,
                'target_price' => $priceAlert->target_price_in_rupees,
                'current_price' => $currentPriceInRupees,
                'trigger_type' => 'immediate_on_activation',
            ]);
        }
    }
}
