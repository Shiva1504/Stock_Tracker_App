<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retailers - Stock Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Retailers</h1>
            <a href="/" class="text-blue-500 hover:text-blue-600">← Back to Products</a>
        </div>
        
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Add Retailer</h2>
            <form action="/retailers" method="POST">
                @csrf
                <div class="flex gap-4">
                    <input 
                        type="text" 
                        name="name" 
                        placeholder="Retailer name" 
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                    <button 
                        type="submit" 
                        class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        Add Retailer
                    </button>
                </div>
                @error('name')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </form>
        </div>

        <!-- Search and Filter Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex flex-col lg:flex-row gap-4 items-center">
                <!-- Search Input -->
                <div class="flex-1 w-full lg:w-auto">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input 
                            type="text" 
                            id="searchInput"
                            placeholder="Search retailers or products..." 
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="{{ request('search') }}"
                        >
                    </div>
                </div>
                
                <!-- Stock Status Filter -->
                <div class="flex gap-2">
                    <select id="stockStatusFilter" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Stock Status</option>
                        <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>In Stock</option>
                        <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                    
                    <!-- Sort Options -->
                    <select id="sortBy" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>Sort by Name</option>
                        <option value="stock_count" {{ request('sort_by') === 'stock_count' ? 'selected' : '' }}>Sort by Stock Count</option>
                    </select>
                    
                    <button id="sortOrder" class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-sort-{{ request('sort_order') === 'desc' ? 'down' : 'up' }}"></i>
                    </button>
                </div>
                
                <!-- Clear Filters -->
                <button id="clearFilters" class="px-4 py-2 text-gray-600 hover:text-gray-800 focus:outline-none">
                    <i class="fas fa-times mr-1"></i>Clear
                </button>
            </div>
            
            <!-- Search Results Info -->
            <div id="searchInfo" class="mt-3 text-sm text-gray-600 hidden">
                <span id="resultCount"></span> retailers found
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Retailers & Stock</h2>
            <div id="retailersContainer">
                @if($retailers->count() > 0)
                    <div class="space-y-6">
                        @foreach($retailers as $retailer)
                            <div class="retailer-item border border-gray-200 rounded-lg p-6" 
                                 data-name="{{ strtolower($retailer->name) }}"
                                 data-stock-status="{{ $retailer->stock->where('in_stock', true)->count() > 0 ? 'in_stock' : 'out_of_stock' }}">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $retailer->name }}</h3>
                                    <div class="flex gap-2">
                                        <a href="/retailers/{{ $retailer->id }}/edit" 
                                           class="px-3 py-1 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 text-sm">
                                            Edit
                                        </a>
                                        <form action="/retailers/{{ $retailer->id }}" method="POST" class="inline" 
                                              onsubmit="return confirm('Are you sure you want to delete this retailer? This will also delete all associated stock.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="px-3 py-1 bg-red-500 text-white rounded-md hover:bg-red-600 text-sm">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                                <!-- Add Stock Form -->
                                <form action="/retailers/{{ $retailer->id }}/stock" method="POST" class="mb-4 p-4 bg-gray-50 rounded-lg">
                                    @csrf
                                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                        <select name="product_id" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                            <option value="">Select Product</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                        <input 
                                            type="number" 
                                            name="price" 
                                            placeholder="Price (₹)" 
                                            step="0.01"
                                            class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            required
                                        >
                                        <input 
                                            type="url" 
                                            name="url" 
                                            placeholder="Product URL (optional for physical products)" 
                                            class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        >
                                        <input 
                                            type="text" 
                                            name="sku" 
                                            placeholder="SKU (optional)" 
                                            class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        >
                                        <div class="flex items-center">
                                            <label class="flex items-center">
                                                <input 
                                                    type="checkbox" 
                                                    name="in_stock" 
                                                    value="1" 
                                                    class="mr-2"
                                                    checked
                                                >
                                                In Stock
                                            </label>
                                        </div>
                                    </div>
                                    <button 
                                        type="submit" 
                                        class="mt-3 px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500"
                                    >
                                        Add Stock
                                    </button>
                                </form>

                                <!-- Stock List -->
                                @if($retailer->stock->count() > 0)
                                    <div class="space-y-2">
                                        <h4 class="font-medium text-gray-700">Current Stock:</h4>
                                        @foreach($retailer->stock as $stock)
                                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                                <div class="flex-1">
                                                    <p class="font-medium">{{ $stock->product->name }}</p>
                                                    <p class="text-sm text-gray-500">
                                                        ₹{{ number_format($stock->price / 100, 2) }}
                                                    </p>
                                                    @if($stock->sku)
                                                        <p class="text-xs text-gray-400">SKU: {{ $stock->sku }}</p>
                                                    @endif
                                                </div>
                                                <div class="flex items-center gap-3">
                                                    @if($stock->hasUrl())
                                                        <a href="{{ $stock->url }}" target="_blank" class="text-blue-500 hover:text-blue-600 text-sm">View</a>
                                                    @else
                                                        <span class="text-gray-400 text-sm">No URL</span>
                                                    @endif
                                                    <span class="text-sm px-2 py-1 rounded {{ $stock->in_stock ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-red-100 text-red-700 border border-red-300' }}">
                                                        {{ $stock->in_stock ? 'In Stock' : 'Out of Stock' }}
                                                    </span>
                                                    <a href="/stock-history/{{ $stock->id }}" class="text-orange-500 hover:text-orange-600 text-sm">History</a>
                                                    <a href="/stock/{{ $stock->id }}/edit" 
                                                       class="px-2 py-1 bg-yellow-500 text-white rounded text-xs hover:bg-yellow-600">
                                                        Edit
                                                    </a>
                                                    <form action="/stock/{{ $stock->id }}" method="POST" class="inline" 
                                                          onsubmit="return confirm('Are you sure you want to delete this stock entry?')">
                                                          @csrf
                                                          @method('DELETE')
                                                          <button type="submit" 
                                                                  class="px-2 py-1 bg-red-500 text-white rounded text-xs hover:bg-red-600">
                                                              Delete
                                                          </button>
                                                      </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-500 text-sm">No stock added yet.</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">No retailers found.</p>
                @endif
            </div>
        </div>
    </div>

    <script>
        let currentSortOrder = '{{ request("sort_order", "asc") }}';
        let searchTimeout;

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                updateFilters();
            }, 300);
        });

        // Filter functionality
        document.getElementById('stockStatusFilter').addEventListener('change', updateFilters);
        document.getElementById('sortBy').addEventListener('change', updateFilters);
        document.getElementById('sortOrder').addEventListener('click', function() {
            currentSortOrder = currentSortOrder === 'asc' ? 'desc' : 'asc';
            updateFilters();
        });

        // Clear filters
        document.getElementById('clearFilters').addEventListener('click', function() {
            document.getElementById('searchInput').value = '';
            document.getElementById('stockStatusFilter').value = '';
            document.getElementById('sortBy').value = 'name';
            currentSortOrder = 'asc';
            updateFilters();
        });

        function updateFilters() {
            const search = document.getElementById('searchInput').value;
            const stockStatus = document.getElementById('stockStatusFilter').value;
            const sortBy = document.getElementById('sortBy').value;
            
            // Update sort order button
            const sortOrderBtn = document.getElementById('sortOrder');
            sortOrderBtn.innerHTML = `<i class="fas fa-sort-${currentSortOrder === 'desc' ? 'down' : 'up'}"></i>`;
            
            // Build URL with parameters
            const params = new URLSearchParams();
            if (search) params.append('search', search);
            if (stockStatus) params.append('stock_status', stockStatus);
            if (sortBy !== 'name') params.append('sort_by', sortBy);
            if (currentSortOrder !== 'asc') params.append('sort_order', currentSortOrder);
            
            // Navigate to filtered results
            const url = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
            window.location.href = url;
        }

        // Update search info on page load
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const stockStatusFilter = document.getElementById('stockStatusFilter');
            
            if (searchInput.value || stockStatusFilter.value) {
                const resultCount = document.querySelectorAll('.retailer-item').length;
                document.getElementById('resultCount').textContent = resultCount;
                document.getElementById('searchInfo').classList.remove('hidden');
            }
        });
    </script>
</body>
</html> 