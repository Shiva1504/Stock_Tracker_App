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
    <div class="container mx-auto px-4 py-4 lg:py-8">
        <!-- Mobile Header -->
        <div class="flex items-center justify-between mb-6 lg:hidden">
            <a href="/" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-xl font-bold text-gray-900">Analytics</h1>
            <div class="w-8"></div> <!-- Spacer for centering -->
        </div>

        <!-- Desktop Header -->
        <div class="hidden lg:flex items-center justify-between mb-8">
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
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6 lg:mb-8">
            <div class="bg-white rounded-xl shadow-lg p-4 lg:p-6 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="p-2 lg:p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-boxes text-blue-600 text-lg lg:text-xl"></i>
                    </div>
                    <div class="ml-3 lg:ml-4">
                        <p class="text-xs lg:text-sm font-medium text-gray-600">Products</p>
                        <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ $stats['total_products'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-4 lg:p-6 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="p-2 lg:p-3 bg-green-100 rounded-full">
                        <i class="fas fa-store text-green-600 text-lg lg:text-xl"></i>
                    </div>
                    <div class="ml-3 lg:ml-4">
                        <p class="text-xs lg:text-sm font-medium text-gray-600">Retailers</p>
                        <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ $stats['total_retailers'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-4 lg:p-6 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="p-2 lg:p-3 bg-green-100 rounded-full">
                        <i class="fas fa-check-circle text-green-600 text-lg lg:text-xl"></i>
                    </div>
                    <div class="ml-3 lg:ml-4">
                        <p class="text-xs lg:text-sm font-medium text-gray-600">In Stock</p>
                        <p class="text-lg lg:text-2xl font-bold text-green-600">{{ $stats['in_stock_count'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-4 lg:p-6 border-l-4 border-red-500">
                <div class="flex items-center">
                    <div class="p-2 lg:p-3 bg-red-100 rounded-full">
                        <i class="fas fa-times-circle text-red-600 text-lg lg:text-xl"></i>
                    </div>
                    <div class="ml-3 lg:ml-4">
                        <p class="text-xs lg:text-sm font-medium text-gray-600">Out of Stock</p>
                        <p class="text-lg lg:text-2xl font-bold text-red-600">{{ $stats['out_of_stock_count'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Advanced Filters -->
        <div class="bg-white rounded-xl shadow-lg p-4 lg:p-6 mb-6 lg:mb-8">
            <h2 class="text-lg lg:text-xl font-semibold mb-4 text-gray-900">
                <i class="fas fa-filter text-blue-600 mr-2"></i>
                Advanced Stock Filters
            </h2>
            
            <!-- Search Input -->
            <div class="mb-4 lg:mb-6">
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
                        class="w-full pl-10 pr-4 py-2 lg:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            
            <form id="stockFilterForm" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
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

            <div class="mt-4 flex flex-col sm:flex-row gap-3">
                <button onclick="checkStock()" class="w-full sm:w-auto px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                    <i class="fas fa-search mr-2"></i>Check Stock Status
                </button>
                <button onclick="resetFilters()" class="w-full sm:w-auto px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-refresh mr-2"></i>Reset Filters
                </button>
            </div>
        </div>

        <!-- Results Section -->
        <div id="resultsSection" class="hidden">
            <div class="bg-white rounded-xl shadow-lg p-4 lg:p-6 mb-6 lg:mb-8">
                <h2 class="text-lg lg:text-xl font-semibold mb-4 text-gray-900">
                    <i class="fas fa-list text-blue-600 mr-2"></i>
                    Search Results
                </h2>
                <div id="resultsCount" class="mb-4"></div>
                <div id="resultsList" class="space-y-4"></div>
            </div>
        </div>

        <!-- Analytics Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8 mb-6 lg:mb-8">
            <!-- Price Distribution Chart -->
            <div class="bg-white rounded-xl shadow-lg p-4 lg:p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-900">
                    <i class="fas fa-chart-pie text-blue-600 mr-2"></i>
                    Price Distribution
                </h3>
                <div class="relative" style="height: 300px;">
                    <canvas id="priceChart"></canvas>
                </div>
            </div>

            <!-- Retailer Performance -->
            <div class="bg-white rounded-xl shadow-lg p-4 lg:p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-900">
                    <i class="fas fa-trophy text-yellow-600 mr-2"></i>
                    Retailer Performance
                </h3>
                <div class="space-y-3 max-h-80 overflow-y-auto">
                    @foreach($stockAnalytics['retailer_performance'] as $retailer)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 text-sm lg:text-base truncate">{{ $retailer->name }}</p>
                                <p class="text-xs lg:text-sm text-gray-500">{{ $retailer->stock_count }} total items</p>
                            </div>
                            <div class="text-right ml-3">
                                <p class="text-xs lg:text-sm font-medium text-green-600">{{ $retailer->stock_count }} in stock</p>
                                <div class="w-16 lg:w-20 bg-gray-200 rounded-full h-2 mt-1">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $retailer->availability_rate }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Quick Insights -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6 lg:mb-8">
            @if($stats['lowest_price_product'])
            <div class="bg-white rounded-xl shadow-lg p-4 lg:p-6 border-l-4 border-green-500">
                <h3 class="text-lg font-semibold mb-2 text-gray-900">
                    <i class="fas fa-tags text-green-600 mr-2"></i>
                    Best Deal
                </h3>
                <p class="text-sm lg:text-base text-gray-600 truncate">{{ $stats['lowest_price_product']->product->name }}</p>
                <p class="text-xl font-bold text-green-600">₹{{ number_format($stats['lowest_price_product']->price / 100, 2) }}</p>
                <p class="text-xs lg:text-sm text-gray-500">at {{ $stats['lowest_price_product']->retailer->name }}</p>
            </div>
            @endif

            @if($stats['highest_price_product'])
            <div class="bg-white rounded-xl shadow-lg p-4 lg:p-6 border-l-4 border-red-500">
                <h3 class="text-lg font-semibold mb-2 text-gray-900">
                    <i class="fas fa-crown text-red-600 mr-2"></i>
                    Premium Item
                </h3>
                <p class="text-sm lg:text-base text-gray-600 truncate">{{ $stats['highest_price_product']->product->name }}</p>
                <p class="text-xl font-bold text-red-600">₹{{ number_format($stats['highest_price_product']->price / 100, 2) }}</p>
                <p class="text-xs lg:text-sm text-gray-500">at {{ $stats['highest_price_product']->retailer->name }}</p>
            </div>
            @endif

            @if($stats['most_available_product'])
            <div class="bg-white rounded-xl shadow-lg p-4 lg:p-6 border-l-4 border-blue-500">
                <h3 class="text-lg font-semibold mb-2 text-gray-900">
                    <i class="fas fa-star text-blue-600 mr-2"></i>
                    Most Available
                </h3>
                <p class="text-sm lg:text-base text-gray-600 truncate">{{ $stats['most_available_product']->name }}</p>
                <p class="text-xl font-bold text-blue-600">{{ $stats['most_available_product']->stock_count }} retailers</p>
                <p class="text-xs lg:text-sm text-gray-500">Widely available</p>
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
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                font: {
                                    size: window.innerWidth < 768 ? 10 : 12
                                }
                            }
                        }
                    }
                }
            });
        }

        // Initialize with default data
        const priceData = @json($stockAnalytics['price_distribution']);
        initializePriceChart(priceData);

        // Stock filtering functionality
        function checkStock() {
            const form = document.getElementById('stockFilterForm');
            const formData = new FormData(form);
            const searchInput = document.getElementById('searchInput');
            
            // Add search term to form data
            if (searchInput.value) {
                formData.append('search', searchInput.value);
            }

            // Show loading state
            const resultsSection = document.getElementById('resultsSection');
            const resultsList = document.getElementById('resultsList');
            resultsList.innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-2xl text-blue-500"></i><p class="mt-2 text-gray-500">Loading results...</p></div>';
            resultsSection.classList.remove('hidden');

            fetch('/stock-status/check', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(Object.fromEntries(formData))
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayResults(data);
                    updateAnalytics(data);
                } else {
                    resultsList.innerHTML = '<div class="text-center py-8 text-red-500">Error loading results</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                resultsList.innerHTML = '<div class="text-center py-8 text-red-500">Error loading results</div>';
            });
        }

        function resetFilters() {
            document.getElementById('stockFilterForm').reset();
            document.getElementById('searchInput').value = '';
            document.getElementById('resultsSection').classList.add('hidden');
            
            // Reset chart to original data
            initializePriceChart(priceData);
        }

        function displayResults(data) {
            const resultsSection = document.getElementById('resultsSection');
            const resultsCount = document.getElementById('resultsCount');
            const resultsList = document.getElementById('resultsList');

            resultsCount.innerHTML = `<p class="text-lg font-semibold text-gray-700">Found ${data.count} stock entries</p>`;
            
            if (data.data.length === 0) {
                resultsList.innerHTML = '<div class="text-center py-8"><i class="fas fa-search text-gray-400 text-4xl mb-4"></i><p class="text-gray-500">No stock entries found matching your criteria.</p></div>';
                return;
            }

            resultsList.innerHTML = data.data.map(stock => `
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="flex-1 min-w-0">
                        <h4 class="font-medium text-gray-900 text-sm lg:text-base truncate">${stock.product.name}</h4>
                        <p class="text-xs lg:text-sm text-gray-500">${stock.retailer.name}</p>
                        <p class="text-xs lg:text-sm text-gray-500">₹${(stock.price / 100).toFixed(2)}</p>
                    </div>
                    <div class="text-right ml-3">
                        <a href="${stock.url}" target="_blank" class="text-blue-500 hover:text-blue-600 text-xs lg:text-sm block mb-1">
                            <i class="fas fa-external-link-alt mr-1"></i>View
                        </a>
                        <span class="px-2 py-1 rounded text-xs lg:text-sm font-medium ${stock.in_stock ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-red-100 text-red-700 border border-red-300'}">
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
            // This would update the retailer performance section
            // For now, we'll just log the data
            console.log('Updated retailer performance:', stockData);
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
            const insightsSection = document.querySelector('.grid.grid-cols-1.md\\:grid-cols-3.gap-6.mb-6.lg\\:mb-8');
            if (!insightsSection) return;

            insightsSection.innerHTML = `
                ${lowestPrice ? `
                <div class="bg-white rounded-xl shadow-lg p-4 lg:p-6 border-l-4 border-green-500">
                    <h3 class="text-lg font-semibold mb-2 text-gray-900">
                        <i class="fas fa-tags text-green-600 mr-2"></i>
                        Best Deal
                    </h3>
                    <p class="text-sm lg:text-base text-gray-600 truncate">${lowestPrice.product.name}</p>
                    <p class="text-xl font-bold text-green-600">₹${(lowestPrice.price / 100).toFixed(2)}</p>
                    <p class="text-xs lg:text-sm text-gray-500">at ${lowestPrice.retailer.name}</p>
                </div>
                ` : ''}
                
                ${highestPrice ? `
                <div class="bg-white rounded-xl shadow-lg p-4 lg:p-6 border-l-4 border-red-500">
                    <h3 class="text-lg font-semibold mb-2 text-gray-900">
                        <i class="fas fa-crown text-red-600 mr-2"></i>
                        Premium Item
                    </h3>
                    <p class="text-sm lg:text-base text-gray-600 truncate">${highestPrice.product.name}</p>
                    <p class="text-xl font-bold text-red-600">₹${(highestPrice.price / 100).toFixed(2)}</p>
                    <p class="text-xs lg:text-sm text-gray-500">at ${highestPrice.retailer.name}</p>
                </div>
                ` : ''}
                
                ${mostAvailable ? `
                <div class="bg-white rounded-xl shadow-lg p-4 lg:p-6 border-l-4 border-blue-500">
                    <h3 class="text-lg font-semibold mb-2 text-gray-900">
                        <i class="fas fa-star text-blue-600 mr-2"></i>
                        Most Available
                    </h3>
                    <p class="text-sm lg:text-base text-gray-600 truncate">${mostAvailable[0]}</p>
                    <p class="text-xl font-bold text-blue-600">${mostAvailable[1]} retailers</p>
                    <p class="text-xs lg:text-sm text-gray-500">Widely available</p>
                </div>
                ` : ''}
            `;
        }

        // Handle window resize for chart responsiveness
        window.addEventListener('resize', function() {
            if (priceChart) {
                priceChart.resize();
            }
        });
    </script>
</body>
</html> 