<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Stock Tracker</h1>
            <a href="/retailers" class="text-blue-500 hover:text-blue-600">Manage Retailers →</a>
        </div>
        
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

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Products</h2>
            @if($products->count() > 0)
                <div class="space-y-4">
                    @foreach($products as $product)
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $product->name }}</h3>
                                <p class="text-sm text-gray-500">
                                    @if($product->inStock())
                                        <span class="text-green-600">In Stock</span>
                                    @else
                                        <span class="text-red-600">Out of Stock</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No products added yet.</p>
            @endif
        </div>
    </div>
</body>
</html> 