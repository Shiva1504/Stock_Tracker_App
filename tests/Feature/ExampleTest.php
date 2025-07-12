<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Retailer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Stock;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_checks_stock_for_products_at_retailers()
    {
        $switch = Product::create([
            'name' => 'Switch',
        ]);

        $bestBuy = Retailer::create([
            'name' => 'Best Buy',
        ]);

        $this->assertFalse($switch->inStock());

        $stock = new Stock([
            'price' => 10000,
            'url' => 'http://foo.com',
            'sku' => '1234567890',
            'in_stock' => true,
        ]);

        $bestBuy->addStock($switch, $stock);

        $this->assertTrue($switch->inStock());
    }
    
}
