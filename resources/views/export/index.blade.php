<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Data - Stock Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Export Data</h1>
            <a href="/" class="text-blue-500 hover:text-blue-600">← Back to Dashboard</a>
        </div>

        <!-- Info Note -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Data Export Options</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Export your stock data, history, and activity logs in CSV or Excel format. Use filters to export specific data sets.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Data Export -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Export Stock Data</h2>
            <form action="/export/csv" method="POST" class="space-y-6">
                @csrf
                
                <!-- Filters -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Product</label>
                        <select name="product_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Products</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Retailer</label>
                        <select name="retailer_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Retailers</option>
                            @foreach($retailers as $retailer)
                                <option value="{{ $retailer->id }}">{{ $retailer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stock Status</label>
                        <select name="stock_status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Status</option>
                            <option value="in_stock">In Stock</option>
                            <option value="out_of_stock">Out of Stock</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Min Price (₹)</label>
                        <input type="number" name="price_min" step="0.01" min="0" 
                               placeholder="Minimum price"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Max Price (₹)</label>
                        <input type="number" name="price_max" step="0.01" min="0" 
                               placeholder="Maximum price"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" name="search" 
                               placeholder="Product, retailer, or SKU"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Export Buttons -->
                <div class="flex gap-4">
                    <button type="submit" class="px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export as CSV
                    </button>
                    <button type="submit" formaction="/export/excel" class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export as Excel
                    </button>
                </div>
            </form>
        </div>

        <!-- Stock History Export -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Export Stock History</h2>
            <form action="/export/stock-history" method="POST" class="space-y-6">
                @csrf
                
                <!-- Filters -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Product</label>
                        <select name="product_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Products</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Retailer</label>
                        <select name="retailer_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Retailers</option>
                            @foreach($retailers as $retailer)
                                <option value="{{ $retailer->id }}">{{ $retailer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                        <input type="date" name="date_from" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                        <input type="date" name="date_to" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Export Button -->
                <div>
                    <button type="submit" class="px-6 py-3 bg-purple-500 text-white rounded-lg hover:bg-purple-600">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Export Stock History
                    </button>
                </div>
            </form>
        </div>

        <!-- Activity Log Export -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Export Activity Log</h2>
            <form action="/export/activity-log" method="POST" class="space-y-6">
                @csrf
                
                <!-- Filters -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Action</label>
                        <select name="action" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Actions</option>
                            <option value="created">Created</option>
                            <option value="updated">Updated</option>
                            <option value="deleted">Deleted</option>
                            <option value="bulk_updated">Bulk Updated</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Subject Type</label>
                        <select name="subject_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Types</option>
                            <option value="Product">Product</option>
                            <option value="Retailer">Retailer</option>
                            <option value="Stock">Stock</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                        <input type="date" name="date_from" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                        <input type="date" name="date_to" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Export Button -->
                <div>
                    <button type="submit" class="px-6 py-3 bg-orange-500 text-white rounded-lg hover:bg-orange-600">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Export Activity Log
                    </button>
                </div>
            </form>
        </div>

        <!-- Export Information -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4">Export Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">Stock Data Export</h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Product name, retailer, price, stock status</li>
                        <li>• SKU, product URL, last updated timestamp</li>
                        <li>• Filter by product, retailer, status, price range</li>
                        <li>• Available in CSV and Excel formats</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">History & Activity Export</h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Stock history with price and status changes</li>
                        <li>• Activity log with user actions and timestamps</li>
                        <li>• Filter by date range, action type, subject type</li>
                        <li>• CSV format for easy analysis</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 