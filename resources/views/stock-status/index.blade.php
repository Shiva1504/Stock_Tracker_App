<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            
            <!-- Search Input -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-search text-blue-600 mr-2"></i>
                    Search
                </label>
                <div class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        id="searchInput"
                        placeholder="Search products, retailers, or SKU..." 
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            
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
        let priceChart;
        let currentFilterData = {};

        // Initialize Price Distribution Chart
        function initializePriceChart(data) {
            const ctx = document.getElementById('priceChart').getContext('2d');
            
            if (priceChart) {
                priceChart.destroy();
            }

            priceChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.map(item => item.price_range),
                    datasets: [{
                        data: data.map(item => item.count),
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
        }

        // Initialize with default data
        const priceData = @json($stockAnalytics['price_distribution']);
        initializePriceChart(priceData);

        // Stock Status Checking with dynamic updates
        function checkStock() {
            const form = document.getElementById('stockFilterForm');
            const formData = new FormData(form);
            const searchValue = document.getElementById('searchInput').value;
            
            const filterData = Object.fromEntries(formData);
            if (searchValue) {
                filterData.search = searchValue;
            }

            // Show loading state
            document.getElementById('resultsSection').classList.add('hidden');
            showLoadingState();

            fetch('/stock-status/check', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(filterData)
            })
            .then(response => response.json())
            .then(data => {
                currentFilterData = data;
                displayResults(data);
                updateAnalytics(data);
                hideLoadingState();
            })
            .catch(error => {
                console.error('Error:', error);
                hideLoadingState();
            });
        }

        function showLoadingState() {
            const resultsSection = document.getElementById('resultsSection');
            resultsSection.classList.remove('hidden');
            resultsSection.innerHTML = `
                <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                    <div class="flex items-center justify-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                        <span class="ml-3 text-gray-600">Loading results...</span>
                    </div>
                </div>
            `;
        }

        function hideLoadingState() {
            // Loading state will be replaced by actual results
        }

        function displayResults(data) {
            const resultsSection = document.getElementById('resultsSection');
            const resultsCount = document.getElementById('resultsCount');
            const resultsList = document.getElementById('resultsList');

            resultsSection.classList.remove('hidden');
            resultsSection.innerHTML = `
                <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                    <h2 class="text-xl font-semibold mb-4 text-gray-900">
                        <i class="fas fa-list text-blue-600 mr-2"></i>
                        Search Results
                    </h2>
                    <div id="resultsCount" class="mb-4"></div>
                    <div id="resultsList" class="space-y-4"></div>
                </div>
            `;

            const newResultsCount = document.getElementById('resultsCount');
            const newResultsList = document.getElementById('resultsList');

            newResultsCount.innerHTML = `<p class="text-lg font-medium text-gray-900">Found ${data.count} results</p>`;

            if (data.data.length === 0) {
                newResultsList.innerHTML = '<p class="text-gray-500 text-center py-8">No stock items found matching your criteria.</p>';
                return;
            }

            newResultsList.innerHTML = data.data.map(stock => `
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900">${stock.product.name}</h4>
                        <p class="text-sm text-gray-500">${stock.retailer.name}</p>
                        <p class="text-sm text-gray-500">₹${(stock.price / 100).toFixed(2)}</p>
                    </div>
                    <div class="text-right">
                        <a href="${stock.url}" target="_blank" class="text-blue-500 hover:text-blue-600 text-sm">
                            <i class="fas fa-external-link-alt mr-1"></i>View
                        </a>
                        <span class="ml-2 px-2 py-1 rounded text-sm font-medium ${stock.in_stock ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-red-100 text-red-700 border border-red-300'}">
                            ${stock.in_stock ? 'In Stock' : 'Out of Stock'}
                        </span>
                    </div>
                </div>
            `).join('');
        }

        function updateAnalytics(data) {
            // Update price distribution chart based on filtered data
            const priceDistribution = calculatePriceDistribution(data.data);
            initializePriceChart(priceDistribution);

            // Update retailer performance based on filtered data
            updateRetailerPerformance(data.data);

            // Update quick insights based on filtered data
            updateQuickInsights(data.data);
        }

        function calculatePriceDistribution(stockData) {
            const priceRanges = {
                'Under ₹50': 0,
                '₹50 - ₹100': 0,
                '₹100 - ₹250': 0,
                '₹250 - ₹500': 0,
                'Above ₹500': 0
            };

            stockData.forEach(stock => {
                const price = stock.price / 100;
                if (price <= 50) priceRanges['Under ₹50']++;
                else if (price <= 100) priceRanges['₹50 - ₹100']++;
                else if (price <= 250) priceRanges['₹100 - ₹250']++;
                else if (price <= 500) priceRanges['₹250 - ₹500']++;
                else priceRanges['Above ₹500']++;
            });

            return Object.entries(priceRanges).map(([range, count]) => ({
                price_range: range,
                count: count
            }));
        }

        function updateRetailerPerformance(stockData) {
            const retailerStats = {};
            
            stockData.forEach(stock => {
                const retailerName = stock.retailer.name;
                if (!retailerStats[retailerName]) {
                    retailerStats[retailerName] = { total: 0, inStock: 0 };
                }
                retailerStats[retailerName].total++;
                if (stock.in_stock) retailerStats[retailerName].inStock++;
            });

            const retailerPerformanceDiv = document.querySelector('.bg-white.rounded-xl.shadow-lg.p-6:nth-child(2) .space-y-3');
            if (retailerPerformanceDiv) {
                retailerPerformanceDiv.innerHTML = Object.entries(retailerStats).map(([name, stats]) => `
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">${name}</p>
                            <p class="text-sm text-gray-500">${stats.total} total items</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-green-600">${stats.inStock} in stock</p>
                            <div class="w-20 bg-gray-200 rounded-full h-2 mt-1">
                                <div class="bg-green-500 h-2 rounded-full" style="width: ${stats.total > 0 ? (stats.inStock / stats.total * 100) : 0}%"></div>
                            </div>
                        </div>
                    </div>
                `).join('');
            }
        }

        function updateQuickInsights(stockData) {
            if (stockData.length === 0) {
                updateInsightsSection('No data available with current filters');
                return;
            }

            // Find lowest price product in stock
            const inStockItems = stockData.filter(stock => stock.in_stock);
            const lowestPrice = inStockItems.length > 0 ? 
                inStockItems.reduce((min, stock) => stock.price < min.price ? stock : min) : null;

            // Find highest price product
            const highestPrice = stockData.reduce((max, stock) => stock.price > max.price ? stock : max);

            // Find most available product
            const productCounts = {};
            stockData.forEach(stock => {
                const productName = stock.product.name;
                productCounts[productName] = (productCounts[productName] || 0) + 1;
            });
            const mostAvailable = Object.entries(productCounts)
                .sort(([,a], [,b]) => b - a)[0];

            updateInsightsSection(lowestPrice, highestPrice, mostAvailable);
        }

        function updateInsightsSection(lowestPrice, highestPrice, mostAvailable) {
            const insightsSection = document.querySelector('.grid.grid-cols-1.md\\:grid-cols-3.gap-6.mb-8');
            if (!insightsSection) return;

            insightsSection.innerHTML = `
                ${lowestPrice ? `
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                    <h3 class="text-lg font-semibold mb-2 text-gray-900">
                        <i class="fas fa-tags text-green-600 mr-2"></i>
                        Best Deal
                    </h3>
                    <p class="text-sm text-gray-600">${lowestPrice.product.name}</p>
                    <p class="text-xl font-bold text-green-600">₹${(lowestPrice.price / 100).toFixed(2)}</p>
                    <p class="text-xs text-gray-500">at ${lowestPrice.retailer.name}</p>
                </div>
                ` : ''}
                
                ${highestPrice ? `
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500">
                    <h3 class="text-lg font-semibold mb-2 text-gray-900">
                        <i class="fas fa-crown text-red-600 mr-2"></i>
                        Premium Item
                    </h3>
                    <p class="text-sm text-gray-600">${highestPrice.product.name}</p>
                    <p class="text-xl font-bold text-red-600">₹${(highestPrice.price / 100).toFixed(2)}</p>
                    <p class="text-xs text-gray-500">at ${highestPrice.retailer.name}</p>
                </div>
                ` : ''}
                
                ${mostAvailable ? `
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                    <h3 class="text-lg font-semibold mb-2 text-gray-900">
                        <i class="fas fa-star text-blue-600 mr-2"></i>
                        Most Available
                    </h3>
                    <p class="text-sm text-gray-600">${mostAvailable[0]}</p>
                    <p class="text-xl font-bold text-blue-600">${mostAvailable[1]} retailers</p>
                    <p class="text-xs text-gray-500">Widely available</p>
                </div>
                ` : ''}
            `;
        }

        function resetFilters() {
            document.getElementById('stockFilterForm').reset();
            document.getElementById('searchInput').value = '';
            // Reset to original data
            initializePriceChart(priceData);
            checkStock(); // This will show all data
        }

        // Add event listeners to form inputs for real-time filtering
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('stockFilterForm');
            const inputs = form.querySelectorAll('select, input');
            const searchInput = document.getElementById('searchInput');
            
            inputs.forEach(input => {
                input.addEventListener('change', function() {
                    checkStock();
                });
            });

            // Search input with debouncing
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    checkStock();
                }, 300);
            });

            // Initial load
            checkStock();
        });
    </script>
</body>
</html> 