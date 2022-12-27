<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Municipality as ModelMunicipality;
use Illuminate\Support\Facades\DB;

class Municipality extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (ModelMunicipality::count() == 0) {
            DB::table('municipality')->insert([
                'id' => 1,
                'name' => 'Santa Cruz de la Sierra',
                'id_city' => 3
            ]);
        }
    }
}
