<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\IntegrationsAPI as ModelIntegrations;

class IntegrationsAPI extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (ModelIntegrations::count() == 0) {
            DB::table('integrations_api')->insert([
                'id' => 1,
                'name' => 'TokenAPP',
                'token' => 'Bearer eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ',
                'domain' => 'AppMobile',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => null
            ]);
            DB::table('integrations_api')->insert([
                'id' => 2,
                'name' => 'WebSockets',
                'token' => 'Bearer SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c',
                'domain' => 'https://dismac.websockets.com:3000',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => null
            ]);
        }
    }
}
