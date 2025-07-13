<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Products - Stock Tracker</title>
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
            <h1 class="text-xl font-bold text-gray-900">Products</h1>
            <div class="w-8"></div> <!-- Spacer for centering -->
        </div>

        <!-- Desktop Header -->
        <div class="hidden lg:flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Products</h1>
            <div class="flex gap-4">
                <a href="/" class="text-blue-500 hover:text-blue-600">← Dashboard</a>
                <a href="/retailers" class="text-blue-500 hover:text-blue-600">Manage Retailers →</a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif
        
        <!-- Add Product Form -->
        <div class="bg-white rounded-lg shadow-md p-4 lg:p-6 mb-6 lg:mb-8">
            <h2 class="text-lg lg:text-xl font-semibold mb-4">Add Product</h2>
            <form action="/products" method="POST">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <input 
                        type="text" 
                        name="name" 
                        placeholder="Product name" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                    <input
                        type="number"
                        name="low_stock_threshold"
                        placeholder="Low Stock Threshold"
                        min="1"
                        value="1"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        required
                    >
                    <button 
                        type="submit" 
                        class="w-full lg:w-auto px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        Add Product
                    </button>
                </div>
                @error('name')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
                @error('low_stock_threshold')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </form>
        </div>

        <!-- Search Section -->
        <div class="bg-white rounded-lg shadow-md p-4 lg:p-6 mb-6 lg:mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg lg:text-xl font-semibold text-gray-900">
                    <i class="fas fa-search text-blue-600 mr-2"></i>
                    Search Products
                </h2>
                <div class="text-sm text-gray-500">
                    <span id="productCount">{{ $products->count() }}</span> products found
                </div>
            </div>
            
            <div class="relative mb-4">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input 
                    type="text" 
                    id="searchInput"
                    placeholder="Search products by name..." 
                    value="{{ $search ?? '' }}"
                    class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <button 
                        onclick="clearSearch()" 
                        id="clearSearchBtn"
                        class="text-gray-400 hover:text-gray-600 {{ $search ? '' : 'hidden' }}"
                    >
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <!-- Search Filters -->
            <div class="flex flex-wrap gap-4">
                <div class="flex items-center">
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            id="inStockFilter" 
                            class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        >
                        <span class="text-sm text-gray-700">In Stock Only</span>
                    </label>
                </div>
                <div class="flex items-center">
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            id="outOfStockFilter" 
                            class="mr-2 rounded border-gray-300 text-red-600 focus:ring-red-500"
                        >
                        <span class="text-sm text-gray-700">Out of Stock Only</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Products List -->
        <div class="bg-white rounded-lg shadow-md p-4 lg:p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-4 gap-4">
                <h2 class="text-lg lg:text-xl font-semibold text-gray-900">
                    <i class="fas fa-boxes text-blue-600 mr-2"></i>
                    Products
                </h2>
                <div class="flex gap-2">
                    <button 
                        onclick="sortProducts('name')" 
                        class="px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200"
                    >
                        <i class="fas fa-sort-alpha-down mr-1"></i>Sort A-Z
                    </button>
                    <button 
                        onclick="sortProducts('stock')" 
                        class="px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200"
                    >
                        <i class="fas fa-sort-amount-down mr-1"></i>Sort by Stock
                    </button>
                </div>
            </div>
            
            <div id="productsList" class="space-y-4">
                @if($products->count() > 0)
                    @foreach($products as $product)
                        <div class="product-item border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors" 
                             data-name="{{ strtolower($product->name) }}" 
                             data-stock="{{ $product->inStock() ? 'in' : 'out' }}">
                            <!-- Mobile Layout -->
                            <div class="lg:hidden p-4">
                                <div class="flex items-start justify-between mb-3">
                                    <h3 class="font-medium text-gray-900 text-base truncate flex-1 mr-3">{{ $product->name }}</h3>
                                    <span class="text-sm px-2 py-1 rounded font-medium {{ $product->inStock() ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-red-100 text-red-700 border border-red-300' }}">
                                        @if($product->inStock())
                                            <i class="fas fa-check-circle mr-1"></i>In Stock
                                            @if($product->isLowStock())
                                                <span class="ml-1 px-1 py-0.5 bg-yellow-100 text-yellow-800 border border-yellow-300 rounded text-xs">Low</span>
                                            @endif
                                        @else
                                            <i class="fas fa-times-circle mr-1"></i>Out of Stock
                                        @endif
                                    </span>
                                </div>
                                <div class="flex items-center justify-between mb-3">
                                    <p class="text-sm text-gray-500">
                                        <i class="fas fa-store mr-1"></i>{{ $product->stock->count() }} retailer(s)
                                    </p>
                                </div>
                                <div class="flex gap-2">
                                    <a href="/products/{{ $product->id }}/edit" 
                                       class="flex-1 px-3 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 text-sm text-center">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </a>
                                    <form action="/products/{{ $product->id }}" method="POST" class="flex-1" 
                                          onsubmit="return confirm('Are you sure you want to delete this product?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="w-full px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 text-sm">
                                            <i class="fas fa-trash mr-1"></i>Delete
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Desktop Layout -->
                            <div class="hidden lg:flex items-center justify-between p-4">
                                <div class="flex-1">
                                    <h3 class="font-medium text-gray-900">{{ $product->name }}</h3>
                                    <div class="flex items-center gap-4 mt-1">
                                        <span class="text-sm px-2 py-1 rounded font-medium {{ $product->inStock() ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-red-100 text-red-700 border border-red-300' }}">
                                            @if($product->inStock())
                                                <i class="fas fa-check-circle mr-1"></i>In Stock
                                                @if($product->isLowStock())
                                                    <span class="ml-2 px-2 py-1 bg-yellow-100 text-yellow-800 border border-yellow-300 rounded font-semibold">Low Stock</span>
                                                @endif
                                            @else
                                                <i class="fas fa-times-circle mr-1"></i>Out of Stock
                                            @endif
                                        </span>
                                        <p class="text-sm text-gray-500">
                                            <i class="fas fa-store mr-1"></i>{{ $product->stock->count() }} retailer(s)
                                        </p>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <a href="/products/{{ $product->id }}/edit" 
                                       class="px-3 py-1 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 text-sm">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </a>
                                    <form action="/products/{{ $product->id }}" method="POST" class="inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this product?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="px-3 py-1 bg-red-500 text-white rounded-md hover:bg-red-600 text-sm">
                                            <i class="fas fa-trash mr-1"></i>Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-box-open text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500">No products found.</p>
                        @if($search)
                            <p class="text-sm text-gray-400 mt-2">Try adjusting your search criteria.</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const clearSearchBtn = document.getElementById('clearSearchBtn');
        const productCount = document.getElementById('productCount');
        const productItems = document.querySelectorAll('.product-item');
        const inStockFilter = document.getElementById('inStockFilter');
        const outOfStockFilter = document.getElementById('outOfStockFilter');

        function filterProducts() {
            const searchTerm = searchInput.value.toLowerCase();
            const showInStock = inStockFilter.checked;
            const showOutOfStock = outOfStockFilter.checked;
            let visibleCount = 0;

            productItems.forEach(item => {
                const productName = item.dataset.name;
                const stockStatus = item.dataset.stock;
                
                let shouldShow = true;

                // Search filter
                if (searchTerm && !productName.includes(searchTerm)) {
                    shouldShow = false;
                }

                // Stock status filter
                if (showInStock && stockStatus !== 'in') {
                    shouldShow = false;
                }
                if (showOutOfStock && stockStatus !== 'out') {
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

            // Update count
            productCount.textContent = visibleCount;

            // Show/hide clear button
            if (searchTerm) {
                clearSearchBtn.classList.remove('hidden');
            } else {
                clearSearchBtn.classList.add('hidden');
            }
        }

        function clearSearch() {
            searchInput.value = '';
            inStockFilter.checked = false;
            outOfStockFilter.checked = false;
            filterProducts();
        }

        function sortProducts(criteria) {
            const productsList = document.getElementById('productsList');
            const items = Array.from(productItems);

            items.sort((a, b) => {
                if (criteria === 'name') {
                    return a.dataset.name.localeCompare(b.dataset.name);
                } else if (criteria === 'stock') {
                    // Sort by stock status (in stock first)
                    if (a.dataset.stock === 'in' && b.dataset.stock === 'out') return -1;
                    if (a.dataset.stock === 'out' && b.dataset.stock === 'in') return 1;
                    return 0;
                }
                return 0;
            });

            // Re-append sorted items
            items.forEach(item => productsList.appendChild(item));
        }

        // Event listeners
        searchInput.addEventListener('input', filterProducts);
        inStockFilter.addEventListener('change', filterProducts);
        outOfStockFilter.addEventListener('change', filterProducts);

        // Initialize
        filterProducts();
    </script>
</body>
</html> 