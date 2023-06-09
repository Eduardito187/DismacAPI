<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RetornarStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'return_stock:cron';

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
        Log::info("Cron is working fine!");
        return Command::SUCCESS;
    }
}
