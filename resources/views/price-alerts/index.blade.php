<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Price Alerts - Stock Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Price Alerts</h1>
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
                    <h3 class="text-sm font-medium text-blue-800">Price Alert System</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Set price alerts for products and get notified when prices drop below your target. Alerts are checked automatically and prevent spam by limiting notifications to once per 24 hours.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create New Alert -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Create New Price Alert</h2>
            
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <p class="text-red-700">{{ session('error') }}</p>
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <p class="text-green-700">{{ session('success') }}</p>
                </div>
            @endif

            <form action="/price-alerts" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="product_id" class="block text-sm font-medium text-gray-700 mb-2">Product</label>
                        <select name="product_id" id="product_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Select a product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="target_price" class="block text-sm font-medium text-gray-700 mb-2">Target Price (₹)</label>
                        <input 
                            type="number" 
                            name="target_price" 
                            id="target_price"
                            step="0.01" 
                            min="0.01"
                            placeholder="Enter target price"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required
                        >
                        @error('target_price')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Create Alert
                </button>
            </form>
        </div>

        <!-- Existing Alerts -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Your Price Alerts</h2>
            
            @if($priceAlerts->count() > 0)
                <div class="space-y-4">
                    @foreach($priceAlerts as $alert)
                        <div class="border border-gray-200 rounded-lg p-4 {{ $alert->is_active ? 'bg-green-50' : 'bg-gray-50' }}">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <h3 class="font-medium text-gray-900">{{ $alert->product->name }}</h3>
                                        <span class="px-2 py-1 text-xs rounded-full {{ $alert->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                            {{ $alert->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Target Price: <span class="font-medium">₹{{ number_format($alert->target_price_in_rupees, 2) }}</span>
                                    </p>
                                    <p class="text-sm text-gray-500 mt-1">
                                        Created: {{ $alert->created_at->format('M j, Y g:i A') }}
                                    </p>
                                    @if($alert->last_triggered_at)
                                        <p class="text-sm text-orange-600 mt-1">
                                            Last triggered: {{ $alert->last_triggered_at->format('M j, Y g:i A') }}
                                        </p>
                                    @endif
                                </div>
                                
                                <div class="flex items-center gap-2">
                                    <form action="/price-alerts/{{ $alert->id }}/toggle" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-3 py-1 text-sm rounded {{ $alert->is_active ? 'bg-yellow-500 text-white hover:bg-yellow-600' : 'bg-green-500 text-white hover:bg-green-600' }}">
                                            {{ $alert->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                    
                                    <button onclick="editAlert({{ $alert->id }}, '{{ $alert->product->name }}', {{ $alert->target_price_in_rupees }}, '{{ $alert->is_active ? 1 : 0 }}')" 
                                            class="px-3 py-1 bg-blue-500 text-white rounded text-sm hover:bg-blue-600">
                                        Edit
                                    </button>
                                    
                                    <form action="/price-alerts/{{ $alert->id }}" method="POST" class="inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this price alert?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded text-sm hover:bg-red-600">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-bell text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">No price alerts set up yet.</p>
                    <p class="text-sm text-gray-400 mt-2">Create your first price alert above to get started.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-semibold mb-4">Edit Price Alert</h3>
            <form id="editForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Product</label>
                    <p id="editProductName" class="text-gray-900 font-medium"></p>
                </div>
                
                <div>
                    <label for="editTargetPrice" class="block text-sm font-medium text-gray-700 mb-2">Target Price (₹)</label>
                    <input 
                        type="number" 
                        name="target_price" 
                        id="editTargetPrice"
                        step="0.01" 
                        min="0.01"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="editIsActive" class="mr-2">
                    <label for="editIsActive" class="text-sm text-gray-700">Active</label>
                </div>
                
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                        Update Alert
                    </button>
                    <button type="button" onclick="closeEditModal()" class="flex-1 px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editAlert(alertId, productName, targetPrice, isActive) {
            document.getElementById('editProductName').textContent = productName;
            document.getElementById('editTargetPrice').value = targetPrice;
            document.getElementById('editIsActive').checked = isActive === '1';
            document.getElementById('editForm').action = `/price-alerts/${alertId}`;
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.getElementById('editModal').classList.remove('flex');
        }

        // Close modal when clicking outside
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });
    </script>
</body>
</html> 