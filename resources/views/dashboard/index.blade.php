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
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Stock Tracker Dashboard</h1>
            <div class="flex gap-4">
                <a href="/products" class="text-blue-500 hover:text-blue-600">Manage Products</a>
                <a href="/retailers" class="text-blue-500 hover:text-blue-600">Manage Retailers</a>
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
</body>
</html> 