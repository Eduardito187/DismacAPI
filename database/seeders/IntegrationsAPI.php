<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\IntegrationsAPI as ModelIntegrations;
use App\Classes\Helper\Text;

class IntegrationsAPI extends Seeder
{
    protected $text;

    public function __construct() {
        $this->text = new Text();
    }
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (ModelIntegrations::count() == 0) {
            DB::table($this->text->getIntegrationsApi())->insert([
                $this->text->getId() => 1,
                $this->text->getName() => 'TokenAPP',
                $this->text->getToken() => 'Bearer eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ',
                $this->text->getDomain() => 'AppMobile',
                $this->text->getCreated() => date("Y-m-d H:i:s"),
                $this->text->getUpdated() => null
            ]);
            DB::table($this->text->getIntegrationsApi())->insert([
                $this->text->getId() => 2,
                $this->text->getName() => 'WebSockets',
                $this->text->getToken() => 'Bearer SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c',
                $this->text->getDomain() => 'https://dismac.websockets.com:3000',
                $this->text->getCreated() => date("Y-m-d H:i:s"),
                $this->text->getUpdated() => null
            ]);
        }
    }
}
