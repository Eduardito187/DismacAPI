<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country as ModelCountry;
use Illuminate\Support\Facades\DB;
use App\Classes\Helper\Text;

class Country extends Seeder
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
        if (ModelCountry::count() == 0) {
            DB::table($this->text->getCountry())->insert([
                $this->text->getId() => 1,
                $this->text->getName() => 'Bolivia'
            ]);
        }
    }
}
