<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Stock - Stock Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Edit Stock</h1>
            <a href="/retailers" class="text-blue-500 hover:text-blue-600">← Back to Retailers</a>
        </div>

        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Update Stock Information</h2>
            
            <form action="/stock/{{ $stock->id }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="product_id" class="block text-sm font-medium text-gray-700 mb-2">Product</label>
                        <select name="product_id" id="product_id" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ $stock->product_id == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Price (₹)</label>
                        <input 
                            type="number" 
                            id="price"
                            name="price" 
                            value="{{ old('price', $stock->price / 100) }}"
                            step="0.01"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required
                        >
                        @error('price')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="url" class="block text-sm font-medium text-gray-700 mb-2">Product URL</label>
                        <input 
                            type="url" 
                            id="url"
                            name="url" 
                            value="{{ old('url', $stock->url) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required
                        >
                        @error('url')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sku" class="block text-sm font-medium text-gray-700 mb-2">SKU (Optional)</label>
                        <input 
                            type="text" 
                            id="sku"
                            name="sku" 
                            value="{{ old('sku', $stock->sku) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                        @error('sku')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stock Status</label>
                        <div class="flex items-center">
                            <label class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    name="in_stock" 
                                    value="1" 
                                    class="mr-2"
                                    {{ $stock->in_stock ? 'checked' : '' }}
                                >
                                In Stock
                            </label>
                        </div>
                        @error('in_stock')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex gap-4 mt-6">
                    <button 
                        type="submit" 
                        class="flex-1 px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        Update Stock
                    </button>
                    <a href="/retailers" 
                       class="flex-1 px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 text-center">
                        Cancel
                    </a>
                </div>
            </form>

            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="font-medium text-gray-900 mb-2">Current Information</h3>
                <div class="text-sm text-gray-600">
                    <p><strong>Product:</strong> {{ $stock->product->name }}</p>
                    <p><strong>Retailer:</strong> {{ $stock->retailer->name }}</p>
                    <p><strong>Price:</strong> ₹{{ number_format($stock->price / 100, 2) }}</p>
                    <p><strong>URL:</strong> <a href="{{ $stock->url }}" target="_blank" class="text-blue-500 hover:text-blue-600">{{ $stock->url }}</a></p>
                    @if($stock->sku)
                        <p><strong>SKU:</strong> {{ $stock->sku }}</p>
                    @endif
                    <p><strong>Status:</strong> <span class="{{ $stock->in_stock ? 'text-green-600' : 'text-red-600' }}">{{ $stock->in_stock ? 'In Stock' : 'Out of Stock' }}</span></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 