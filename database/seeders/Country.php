<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country as ModelCountry;
use Illuminate\Support\Facades\DB;

class Country extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (ModelCountry::count() == 0) {
            DB::table('country')->insert([
                'id' => 1,
                'name' => 'Bolivia'
            ]);
        }
    }
}
