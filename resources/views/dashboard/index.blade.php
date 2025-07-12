<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Stock Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>

</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-green-700">{{ session('success') }}</p>
            </div>
        @endif



        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Stock Tracker Dashboard</h1>
            <div class="flex gap-4 items-center">
                <a href="/products" class="text-blue-500 hover:text-blue-600">Manage Products</a>
                <a href="/retailers" class="text-blue-500 hover:text-blue-600">Manage Retailers</a>
                <!-- Notification Bell -->
                <div class="relative" id="notificationContainer">
                    <button class="relative focus:outline-none" id="notificationBell">
                        <svg class="w-7 h-7 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if($notifications->count() > 0)
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1.5">{{ $notifications->count() }}</span>
                        @endif
                    </button>
                    <!-- Dropdown -->
                    <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-96 bg-white border border-gray-200 rounded-lg shadow-lg z-50 max-h-96 overflow-y-auto">
                        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                            <span class="font-semibold text-gray-800">Notifications ({{ $notifications->count() }})</span>
                            @if($notifications->count() > 0)
                                <form method="POST" action="/notifications/mark-all-read">
                                    @csrf
                                    <button type="submit" class="text-xs text-blue-500 hover:underline" onclick="return confirm('This will mark notifications as read and deactivate the related price alerts. Continue?')">Mark all as read & deactivate alerts</button>
                                </form>
                            @endif
                        </div>
                        <div class="divide-y divide-gray-100">
                            @forelse($notifications as $notification)
                                <div class="p-4 hover:bg-orange-50">
                                    <div class="font-medium text-gray-900">{{ $notification->data['product_name'] ?? 'Product' }} price dropped!</div>
                                    <div class="text-sm text-gray-700 mt-1">
                                        Target: <span class="font-semibold">₹{{ number_format($notification->data['target_price'] ?? 0, 2) }}</span><br>
                                        Current: <span class="font-semibold">₹{{ number_format($notification->data['current_price'] ?? 0, 2) }}</span>
                                        @if(isset($notification->data['retailer_name']))
                                            <br>At: <span class="font-medium text-blue-600">{{ $notification->data['retailer_name'] }}</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center justify-between mt-2">
                                        <div class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</div>
                                        @if(isset($notification->data['product_url']) && $notification->data['product_url'])
                                            <a href="{{ $notification->data['product_url'] }}" target="_blank" class="text-xs text-blue-500 hover:text-blue-700 hover:underline">
                                                View Product →
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="p-4 text-gray-500 text-center">No new notifications</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Statistics Cards Row 1 -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Products</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_products'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Retailers</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_retailers'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Stock Entries</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_stock_entries'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Stock Entries Out of Stock</p>
                        <p class="text-2xl font-bold text-red-600">{{ $stats['out_of_stock_count'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Status Cards Row 2 -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <!-- Products In Stock -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-green-600">Products In Stock</h2>
                    <span class="inline-block bg-green-100 text-green-700 text-sm font-bold px-3 py-1 rounded-full border border-green-300">{{ $productsWithStock->count() }}</span>
                </div>
                @if($productsWithStock->count() > 0)
                    <div class="space-y-3">
                        @foreach($productsWithStock as $product)
                            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $product->name }}</h3>
                                    <p class="text-sm text-gray-500">
                                        @foreach($product->stock->where('in_stock', true) as $stock)
                                            {{ $stock->retailer->name }} (₹{{ number_format($stock->price / 100, 2) }})
                                            @if(!$loop->last), @endif
                                        @endforeach
                                    </p>
                                </div>
                                <span class="text-green-600 text-sm font-medium">✅ In Stock</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">No products currently in stock.</p>
                @endif
            </div>

            <!-- Products Out of Stock -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-red-600">Products Out of Stock</h2>
                    <span class="inline-block bg-red-100 text-red-700 text-sm font-bold px-3 py-1 rounded-full border border-red-300">{{ $productsOutOfStock->count() }}</span>
                </div>
                @if($productsOutOfStock->count() > 0)
                    <div class="space-y-3">
                        @foreach($productsOutOfStock as $product)
                            <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $product->name }}</h3>
                                    <p class="text-sm text-gray-500">
                                        @if($product->stock->count() === 0)
                                            <span class="italic text-gray-400">No stock entries</span>
                                        @else
                                            @foreach($product->stock->where('in_stock', false) as $stock)
                                                {{ $stock->retailer->name }} (₹{{ number_format($stock->price / 100, 2) }})
                                                @if(!$loop->last), @endif
                                            @endforeach
                                        @endif
                                    </p>
                                </div>
                                <span class="text-red-600 text-sm font-medium">❌ Out of Stock</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">All products are in stock!</p>
                @endif
            </div>
        </div>

        <!-- Recent Products -->
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Recent Products</h2>
            @if($recentProducts->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($recentProducts as $product)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-medium text-gray-900 mb-2">{{ $product->name }}</h3>
                            <div class="flex items-center justify-between">
                                <p class="text-sm text-gray-500">
                                    @if($product->stock->count() > 0)
                                        {{ $product->stock->count() }} retailer(s)
                                    @else
                                        No retailers
                                    @endif
                                </p>
                                <span class="text-sm {{ $product->inStock() ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $product->inStock() ? 'In Stock' : 'Out of Stock' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No products added yet.</p>
            @endif
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Quick Actions</h2>
            <div class="flex flex-wrap gap-4">
                <a href="/products" class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                    Add New Product
                </a>
                <a href="/retailers" class="px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                    Add New Retailer
                </a>
                <a href="/stock-status" class="px-6 py-3 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-colors">
                    <i class="fas fa-chart-line mr-2"></i>Advanced Stock Analysis
                </a>
                <a href="/stock-history" class="px-6 py-3 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                    <i class="fas fa-history mr-2"></i>Stock History & Trends
                </a>
                <a href="/bulk-update" class="px-6 py-3 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Bulk Update Stock
                </a>
                <a href="/export" class="px-6 py-3 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-colors">
                    <i class="fas fa-download text-2xl mb-2"></i>
                    <h3 class="font-semibold">Export Data</h3>
                    <p class="text-sm opacity-90">Export stock data, history & logs</p>
                </a>

                <a href="/price-alerts" class="px-6 py-3 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                    <i class="fas fa-bell text-2xl mb-2"></i>
                    <h3 class="font-semibold">Price Alerts</h3>
                    <p class="text-sm opacity-90">Set alerts for price drops</p>
                </a>
            </div>
        </div>

        <!-- Recent Activity Feed -->
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Recent Activity Feed</h2>
            @if($recentActivity->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($recentActivity as $activity)
                        <li class="py-4 flex items-start gap-4">
                            <div class="flex-shrink-0">
                                @if($activity->action === 'created')
                                    <span class="inline-block p-2 bg-green-100 text-green-600 rounded-full">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    </span>
                                @elseif($activity->action === 'updated')
                                    <span class="inline-block p-2 bg-yellow-100 text-yellow-600 rounded-full">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v16h16V4H4zm4 4h8v8H8V8z"></path></svg>
                                    </span>
                                @elseif($activity->action === 'deleted')
                                    <span class="inline-block p-2 bg-red-100 text-red-600 rounded-full">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </span>
                                @else
                                    <span class="inline-block p-2 bg-gray-100 text-gray-600 rounded-full">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/></svg>
                                    </span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-gray-900">{{ $activity->user ? $activity->user->name : 'System' }}</span>
                                    <span class="text-xs text-gray-400">{{ $activity->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="mt-1 text-sm text-gray-700">
                                    <span class="font-medium capitalize">{{ $activity->action }}</span>
                                    <span class="text-gray-500">{{ $activity->subject_type }} #{{ $activity->subject_id }}</span>
                                    <span class="block text-gray-500">{{ $activity->description }}</span>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500">No recent activity yet.</p>
            @endif
        </div>
    </div>

    <script>
        // Notification dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            const notificationBell = document.getElementById('notificationBell');
            const notificationDropdown = document.getElementById('notificationDropdown');
            
            if (notificationBell && notificationDropdown) {
                // Toggle dropdown on bell click
                notificationBell.addEventListener('click', function(e) {
                    e.stopPropagation();
                    notificationDropdown.classList.toggle('hidden');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!notificationBell.contains(e.target) && !notificationDropdown.contains(e.target)) {
                        notificationDropdown.classList.add('hidden');
                    }
                });
                
                // Close dropdown when clicking on dropdown content (optional)
                notificationDropdown.addEventListener('click', function(e) {
                    if (e.target.tagName === 'BUTTON' && e.target.textContent.includes('Mark all as read')) {
                        // Keep dropdown open for a moment to show the action
                        setTimeout(() => {
                            notificationDropdown.classList.add('hidden');
                        }, 500);
                    }
                });
            }
        });
    </script>
</body>
</html> 