<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retailers - Stock Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Retailers</h1>
            <a href="/" class="text-blue-500 hover:text-blue-600">← Back to Products</a>
        </div>
        
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

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Retailers & Stock</h2>
            @if($retailers->count() > 0)
                <div class="space-y-6">
                    @foreach($retailers as $retailer)
                        <div class="border border-gray-200 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $retailer->name }}</h3>
                            
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
                                        placeholder="Product URL" 
                                        class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        required
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
                                            <div>
                                                <p class="font-medium">{{ $stock->product->name }}</p>
                                                <p class="text-sm text-gray-500">
                                                    ₹{{ number_format($stock->price / 100, 2) }}
                                                </p>
                                                @if($stock->sku)
                                                    <p class="text-xs text-gray-400">SKU: {{ $stock->sku }}</p>
                                                @endif
                                            </div>
                                            <div class="text-right">
                                                <a href="{{ $stock->url }}" target="_blank" class="text-blue-500 hover:text-blue-600 text-sm">View</a>
                                                <p class="text-sm {{ $stock->in_stock ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $stock->in_stock ? 'In Stock' : 'Out of Stock' }}
                                                </p>
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
                <p class="text-gray-500">No retailers added yet.</p>
            @endif
        </div>
    </div>
</body>
</html> 