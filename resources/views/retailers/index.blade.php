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
    <div class="container mx-auto px-4 py-4 lg:py-8">
        <!-- Mobile Header -->
        <div class="flex items-center justify-between mb-6 lg:hidden">
            <a href="/" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-xl font-bold text-gray-900">Retailers</h1>
            <div class="w-8"></div> <!-- Spacer for centering -->
        </div>

        <!-- Desktop Header -->
        <div class="hidden lg:flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Retailers</h1>
            <a href="/" class="text-blue-500 hover:text-blue-600">← Back to Dashboard</a>
        </div>
        
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif
        
        <!-- Add Retailer Form -->
        <div class="bg-white rounded-lg shadow-md p-4 lg:p-6 mb-6 lg:mb-8">
            <h2 class="text-lg lg:text-xl font-semibold mb-4">Add Retailer</h2>
            <form action="/retailers" method="POST">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <input 
                        type="text" 
                        name="name" 
                        placeholder="Retailer name" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                    <div class="lg:col-span-2">
                        <button 
                            type="submit" 
                            class="w-full lg:w-auto px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            Add Retailer
                        </button>
                    </div>
                </div>
                @error('name')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </form>
        </div>

        <!-- Search and Filter Section -->
        <div class="bg-white rounded-lg shadow-md p-4 lg:p-6 mb-6">
            <div class="flex flex-col gap-4">
                <!-- Search Input -->
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
                
                <!-- Filters Row -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <!-- Stock Status Filter -->
                    <select id="stockStatusFilter" class="w-full sm:w-auto px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Stock Status</option>
                        <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>In Stock</option>
                        <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                    
                    <!-- Sort Options -->
                    <select id="sortBy" class="w-full sm:w-auto px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>Sort by Name</option>
                        <option value="stock_count" {{ request('sort_by') === 'stock_count' ? 'selected' : '' }}>Sort by Stock Count</option>
                    </select>
                    
                    <button id="sortOrder" class="w-full sm:w-auto px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-sort-{{ request('sort_order') === 'desc' ? 'down' : 'up' }}"></i>
                    </button>
                    
                    <!-- Clear Filters -->
                    <button id="clearFilters" class="w-full sm:w-auto px-4 py-2 text-gray-600 hover:text-gray-800 focus:outline-none border border-gray-300 rounded-md">
                        <i class="fas fa-times mr-1"></i>Clear
                    </button>
                </div>
            </div>
            
            <!-- Search Results Info -->
            <div id="searchInfo" class="mt-3 text-sm text-gray-600 hidden">
                <span id="resultCount"></span> retailers found
            </div>
        </div>

        <!-- Retailers List -->
        <div class="bg-white rounded-lg shadow-md p-4 lg:p-6">
            <h2 class="text-lg lg:text-xl font-semibold mb-4">Retailers & Stock</h2>
            <div id="retailersContainer">
                @if($retailers->count() > 0)
                    <div class="space-y-6">
                        @foreach($retailers as $retailer)
                            <div class="retailer-item border border-gray-200 rounded-lg p-4 lg:p-6" 
                                 data-name="{{ strtolower($retailer->name) }}"
                                 data-stock-status="{{ $retailer->stock->where('in_stock', true)->count() > 0 ? 'in_stock' : 'out_of_stock' }}">
                                
                                <!-- Retailer Header -->
                                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-4 gap-3">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $retailer->name }}</h3>
                                    <div class="flex gap-2">
                                        <a href="/retailers/{{ $retailer->id }}/edit" 
                                           class="px-3 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 text-sm">
                                            <i class="fas fa-edit mr-1"></i>Edit
                                        </a>
                                        <form action="/retailers/{{ $retailer->id }}" method="POST" class="inline" 
                                              onsubmit="return confirm('Are you sure you want to delete this retailer? This will also delete all associated stock.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 text-sm">
                                                <i class="fas fa-trash mr-1"></i>Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                                <!-- Add Stock Form -->
                                <form action="/retailers/{{ $retailer->id }}/stock" method="POST" class="mb-4 p-4 bg-gray-50 rounded-lg">
                                    @csrf
                                    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
                                        <select name="product_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
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
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            required
                                        >
                                        <input 
                                            type="url" 
                                            name="url" 
                                            placeholder="Product URL (optional)" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        >
                                        <input 
                                            type="text" 
                                            name="sku" 
                                            placeholder="SKU (optional)" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
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
                                                <span class="text-sm">In Stock</span>
                                            </label>
                                        </div>
                                    </div>
                                    <button 
                                        type="submit" 
                                        class="mt-3 w-full lg:w-auto px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500"
                                    >
                                        <i class="fas fa-plus mr-1"></i>Add Stock
                                    </button>
                                </form>

                                <!-- Stock List -->
                                @if($retailer->stock->count() > 0)
                                    <div class="space-y-3">
                                        <h4 class="font-medium text-gray-700 text-sm lg:text-base">Current Stock ({{ $retailer->stock->count() }} items):</h4>
                                        @foreach($retailer->stock as $stock)
                                            <!-- Mobile Stock Item -->
                                            <div class="lg:hidden border border-gray-200 rounded-lg p-3">
                                                <div class="flex items-start justify-between mb-2">
                                                    <div class="flex-1 min-w-0">
                                                        <p class="font-medium text-gray-900 text-sm truncate">{{ $stock->product->name }}</p>
                                                        <p class="text-sm text-gray-500">₹{{ number_format($stock->price / 100, 2) }}</p>
                                                        @if($stock->sku)
                                                            <p class="text-xs text-gray-400">SKU: {{ $stock->sku }}</p>
                                                        @endif
                                                    </div>
                                                    <span class="text-xs px-2 py-1 rounded ml-2 {{ $stock->in_stock ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-red-100 text-red-700 border border-red-300' }}">
                                                        {{ $stock->in_stock ? 'In Stock' : 'Out of Stock' }}
                                                    </span>
                                                </div>
                                                <div class="flex gap-2">
                                                    @if($stock->hasUrl())
                                                        <a href="{{ $stock->url }}" target="_blank" class="text-blue-500 hover:text-blue-600 text-xs">
                                                            <i class="fas fa-external-link-alt mr-1"></i>View
                                                        </a>
                                                    @endif
                                                    <a href="/stock-history/{{ $stock->id }}" class="text-orange-500 hover:text-orange-600 text-xs">
                                                        <i class="fas fa-history mr-1"></i>History
                                                    </a>
                                                    <a href="/stock/{{ $stock->id }}/edit" class="text-yellow-500 hover:text-yellow-600 text-xs">
                                                        <i class="fas fa-edit mr-1"></i>Edit
                                                    </a>
                                                </div>
                                            </div>

                                            <!-- Desktop Stock Item -->
                                            <div class="hidden lg:flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                                <div class="flex-1">
                                                    <p class="font-medium text-gray-900">{{ $stock->product->name }}</p>
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
                                                       class="text-yellow-500 hover:text-yellow-600 text-sm">Edit</a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-500 text-sm lg:text-base">No stock items for this retailer yet.</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-store text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500">No retailers found.</p>
                        @if(request('search') || request('stock_status'))
                            <p class="text-sm text-gray-400 mt-2">Try adjusting your search criteria.</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Search and filter functionality
        const searchInput = document.getElementById('searchInput');
        const stockStatusFilter = document.getElementById('stockStatusFilter');
        const sortBy = document.getElementById('sortBy');
        const sortOrder = document.getElementById('sortOrder');
        const clearFilters = document.getElementById('clearFilters');
        const searchInfo = document.getElementById('searchInfo');
        const resultCount = document.getElementById('resultCount');
        const retailerItems = document.querySelectorAll('.retailer-item');

        let currentSortOrder = '{{ request('sort_order') === 'desc' ? 'desc' : 'asc' }}';

        function filterRetailers() {
            const searchTerm = searchInput.value.toLowerCase();
            const stockStatus = stockStatusFilter.value;
            let visibleCount = 0;

            retailerItems.forEach(item => {
                const retailerName = item.dataset.name;
                const stockStatusData = item.dataset.stockStatus;
                
                let shouldShow = true;

                // Search filter
                if (searchTerm && !retailerName.includes(searchTerm)) {
                    shouldShow = false;
                }

                // Stock status filter
                if (stockStatus && stockStatusData !== stockStatus) {
                    shouldShow = false;
                }

                // Show/hide item
                if (shouldShow) {
                    item.style.display = 'block';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            // Update search info
            if (searchTerm || stockStatus) {
                resultCount.textContent = visibleCount;
                searchInfo.classList.remove('hidden');
            } else {
                searchInfo.classList.add('hidden');
            }
        }

        function sortRetailers() {
            const sortCriteria = sortBy.value;
            const retailersContainer = document.getElementById('retailersContainer');
            const items = Array.from(retailerItems);

            items.sort((a, b) => {
                let comparison = 0;
                
                if (sortCriteria === 'name') {
                    comparison = a.dataset.name.localeCompare(b.dataset.name);
                } else if (sortCriteria === 'stock_count') {
                    // This would need to be implemented with actual stock count data
                    comparison = 0;
                }

                return currentSortOrder === 'desc' ? -comparison : comparison;
            });

            // Re-append sorted items
            items.forEach(item => retailersContainer.appendChild(item));
        }

        function clearAllFilters() {
            searchInput.value = '';
            stockStatusFilter.value = '';
            sortBy.value = 'name';
            currentSortOrder = 'asc';
            sortOrder.innerHTML = '<i class="fas fa-sort-up"></i>';
            filterRetailers();
        }

        function toggleSortOrder() {
            currentSortOrder = currentSortOrder === 'asc' ? 'desc' : 'asc';
            sortOrder.innerHTML = `<i class="fas fa-sort-${currentSortOrder === 'desc' ? 'down' : 'up'}"></i>`;
            sortRetailers();
        }

        // Event listeners
        searchInput.addEventListener('input', filterRetailers);
        stockStatusFilter.addEventListener('change', filterRetailers);
        sortBy.addEventListener('change', sortRetailers);
        sortOrder.addEventListener('click', toggleSortOrder);
        clearFilters.addEventListener('click', clearAllFilters);

        // Initialize
        filterRetailers();
    </script>
</body>
</html> 