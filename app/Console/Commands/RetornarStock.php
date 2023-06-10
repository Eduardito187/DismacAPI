<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Classes\Partner\PartnerApi;

class RetornarStock extends Command
{
    /**
     * @var PartnerApi
     */
    protected $PartnerApi;

    public function __construct() {
        parent::__construct();
        $this->PartnerApi = new PartnerApi();
    }

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
        $this->PartnerApi->runProcessCronCommitedStock();
        Log::info("Cron retorno de stock ejecutado.");
        return Command::SUCCESS;
    }
}
