<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Municipality as ModelMunicipality;
use Illuminate\Support\Facades\DB;
use App\Classes\Helper\Text;

class Municipality extends Seeder
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
        if (ModelMunicipality::count() == 0) {
            DB::table($this->text->getMunicipality())->insert([
                $this->text->getId() => 1,
                $this->text->getName() => 'Santa Cruz de la Sierra',
                $this->text->getIdCity() => 3
            ]);
        }
    }
}
