<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Classes\Product\ProductApi;

class StatusProduct extends Command
{
    /**
     * @var ProductApi
     */
    protected $ProductApi;

    public function __construct() {
        parent::__construct();
        $this->ProductApi = new ProductApi();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'status_product:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancelar ordenes y retornar stock.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->ProductApi->processCron();
        Log::channel('status_product')->info("Cron de estados ejecutado.");
        return Command::SUCCESS;
    }
}