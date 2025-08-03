<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;

class SyncCustomerServiceCounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer:sync-service-counts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize customer service counts based on actual transaction data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting customer service count synchronization...');
        
        try {
            Customer::syncAllServiceCounts();
            $this->info('Customer service counts synchronized successfully!');
            return 0;
        } catch (\Exception $e) {
            $this->error('Error synchronizing customer service counts: ' . $e->getMessage());
            return 1;
        }
    }
}
