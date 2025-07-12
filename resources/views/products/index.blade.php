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
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-8">
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
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Add Product</h2>
            <form action="/products" method="POST">
                @csrf
                <div class="flex gap-4">
                    <input 
                        type="text" 
                        name="name" 
                        placeholder="Product name" 
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                    <button 
                        type="submit" 
                        class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        Add Product
                    </button>
                </div>
                @error('name')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </form>
        </div>

        <!-- Search Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900">
                    <i class="fas fa-search text-blue-600 mr-2"></i>
                    Search Products
                </h2>
                <div class="text-sm text-gray-500">
                    <span id="productCount">{{ $products->count() }}</span> products found
                </div>
            </div>
            
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input 
                    type="text" 
                    id="searchInput"
                    placeholder="Search products by name..." 
                    value="{{ $search ?? '' }}"
                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
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
            <div class="mt-4 flex flex-wrap gap-4">
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
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900">
                    <i class="fas fa-boxes text-blue-600 mr-2"></i>
                    Products
                </h2>
                <div class="flex gap-2">
                    <button 
                        onclick="sortProducts('name')" 
                        class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200"
                    >
                        <i class="fas fa-sort-alpha-down mr-1"></i>Sort A-Z
                    </button>
                    <button 
                        onclick="sortProducts('stock')" 
                        class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200"
                    >
                        <i class="fas fa-sort-amount-down mr-1"></i>Sort by Stock
                    </button>
                </div>
            </div>
            
            <div id="productsList" class="space-y-4">
                @if($products->count() > 0)
                    @foreach($products as $product)
                        <div class="product-item flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors" 
                             data-name="{{ strtolower($product->name) }}" 
                             data-stock="{{ $product->inStock() ? 'in' : 'out' }}">
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-900">{{ $product->name }}</h3>
                                <div class="flex items-center gap-4 mt-1">
                                    <p class="text-sm text-gray-500">
                                        @if($product->inStock())
                                            <span class="text-green-600">
                                                <i class="fas fa-check-circle mr-1"></i>In Stock
                                            </span>
                                        @else
                                            <span class="text-red-600">
                                                <i class="fas fa-times-circle mr-1"></i>Out of Stock
                                            </span>
                                        @endif
                                    </p>
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
        let currentSort = 'name';
        let currentProducts = [];

        // Initialize products data
        document.addEventListener('DOMContentLoaded', function() {
            initializeProducts();
            setupEventListeners();
        });

        function initializeProducts() {
            const productItems = document.querySelectorAll('.product-item');
            currentProducts = Array.from(productItems).map(item => ({
                element: item,
                name: item.dataset.name,
                stock: item.dataset.stock
            }));
        }

        function setupEventListeners() {
            const searchInput = document.getElementById('searchInput');
            const inStockFilter = document.getElementById('inStockFilter');
            const outOfStockFilter = document.getElementById('outOfStockFilter');

            // Search functionality
            searchInput.addEventListener('input', debounce(function() {
                performSearch();
            }, 300));

            // Filter functionality
            inStockFilter.addEventListener('change', performSearch);
            outOfStockFilter.addEventListener('change', performSearch);
        }

        function performSearch() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const inStockOnly = document.getElementById('inStockFilter').checked;
            const outOfStockOnly = document.getElementById('outOfStockFilter').checked;
            const clearBtn = document.getElementById('clearSearchBtn');

            // Show/hide clear button
            clearBtn.classList.toggle('hidden', !searchTerm);

            let visibleCount = 0;

            currentProducts.forEach(product => {
                const matchesSearch = !searchTerm || product.name.includes(searchTerm);
                const matchesStockFilter = 
                    (!inStockOnly && !outOfStockOnly) ||
                    (inStockOnly && product.stock === 'in') ||
                    (outOfStockOnly && product.stock === 'out');

                const shouldShow = matchesSearch && matchesStockFilter;
                product.element.style.display = shouldShow ? 'flex' : 'none';
                
                if (shouldShow) visibleCount++;
            });

            // Update count
            document.getElementById('productCount').textContent = visibleCount;

            // Show/hide no results message
            const noResultsMsg = document.querySelector('.text-center.py-8');
            if (visibleCount === 0) {
                if (!noResultsMsg) {
                    const productsList = document.getElementById('productsList');
                    productsList.innerHTML = `
                        <div class="text-center py-8">
                            <i class="fas fa-search text-gray-400 text-4xl mb-4"></i>
                            <p class="text-gray-500">No products found matching your criteria.</p>
                            <p class="text-sm text-gray-400 mt-2">Try adjusting your search or filters.</p>
                        </div>
                    `;
                }
            } else if (noResultsMsg) {
                // Restore original products if they were hidden
                if (currentProducts.length > 0) {
                    const productsList = document.getElementById('productsList');
                    productsList.innerHTML = '';
                    currentProducts.forEach(product => {
                        productsList.appendChild(product.element);
                    });
                }
            }
        }

        function clearSearch() {
            document.getElementById('searchInput').value = '';
            document.getElementById('inStockFilter').checked = false;
            document.getElementById('outOfStockFilter').checked = false;
            document.getElementById('clearSearchBtn').classList.add('hidden');
            performSearch();
        }

        function sortProducts(sortType) {
            currentSort = sortType;
            const productsList = document.getElementById('productsList');
            
            // Remove existing sort buttons active state
            document.querySelectorAll('[onclick^="sortProducts"]').forEach(btn => {
                btn.classList.remove('bg-blue-500', 'text-white');
                btn.classList.add('bg-gray-100', 'text-gray-700');
            });

            // Add active state to clicked button
            event.target.classList.remove('bg-gray-100', 'text-gray-700');
            event.target.classList.add('bg-blue-500', 'text-white');

            // Sort products
            currentProducts.sort((a, b) => {
                if (sortType === 'name') {
                    return a.name.localeCompare(b.name);
                } else if (sortType === 'stock') {
                    if (a.stock === b.stock) {
                        return a.name.localeCompare(b.name);
                    }
                    return a.stock === 'in' ? -1 : 1;
                }
            });

            // Re-render products
            productsList.innerHTML = '';
            currentProducts.forEach(product => {
                productsList.appendChild(product.element);
            });
        }

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    </script>
</body>
</html> 