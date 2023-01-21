<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Store as ModelStore;
use Illuminate\Support\Facades\DB;
use App\Classes\Helper\Text;

class InitStore extends Seeder
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
        if (ModelStore::count() == 0) {
            $list = [
                [
                    $this->text->getId() => 1,
                    $this->text->getName() => "La Paz",
                    $this->text->getCode() => "lpz",
                    $this->text->getCreated() => date("Y-m-d H:i:s"),
                    $this->text->getUpdated() => null
                ],
                [
                    $this->text->getId() => 2,
                    $this->text->getName() => "Santa Cruz de la Sierra",
                    $this->text->getCode() => "scz",
                    $this->text->getCreated() => date("Y-m-d H:i:s"),
                    $this->text->getUpdated() => null
                ],
                [
                    $this->text->getId() => 3,
                    $this->text->getName() => "Cochabamba",
                    $this->text->getCode() => "cba",
                    $this->text->getCreated() => date("Y-m-d H:i:s"),
                    $this->text->getUpdated() => null
                ],
                [
                    $this->text->getId() => 5,
                    $this->text->getName() => "Tarija",
                    $this->text->getCode() => "tarija",
                    $this->text->getCreated() => date("Y-m-d H:i:s"),
                    $this->text->getUpdated() => null
                ],
                [
                    $this->text->getId() => 8,
                    $this->text->getName() => "Sucre",
                    $this->text->getCode() => "sucre",
                    $this->text->getCreated() => date("Y-m-d H:i:s"),
                    $this->text->getUpdated() => null
                ],
                [
                    $this->text->getId() => 9,
                    $this->text->getName() => "Montero",
                    $this->text->getCode() => "montero",
                    $this->text->getCreated() => date("Y-m-d H:i:s"),
                    $this->text->getUpdated() => null
                ]
            ];
            ModelStore::insert($list);
        }
    }
}
