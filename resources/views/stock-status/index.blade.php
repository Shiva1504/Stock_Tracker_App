<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Status Analysis - Stock Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-chart-line text-blue-600 mr-3"></i>
                    Stock Status Analysis
                </h1>
                <p class="text-gray-600">Comprehensive stock monitoring and analytics dashboard</p>
            </div>
            <div class="flex gap-4">
                <a href="/" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                    <i class="fas fa-home mr-2"></i>Dashboard
                </a>
                <a href="/products" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                    <i class="fas fa-box mr-2"></i>Products
                </a>
            </div>
        </div>

        <!-- Quick Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-boxes text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Products</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_products'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-store text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Retailers</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_retailers'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">In Stock</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['in_stock_count'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 rounded-full">
                        <i class="fas fa-times-circle text-red-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Out of Stock</p>
                        <p class="text-2xl font-bold text-red-600">{{ $stats['out_of_stock_count'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Advanced Filters -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-900">
                <i class="fas fa-filter text-blue-600 mr-2"></i>
                Advanced Stock Filters
            </h2>
            <form id="stockFilterForm" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Product</label>
                    <select name="product_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Products</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Retailer</label>
                    <select name="retailer_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Retailers</option>
                        @foreach($retailers as $retailer)
                            <option value="{{ $retailer->id }}">{{ $retailer->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Price Range</label>
                    <select name="price_range" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Prices</option>
                        <option value="under_50">Under ₹50</option>
                        <option value="50_100">₹50 - ₹100</option>
                        <option value="100_250">₹100 - ₹250</option>
                        <option value="250_500">₹250 - ₹500</option>
                        <option value="above_500">Above ₹500</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stock Status</label>
                    <select name="stock_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="1">In Stock</option>
                        <option value="0">Out of Stock</option>
                    </select>
                </div>
            </form>

            <div class="mt-4 flex gap-4">
                <button onclick="checkStock()" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                    <i class="fas fa-search mr-2"></i>Check Stock Status
                </button>
                <button onclick="resetFilters()" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-refresh mr-2"></i>Reset Filters
                </button>
            </div>
        </div>

        <!-- Results Section -->
        <div id="resultsSection" class="hidden">
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <h2 class="text-xl font-semibold mb-4 text-gray-900">
                    <i class="fas fa-list text-blue-600 mr-2"></i>
                    Search Results
                </h2>
                <div id="resultsCount" class="mb-4"></div>
                <div id="resultsList" class="space-y-4"></div>
            </div>
        </div>

        <!-- Analytics Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Price Distribution Chart -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-900">
                    <i class="fas fa-chart-pie text-blue-600 mr-2"></i>
                    Price Distribution
                </h3>
                <canvas id="priceChart" width="400" height="200"></canvas>
            </div>

            <!-- Retailer Performance -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-900">
                    <i class="fas fa-trophy text-yellow-600 mr-2"></i>
                    Retailer Performance
                </h3>
                <div class="space-y-3">
                    @foreach($stockAnalytics['retailer_performance'] as $retailer)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">{{ $retailer->name }}</p>
                                <p class="text-sm text-gray-500">{{ $retailer->stock_count }} total items</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-green-600">{{ $retailer->stock_count }} in stock</p>
                                <div class="w-20 bg-gray-200 rounded-full h-2 mt-1">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $retailer->availability_rate }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Quick Insights -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            @if($stats['lowest_price_product'])
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                <h3 class="text-lg font-semibold mb-2 text-gray-900">
                    <i class="fas fa-tags text-green-600 mr-2"></i>
                    Best Deal
                </h3>
                <p class="text-sm text-gray-600">{{ $stats['lowest_price_product']->product->name }}</p>
                <p class="text-xl font-bold text-green-600">₹{{ number_format($stats['lowest_price_product']->price / 100, 2) }}</p>
                <p class="text-xs text-gray-500">at {{ $stats['lowest_price_product']->retailer->name }}</p>
            </div>
            @endif

            @if($stats['highest_price_product'])
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500">
                <h3 class="text-lg font-semibold mb-2 text-gray-900">
                    <i class="fas fa-crown text-red-600 mr-2"></i>
                    Premium Item
                </h3>
                <p class="text-sm text-gray-600">{{ $stats['highest_price_product']->product->name }}</p>
                <p class="text-xl font-bold text-red-600">₹{{ number_format($stats['highest_price_product']->price / 100, 2) }}</p>
                <p class="text-xs text-gray-500">at {{ $stats['highest_price_product']->retailer->name }}</p>
            </div>
            @endif

            @if($stats['most_available_product'])
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                <h3 class="text-lg font-semibold mb-2 text-gray-900">
                    <i class="fas fa-star text-blue-600 mr-2"></i>
                    Most Available
                </h3>
                <p class="text-sm text-gray-600">{{ $stats['most_available_product']->name }}</p>
                <p class="text-xl font-bold text-blue-600">{{ $stats['most_available_product']->stock_count }} retailers</p>
                <p class="text-xs text-gray-500">Widely available</p>
            </div>
            @endif
        </div>
    </div>

    <script>
        // Price Distribution Chart
        const priceData = @json($stockAnalytics['price_distribution']);
        const ctx = document.getElementById('priceChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: priceData.map(item => item.price_range),
                datasets: [{
                    data: priceData.map(item => item.count),
                    backgroundColor: [
                        '#3B82F6',
                        '#10B981',
                        '#F59E0B',
                        '#EF4444',
                        '#8B5CF6'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Stock Status Checking
        function checkStock() {
            const form = document.getElementById('stockFilterForm');
            const formData = new FormData(form);

            fetch('/stock-status/check', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(Object.fromEntries(formData))
            })
            .then(response => response.json())
            .then(data => {
                displayResults(data);
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function displayResults(data) {
            const resultsSection = document.getElementById('resultsSection');
            const resultsCount = document.getElementById('resultsCount');
            const resultsList = document.getElementById('resultsList');

            resultsSection.classList.remove('hidden');
            resultsCount.innerHTML = `<p class="text-lg font-medium text-gray-900">Found ${data.count} results</p>`;

            if (data.data.length === 0) {
                resultsList.innerHTML = '<p class="text-gray-500 text-center py-8">No stock items found matching your criteria.</p>';
                return;
            }

            resultsList.innerHTML = data.data.map(stock => `
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900">${stock.product.name}</h4>
                        <p class="text-sm text-gray-500">${stock.retailer.name}</p>
                        <p class="text-sm text-gray-500">₹${(stock.price / 100).toFixed(2)}</p>
                    </div>
                    <div class="text-right">
                        <span class="px-3 py-1 rounded-full text-xs font-medium ${stock.in_stock ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                            ${stock.in_stock ? 'In Stock' : 'Out of Stock'}
                        </span>
                        <div class="mt-2">
                            <a href="${stock.url}" target="_blank" class="text-blue-500 hover:text-blue-600 text-sm">
                                <i class="fas fa-external-link-alt mr-1"></i>View
                            </a>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function resetFilters() {
            document.getElementById('stockFilterForm').reset();
            document.getElementById('resultsSection').classList.add('hidden');
        }

        // Auto-check on page load
        document.addEventListener('DOMContentLoaded', function() {
            checkStock();
        });
    </script>
</body>
</html> 