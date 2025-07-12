<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bulk Update - Stock Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Bulk Update Stock</h1>
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
                    <h3 class="text-sm font-medium text-blue-800">Bulk Update Operations</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Select multiple stock entries and update them simultaneously. All changes are tracked in history and activity logs.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Filters</h2>
            <form id="filterForm" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Product</label>
                    <select name="product_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Products</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Retailer</label>
                    <select name="retailer_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Retailers</option>
                        @foreach($retailers as $retailer)
                            <option value="{{ $retailer->id }}" {{ request('retailer_id') == $retailer->id ? 'selected' : '' }}>
                                {{ $retailer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stock Status</label>
                    <select name="stock_status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                        <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Product, retailer, or SKU"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                        Apply Filters
                    </button>
                </div>
                <div class="flex items-end">
                    <a href="/bulk-update" class="w-full px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 text-center">
                        Clear Filters
                    </a>
                </div>
            </form>
        </div>

        <!-- Bulk Update Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Bulk Update Options</h2>
            <form id="bulkUpdateForm" class="space-y-6">
                <!-- Update Type Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Update Type</label>
                    <select id="updateType" name="update_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Select update type</option>
                        <option value="status">Stock Status</option>
                        <option value="price">Set Price</option>
                        <option value="percentage">Adjust Price by Percentage</option>
                        <option value="url">Update URL</option>
                    </select>
                </div>

                <!-- Dynamic Update Fields -->
                <div id="updateFields" class="space-y-4 hidden">
                    <!-- Status Update -->
                    <div id="statusField" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">New Status</label>
                        <select name="new_status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="1">In Stock</option>
                            <option value="0">Out of Stock</option>
                        </select>
                    </div>

                    <!-- Price Update -->
                    <div id="priceField" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">New Price (₹)</label>
                        <input type="number" name="new_value" step="0.01" min="0" 
                               placeholder="Enter new price"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Percentage Update -->
                    <div id="percentageField" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Percentage Change (%)</label>
                        <input type="number" name="new_value" step="0.1" 
                               placeholder="e.g., 10 for 10% increase, -5 for 5% decrease"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-sm text-gray-500 mt-1">Positive values increase price, negative values decrease price</p>
                    </div>

                    <!-- URL Update -->
                    <div id="urlField" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">New URL</label>
                        <input type="url" name="new_value" 
                               placeholder="https://example.com/product"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" id="bulkUpdateBtn" class="px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="btnText">Update Selected Items</span>
                        <span id="btnLoading" class="hidden">Updating...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Stock Selection -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold">Stock Entries</h2>
                <div class="flex items-center gap-4">
                    <button id="selectAllBtn" class="text-sm text-blue-600 hover:text-blue-800">Select All</button>
                    <button id="deselectAllBtn" class="text-sm text-gray-600 hover:text-gray-800">Deselect All</button>
                    <span id="selectedCount" class="text-sm text-gray-500">0 selected</span>
                </div>
            </div>

            @if($stocks->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left">
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Retailer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($stocks as $stock)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" name="stock_ids[]" value="{{ $stock->id }}" 
                                               class="stock-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $stock->product->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $stock->retailer->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ₹{{ number_format($stock->price / 100, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $stock->in_stock ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $stock->in_stock ? 'In Stock' : 'Out of Stock' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $stock->sku ?: 'N/A' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $stocks->links() }}
                </div>
            @else
                <p class="text-gray-500 text-center py-8">No stock entries found for the selected filters.</p>
            @endif
        </div>
    </div>

    <script>
        // Update type change handler
        document.getElementById('updateType').addEventListener('change', function() {
            const updateFields = document.getElementById('updateFields');
            const statusField = document.getElementById('statusField');
            const priceField = document.getElementById('priceField');
            const percentageField = document.getElementById('percentageField');
            const urlField = document.getElementById('urlField');

            // Hide all fields
            statusField.classList.add('hidden');
            priceField.classList.add('hidden');
            percentageField.classList.add('hidden');
            urlField.classList.add('hidden');
            updateFields.classList.add('hidden');

            // Show relevant field
            if (this.value) {
                updateFields.classList.remove('hidden');
                switch (this.value) {
                    case 'status':
                        statusField.classList.remove('hidden');
                        break;
                    case 'price':
                        priceField.classList.remove('hidden');
                        break;
                    case 'percentage':
                        percentageField.classList.remove('hidden');
                        break;
                    case 'url':
                        urlField.classList.remove('hidden');
                        break;
                }
            }
        });

        // Select all functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.stock-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });

        document.getElementById('selectAllBtn').addEventListener('click', function() {
            document.getElementById('selectAll').checked = true;
            const checkboxes = document.querySelectorAll('.stock-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
            updateSelectedCount();
        });

        document.getElementById('deselectAllBtn').addEventListener('click', function() {
            document.getElementById('selectAll').checked = false;
            const checkboxes = document.querySelectorAll('.stock-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            updateSelectedCount();
        });

        // Individual checkbox change
        document.querySelectorAll('.stock-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCount);
        });

        function updateSelectedCount() {
            const selected = document.querySelectorAll('.stock-checkbox:checked').length;
            document.getElementById('selectedCount').textContent = `${selected} selected`;
        }

        // Bulk update form submission
        document.getElementById('bulkUpdateForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const selectedCheckboxes = document.querySelectorAll('.stock-checkbox:checked');
            if (selectedCheckboxes.length === 0) {
                alert('Please select at least one stock entry to update.');
                return;
            }

            const formData = new FormData(this);
            const stockIds = Array.from(selectedCheckboxes).map(cb => cb.value);
            formData.set('stock_ids', JSON.stringify(stockIds));

            // Show loading state
            const btn = document.getElementById('bulkUpdateBtn');
            const btnText = document.getElementById('btnText');
            const btnLoading = document.getElementById('btnLoading');
            
            btn.disabled = true;
            btnText.classList.add('hidden');
            btnLoading.classList.remove('hidden');

            fetch('/bulk-update', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    stock_ids: stockIds,
                    update_type: formData.get('update_type'),
                    new_value: formData.get('new_value'),
                    new_status: formData.get('new_status')
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload(); // Refresh to show updated data
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating stock entries.');
            })
            .finally(() => {
                // Reset button state
                btn.disabled = false;
                btnText.classList.remove('hidden');
                btnLoading.classList.add('hidden');
            });
        });

        // Initialize selected count
        updateSelectedCount();
    </script>
</body>
</html> 