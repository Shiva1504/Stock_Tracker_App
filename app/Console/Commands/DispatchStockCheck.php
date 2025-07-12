<?php

namespace App\Console\Commands;

use App\Jobs\CheckStockStatus;
use Illuminate\Console\Command;

class DispatchStockCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:dispatch-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch job to check stock status for all products';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Dispatching stock check job...');
        
        CheckStockStatus::dispatch();
        
        $this->info('Stock check job dispatched successfully!');
        
        return 0;
    }
}
