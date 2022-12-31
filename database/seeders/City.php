<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\City as ModelCity;
use Illuminate\Support\Facades\DB;
use App\Classes\Helper\Text;

class City extends Seeder
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
        if (ModelCity::count() == 0) {
            DB::table($this->text->getCity())->insert([
                $this->text->getId() => 1,
                $this->text->getName() => 'Pando'
            ]);
            DB::table($this->text->getCity())->insert([
                $this->text->getId() => 2,
                $this->text->getName() => 'Beni'
            ]);
            DB::table($this->text->getCity())->insert([
                $this->text->getId() => 3,
                $this->text->getName() => 'Santa Cruz'
            ]);
            DB::table($this->text->getCity())->insert([
                $this->text->getId() => 4,
                $this->text->getName() => 'La Paz'
            ]);
            DB::table($this->text->getCity())->insert([
                $this->text->getId() => 5,
                $this->text->getName() => 'Cochabamba'
            ]);
            DB::table($this->text->getCity())->insert([
                $this->text->getId() => 6,
                $this->text->getName() => 'Oruro'
            ]);
            DB::table($this->text->getCity())->insert([
                $this->text->getId() => 7,
                $this->text->getName() => 'Potosi'
            ]);
            DB::table($this->text->getCity())->insert([
                $this->text->getId() => 8,
                $this->text->getName() => 'Chuquisaca'
            ]);
            DB::table($this->text->getCity())->insert([
                $this->text->getId() => 9,
                $this->text->getName() => 'Tarija'
            ]);
        }
    }
}
