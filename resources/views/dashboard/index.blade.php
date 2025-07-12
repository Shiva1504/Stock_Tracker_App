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

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
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
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">In Stock</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['in_stock_count'] }}</p>
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
                        <p class="text-sm font-medium text-gray-600">Out of Stock</p>
                        <p class="text-2xl font-bold text-red-600">{{ $stats['out_of_stock_count'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Products In Stock -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4 text-green-600">Products In Stock</h2>
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
                <h2 class="text-xl font-semibold mb-4 text-red-600">Products Out of Stock</h2>
                @if($productsOutOfStock->count() > 0)
                    <div class="space-y-3">
                        @foreach($productsOutOfStock as $product)
                            <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $product->name }}</h3>
                                    <p class="text-sm text-gray-500">
                                        @foreach($product->stock->where('in_stock', false) as $stock)
                                            {{ $stock->retailer->name }} (₹{{ number_format($stock->price / 100, 2) }})
                                            @if(!$loop->last), @endif
                                        @endforeach
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
                <button onclick="alert('Use: php artisan stock:check')" class="px-6 py-3 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-colors">
                    Check Stock Status
                </button>
            </div>
        </div>
    </div>
</body>
</html> 