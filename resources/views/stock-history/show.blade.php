<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock History Details - Stock Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Stock History Details</h1>
            <a href="/stock-history" class="text-blue-500 hover:text-blue-600">← Back to History</a>
        </div>

        <!-- Stock Info Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Stock Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Product</h3>
                    <p class="text-lg font-semibold text-gray-900">{{ $stock->product->name }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Retailer</h3>
                    <p class="text-lg font-semibold text-gray-900">{{ $stock->retailer->name }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Current Status</h3>
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $stock->in_stock ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $stock->in_stock ? 'In Stock' : 'Out of Stock' }}
                    </span>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Current Price</h3>
                    <p class="text-lg font-semibold text-gray-900">₹{{ number_format($stock->price / 100, 2) }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">SKU</h3>
                    <p class="text-lg font-semibold text-gray-900">{{ $stock->sku ?: 'N/A' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Product URL</h3>
                    <a href="{{ $stock->url }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">View Product</a>
                </div>
            </div>
        </div>

        <!-- Charts -->
        @if($trends['price_trends']->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Price History Chart -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Price History</h3>
                <canvas id="priceChart" width="400" height="200"></canvas>
            </div>

            <!-- Stock Status Timeline -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Stock Status Timeline</h3>
                <canvas id="statusChart" width="400" height="200"></canvas>
            </div>
        </div>
        @endif

        <!-- History Timeline -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-6">History Timeline</h2>
            @if($history->count() > 0)
                <div class="flow-root">
                    <ul role="list" class="-mb-8">
                        @foreach($history as $index => $entry)
                            <li>
                                <div class="relative pb-8">
                                    @if($index < $history->count() - 1)
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            @if($entry->in_stock)
                                                <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </span>
                                            @else
                                                <span class="h-8 w-8 rounded-full bg-red-500 flex items-center justify-center ring-8 ring-white">
                                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">
                                                    Price changed to <span class="font-medium text-gray-900">₹{{ number_format($entry->price / 100, 2) }}</span>
                                                    and status is <span class="font-medium {{ $entry->in_stock ? 'text-green-600' : 'text-red-600' }}">
                                                        {{ $entry->in_stock ? 'In Stock' : 'Out of Stock' }}
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                <time datetime="{{ $entry->changed_at->format('Y-m-d H:i:s') }}">
                                                    {{ $entry->changed_at->format('M d, Y H:i') }}
                                                </time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <p class="text-gray-500 text-center py-8">No history available for this stock entry.</p>
            @endif
        </div>
    </div>

    @if($trends['price_trends']->count() > 0)
    <script>
        // Price History Chart
        const priceCtx = document.getElementById('priceChart').getContext('2d');
        new Chart(priceCtx, {
            type: 'line',
            data: {
                labels: @json($trends['price_trends']->pluck('date')),
                datasets: [{
                    label: 'Price (₹)',
                    data: @json($trends['price_trends']->pluck('price')),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₹' + value;
                            }
                        }
                    }
                }
            }
        });

        // Status Timeline Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'line',
            data: {
                labels: @json($trends['status_changes']->pluck('date')),
                datasets: [{
                    label: 'Stock Status',
                    data: @json($trends['status_changes']->map(function($item) { return $item['in_stock'] ? 1 : 0; })),
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.1,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        min: 0,
                        max: 1,
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                return value === 1 ? 'In Stock' : 'Out of Stock';
                            }
                        }
                    }
                }
            }
        });
    </script>
    @endif
</body>
</html> 