<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Stock Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Mobile-specific styles */
        @media (max-width: 768px) {
            .mobile-menu {
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }
            .mobile-menu.open {
                transform: translateX(0);
            }
            .mobile-overlay {
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.3s ease-in-out;
            }
            .mobile-overlay.open {
                opacity: 1;
                visibility: visible;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Mobile Navigation Overlay -->
    <div id="mobileOverlay" class="mobile-overlay fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"></div>
    
    <!-- Mobile Sidebar Menu -->
    <div id="mobileMenu" class="mobile-menu fixed left-0 top-0 h-full w-64 bg-white shadow-lg z-50 lg:hidden">
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">Stock Tracker</h2>
                <button id="closeMobileMenu" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <nav class="p-4">
            <ul class="space-y-2">
                <li>
                    <a href="/" class="flex items-center px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg">
                        <i class="fas fa-home mr-3"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="/products" class="flex items-center px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg">
                        <i class="fas fa-box mr-3"></i>
                        Products
                    </a>
                </li>
                <li>
                    <a href="/retailers" class="flex items-center px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg">
                        <i class="fas fa-store mr-3"></i>
                        Retailers
                    </a>
                </li>
                <li>
                    <a href="/stock-status" class="flex items-center px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg">
                        <i class="fas fa-chart-line mr-3"></i>
                        Analytics
                    </a>
                </li>
                <li>
                    <a href="/price-alerts" class="flex items-center px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg">
                        <i class="fas fa-bell mr-3"></i>
                        Price Alerts
                    </a>
                </li>
                <li>
                    <a href="/stock-history" class="flex items-center px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg">
                        <i class="fas fa-history mr-3"></i>
                        History
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <div class="container mx-auto px-4 py-4 lg:py-8">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-green-700">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Mobile Header -->
        <div class="flex items-center justify-between mb-6 lg:hidden">
            <button id="openMobileMenu" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <h1 class="text-xl font-bold text-gray-900">Dashboard</h1>
            <div class="relative" id="notificationContainer">
                <button class="relative focus:outline-none" id="notificationBell">
                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    @if($notifications->count() > 0)
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1.5">{{ $notifications->count() }}</span>
                    @endif
                </button>
                <!-- Mobile Notification Dropdown -->
                <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded-lg shadow-lg z-50 max-h-96 overflow-y-auto">
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

        <!-- Desktop Header -->
        <div class="hidden lg:flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Stock Tracker Dashboard</h1>
            <div class="flex gap-4 items-center">
                <a href="/products" class="text-blue-500 hover:text-blue-600">Manage Products</a>
                <a href="/retailers" class="text-blue-500 hover:text-blue-600">Manage Retailers</a>
                <!-- Desktop Notification Bell -->
                <div class="relative" id="desktopNotificationContainer">
                    <button class="relative focus:outline-none" id="desktopNotificationBell">
                        <svg class="w-7 h-7 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if($notifications->count() > 0)
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1.5">{{ $notifications->count() }}</span>
                        @endif
                    </button>
                    <!-- Desktop Dropdown -->
                    <div id="desktopNotificationDropdown" class="hidden absolute right-0 mt-2 w-96 bg-white border border-gray-200 rounded-lg shadow-lg z-50 max-h-96 overflow-y-auto">
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
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6 lg:mb-8">
            <div class="bg-white rounded-lg shadow-md p-4 lg:p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-5 h-5 lg:w-6 lg:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div class="ml-3 lg:ml-4">
                        <p class="text-xs lg:text-sm font-medium text-gray-600">Products</p>
                        <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ $stats['total_products'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 lg:p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-5 h-5 lg:w-6 lg:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="ml-3 lg:ml-4">
                        <p class="text-xs lg:text-sm font-medium text-gray-600">Retailers</p>
                        <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ $stats['total_retailers'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 lg:p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg class="w-5 h-5 lg:w-6 lg:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div class="ml-3 lg:ml-4">
                        <p class="text-xs lg:text-sm font-medium text-gray-600">Stock Entries</p>
                        <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ $stats['total_stock_entries'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 lg:p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <svg class="w-5 h-5 lg:w-6 lg:h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div class="ml-3 lg:ml-4">
                        <p class="text-xs lg:text-sm font-medium text-gray-600">Out of Stock</p>
                        <p class="text-lg lg:text-2xl font-bold text-red-600">{{ $stats['out_of_stock_count'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Status Cards Row 2 -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8 mb-6 lg:mb-8">
            <!-- Products In Stock -->
            <div class="bg-white rounded-lg shadow-md p-4 lg:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg lg:text-xl font-semibold text-green-600">Products In Stock</h2>
                    <span class="inline-block bg-green-100 text-green-700 text-xs lg:text-sm font-bold px-2 lg:px-3 py-1 rounded-full border border-green-300">{{ $productsWithStock->count() }}</span>
                </div>
                @if($productsWithStock->count() > 0)
                    <div class="space-y-3">
                        @foreach($productsWithStock->take(5) as $product)
                            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-medium text-gray-900 text-sm lg:text-base truncate">{{ $product->name }}</h3>
                                    <p class="text-xs lg:text-sm text-gray-500 truncate">
                                        @foreach($product->stock->where('in_stock', true)->take(2) as $stock)
                                            {{ $stock->retailer->name }} (₹{{ number_format($stock->price / 100, 2) }})
                                            @if(!$loop->last), @endif
                                        @endforeach
                                        @if($product->stock->where('in_stock', true)->count() > 2)
                                            +{{ $product->stock->where('in_stock', true)->count() - 2 }} more
                                        @endif
                                    </p>
                                </div>
                                <span class="text-green-600 text-xs lg:text-sm font-medium ml-2">✅</span>
                            </div>
                        @endforeach
                        @if($productsWithStock->count() > 5)
                            <div class="text-center">
                                <a href="/products" class="text-blue-500 hover:text-blue-600 text-sm">View all {{ $productsWithStock->count() }} products →</a>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-gray-500 text-sm lg:text-base">No products currently in stock.</p>
                @endif
            </div>

            <!-- Products Out of Stock -->
            <div class="bg-white rounded-lg shadow-md p-4 lg:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg lg:text-xl font-semibold text-red-600">Products Out of Stock</h2>
                    <span class="inline-block bg-red-100 text-red-700 text-xs lg:text-sm font-bold px-2 lg:px-3 py-1 rounded-full border border-red-300">{{ $productsOutOfStock->count() }}</span>
                </div>
                @if($productsOutOfStock->count() > 0)
                    <div class="space-y-3">
                        @foreach($productsOutOfStock->take(5) as $product)
                            <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-medium text-gray-900 text-sm lg:text-base truncate">{{ $product->name }}</h3>
                                    <p class="text-xs lg:text-sm text-gray-500 truncate">
                                        @if($product->stock->count() === 0)
                                            <span class="italic text-gray-400">No stock entries</span>
                                        @else
                                            @foreach($product->stock->where('in_stock', false)->take(2) as $stock)
                                                {{ $stock->retailer->name }} (₹{{ number_format($stock->price / 100, 2) }})
                                                @if(!$loop->last), @endif
                                            @endforeach
                                            @if($product->stock->where('in_stock', false)->count() > 2)
                                                +{{ $product->stock->where('in_stock', false)->count() - 2 }} more
                                            @endif
                                        @endif
                                    </p>
                                </div>
                                <span class="text-red-600 text-xs lg:text-sm font-medium ml-2">❌</span>
                            </div>
                        @endforeach
                        @if($productsOutOfStock->count() > 5)
                            <div class="text-center">
                                <a href="/products" class="text-blue-500 hover:text-blue-600 text-sm">View all {{ $productsOutOfStock->count() }} products →</a>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-gray-500 text-sm lg:text-base">All products are in stock!</p>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-4 lg:p-6 mb-6 lg:mb-8">
            <h2 class="text-lg lg:text-xl font-semibold mb-4">Quick Actions</h2>
            <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 lg:gap-4">
                <a href="/products" class="flex flex-col items-center p-4 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors text-center">
                    <i class="fas fa-plus text-xl lg:text-2xl mb-2"></i>
                    <span class="text-xs lg:text-sm font-medium">Add Product</span>
                </a>
                <a href="/retailers" class="flex flex-col items-center p-4 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors text-center">
                    <i class="fas fa-store text-xl lg:text-2xl mb-2"></i>
                    <span class="text-xs lg:text-sm font-medium">Add Retailer</span>
                </a>
                <a href="/stock-status" class="flex flex-col items-center p-4 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-colors text-center">
                    <i class="fas fa-chart-line text-xl lg:text-2xl mb-2"></i>
                    <span class="text-xs lg:text-sm font-medium">Analytics</span>
                </a>
                <a href="/price-alerts" class="flex flex-col items-center p-4 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors text-center">
                    <i class="fas fa-bell text-xl lg:text-2xl mb-2"></i>
                    <span class="text-xs lg:text-sm font-medium">Price Alerts</span>
                </a>
                <a href="/stock-history" class="flex flex-col items-center p-4 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors text-center">
                    <i class="fas fa-history text-xl lg:text-2xl mb-2"></i>
                    <span class="text-xs lg:text-sm font-medium">History</span>
                </a>
                <a href="/bulk-update" class="flex flex-col items-center p-4 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition-colors text-center">
                    <i class="fas fa-edit text-xl lg:text-2xl mb-2"></i>
                    <span class="text-xs lg:text-sm font-medium">Bulk Update</span>
                </a>
            </div>
        </div>

        <!-- Recent Products -->
        <div class="bg-white rounded-lg shadow-md p-4 lg:p-6 mb-6 lg:mb-8">
            <h2 class="text-lg lg:text-xl font-semibold mb-4">Recent Products</h2>
            @if($recentProducts->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($recentProducts as $product)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-medium text-gray-900 mb-2 text-sm lg:text-base truncate">{{ $product->name }}</h3>
                            <div class="flex items-center justify-between">
                                <p class="text-xs lg:text-sm text-gray-500">
                                    @if($product->stock->count() > 0)
                                        {{ $product->stock->count() }} retailer(s)
                                    @else
                                        No retailers
                                    @endif
                                </p>
                                <span class="text-xs lg:text-sm {{ $product->inStock() ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $product->inStock() ? 'In Stock' : 'Out of Stock' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm lg:text-base">No products added yet.</p>
            @endif
        </div>

        <!-- Recent Activity Feed -->
        <div class="bg-white rounded-lg shadow-md p-4 lg:p-6">
            <h2 class="text-lg lg:text-xl font-semibold mb-4">Recent Activity</h2>
            @if($recentActivity->count() > 0)
                <div class="space-y-4">
                    @foreach($recentActivity->take(5) as $activity)
                        <div class="flex items-start gap-3 lg:gap-4">
                            <div class="flex-shrink-0">
                                @if($activity->action === 'created')
                                    <span class="inline-block p-2 bg-green-100 text-green-600 rounded-full">
                                        <svg class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    </span>
                                @elseif($activity->action === 'updated')
                                    <span class="inline-block p-2 bg-yellow-100 text-yellow-600 rounded-full">
                                        <svg class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v16h16V4H4zm4 4h8v8H8V8z"></path></svg>
                                    </span>
                                @elseif($activity->action === 'deleted')
                                    <span class="inline-block p-2 bg-red-100 text-red-600 rounded-full">
                                        <svg class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </span>
                                @else
                                    <span class="inline-block p-2 bg-gray-100 text-gray-600 rounded-full">
                                        <svg class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/></svg>
                                    </span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-gray-900 text-sm lg:text-base">{{ $activity->user ? $activity->user->name : 'System' }}</span>
                                    <span class="text-xs text-gray-400">{{ $activity->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="mt-1 text-xs lg:text-sm text-gray-700">
                                    <span class="font-medium capitalize">{{ $activity->action }}</span>
                                    <span class="text-gray-500">{{ $activity->subject_type }} #{{ $activity->subject_id }}</span>
                                    <div class="text-gray-500 truncate">{{ $activity->description }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if($recentActivity->count() > 5)
                        <div class="text-center pt-2">
                            <a href="/activity-log" class="text-blue-500 hover:text-blue-600 text-sm">View all activity →</a>
                        </div>
                    @endif
                </div>
            @else
                <p class="text-gray-500 text-sm lg:text-base">No recent activity yet.</p>
            @endif
        </div>
    </div>

    <script>
        // Mobile menu functionality
        document.addEventListener('DOMContentLoaded', function() {
            const openMobileMenu = document.getElementById('openMobileMenu');
            const closeMobileMenu = document.getElementById('closeMobileMenu');
            const mobileMenu = document.getElementById('mobileMenu');
            const mobileOverlay = document.getElementById('mobileOverlay');

            if (openMobileMenu && closeMobileMenu && mobileMenu && mobileOverlay) {
                openMobileMenu.addEventListener('click', function() {
                    mobileMenu.classList.add('open');
                    mobileOverlay.classList.add('open');
                    document.body.style.overflow = 'hidden';
                });

                closeMobileMenu.addEventListener('click', function() {
                    mobileMenu.classList.remove('open');
                    mobileOverlay.classList.remove('open');
                    document.body.style.overflow = '';
                });

                mobileOverlay.addEventListener('click', function() {
                    mobileMenu.classList.remove('open');
                    mobileOverlay.classList.remove('open');
                    document.body.style.overflow = '';
                });
            }

            // Notification dropdown functionality
            const notificationBell = document.getElementById('notificationBell');
            const notificationDropdown = document.getElementById('notificationDropdown');
            const desktopNotificationBell = document.getElementById('desktopNotificationBell');
            const desktopNotificationDropdown = document.getElementById('desktopNotificationDropdown');
            
            // Mobile notifications
            if (notificationBell && notificationDropdown) {
                notificationBell.addEventListener('click', function(e) {
                    e.stopPropagation();
                    notificationDropdown.classList.toggle('hidden');
                });
            }
            
            // Desktop notifications
            if (desktopNotificationBell && desktopNotificationDropdown) {
                desktopNotificationBell.addEventListener('click', function(e) {
                    e.stopPropagation();
                    desktopNotificationDropdown.classList.toggle('hidden');
                });
            }
            
            // Close dropdowns when clicking outside
            document.addEventListener('click', function(e) {
                if (notificationBell && notificationDropdown && !notificationBell.contains(e.target) && !notificationDropdown.contains(e.target)) {
                    notificationDropdown.classList.add('hidden');
                }
                if (desktopNotificationBell && desktopNotificationDropdown && !desktopNotificationBell.contains(e.target) && !desktopNotificationDropdown.contains(e.target)) {
                    desktopNotificationDropdown.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html> 