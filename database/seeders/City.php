<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\City as ModelCity;
use Illuminate\Support\Facades\DB;

class City extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (ModelCity::count() == 0) {
            DB::table('city')->insert([
                'id' => 1,
                'name' => 'Pando'
            ]);
            DB::table('city')->insert([
                'id' => 2,
                'name' => 'Beni'
            ]);
            DB::table('city')->insert([
                'id' => 3,
                'name' => 'Santa Cruz'
            ]);
            DB::table('city')->insert([
                'id' => 4,
                'name' => 'La Paz'
            ]);
            DB::table('city')->insert([
                'id' => 5,
                'name' => 'Cochabamba'
            ]);
            DB::table('city')->insert([
                'id' => 6,
                'name' => 'Oruro'
            ]);
            DB::table('city')->insert([
                'id' => 7,
                'name' => 'Potosi'
            ]);
            DB::table('city')->insert([
                'id' => 8,
                'name' => 'Chuquisaca'
            ]);
            DB::table('city')->insert([
                'id' => 9,
                'name' => 'Tarija'
            ]);
        }
    }
}
