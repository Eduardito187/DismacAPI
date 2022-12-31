<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Classes\Helper\Text;
use App\Models\Permissions as ModelPermissions;
use App\Models\Rol as ModelRol;
use App\Models\RolPermissions as ModelRolPermissions;

class InitialPermissions extends Seeder
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
        $this->setPermissions();
        $this->setRols();
        $this->setRolPermissions();
    }

    private function setPermissions(){
        $listPermissions = [
            [
                $this->text->getId() => 1,
                $this->text->getName() => "Create account",
                $this->text->getCode() => "cod_00001",
                $this->text->getCreated() => date("Y-m-d H:i:s"),
                $this->text->getUpdated() => null
            ],
            [
                $this->text->getId() => 2,
                $this->text->getName() => "Edit account",
                $this->text->getCode() => "cod_00002",
                $this->text->getCreated() => date("Y-m-d H:i:s"),
                $this->text->getUpdated() => null
            ],
            [
                $this->text->getId() => 3,
                $this->text->getName() => "Disable account",
                $this->text->getCode() => "cod_00003",
                $this->text->getCreated() => date("Y-m-d H:i:s"),
                $this->text->getUpdated() => null
            ],
            [
                $this->text->getId() => 4,
                $this->text->getName() => "Delete account",
                $this->text->getCode() => "cod_00004",
                $this->text->getCreated() => date("Y-m-d H:i:s"),
                $this->text->getUpdated() => null
            ],
            [
                $this->text->getId() => 5,
                $this->text->getName() => "Reset password",
                $this->text->getCode() => "cod_00005",
                $this->text->getCreated() => date("Y-m-d H:i:s"),
                $this->text->getUpdated() => null
            ],
            [
                $this->text->getId() => 6,
                $this->text->getName() => "Create catalog",
                $this->text->getCode() => "cod_00006",
                $this->text->getCreated() => date("Y-m-d H:i:s"),
                $this->text->getUpdated() => null
            ],
            [
                $this->text->getId() => 7,
                $this->text->getName() => "Update catalog",
                $this->text->getCode() => "cod_00007",
                $this->text->getCreated() => date("Y-m-d H:i:s"),
                $this->text->getUpdated() => null
            ],
            [
                $this->text->getId() => 8,
                $this->text->getName() => "Disable catalog",
                $this->text->getCode() => "cod_00008",
                $this->text->getCreated() => date("Y-m-d H:i:s"),
                $this->text->getUpdated() => null
            ],
            [
                $this->text->getId() => 9,
                $this->text->getName() => "Delete catalog",
                $this->text->getCode() => "cod_00009",
                $this->text->getCreated() => date("Y-m-d H:i:s"),
                $this->text->getUpdated() => null
            ],
            [
                $this->text->getId() => 10,
                $this->text->getName() => "Upload picture",
                $this->text->getCode() => "cod_00010",
                $this->text->getCreated() => date("Y-m-d H:i:s"),
                $this->text->getUpdated() => null
            ],
            [
                $this->text->getId() => 11,
                $this->text->getName() => "Delete picture",
                $this->text->getCode() => "cod_00011",
                $this->text->getCreated() => date("Y-m-d H:i:s"),
                $this->text->getUpdated() => null
            ],
            [
                $this->text->getId() => 12,
                $this->text->getName() => "Update picture",
                $this->text->getCode() => "cod_00012",
                $this->text->getCreated() => date("Y-m-d H:i:s"),
                $this->text->getUpdated() => null
            ],
            [
                $this->text->getId() => 13,
                $this->text->getName() => "Share picture",
                $this->text->getCode() => "cod_00013",
                $this->text->getCreated() => date("Y-m-d H:i:s"),
                $this->text->getUpdated() => null
            ],
            [
                $this->text->getId() => 14,
                $this->text->getName() => "Edit partner",
                $this->text->getCode() => "cod_00014",
                $this->text->getCreated() => date("Y-m-d H:i:s"),
                $this->text->getUpdated() => null
            ],
            [
                $this->text->getId() => 15,
                $this->text->getName() => "Disable partner",
                $this->text->getCode() => "cod_00015",
                $this->text->getCreated() => date("Y-m-d H:i:s"),
                $this->text->getUpdated() => null
            ],
            [
                $this->text->getId() => 16,
                $this->text->getName() => "Delete partner",
                $this->text->getCode() => "cod_00016",
                $this->text->getCreated() => date("Y-m-d H:i:s"),
                $this->text->getUpdated() => null
            ],
            [
                $this->text->getId() => 17,
                $this->text->getName() => "Change SuperAdmin",
                $this->text->getCode() => "cod_00017",
                $this->text->getCreated() => date("Y-m-d H:i:s"),
                $this->text->getUpdated() => null
            ],
            [
                $this->text->getId() => 18,
                $this->text->getName() => "Request category",
                $this->text->getCode() => "cod_00018",
                $this->text->getCreated() => date("Y-m-d H:i:s"),
                $this->text->getUpdated() => null
            ],
            [
                $this->text->getId() => 19,
                $this->text->getName() => "Request filter",
                $this->text->getCode() => "cod_00019",
                $this->text->getCreated() => date("Y-m-d H:i:s"),
                $this->text->getUpdated() => null
            ],
            [
                $this->text->getId() => 20,
                $this->text->getName() => "Request attribute",
                $this->text->getCode() => "cod_00020",
                $this->text->getCreated() => date("Y-m-d H:i:s"),
                $this->text->getUpdated() => null
            ],
            [
                $this->text->getId() => 21,
                $this->text->getName() => "Request brand",
                $this->text->getCode() => "cod_00021",
                $this->text->getCreated() => date("Y-m-d H:i:s"),
                $this->text->getUpdated() => null
            ],
            [
                $this->text->getId() => 22,
                $this->text->getName() => "Create product",
                $this->text->getCode() => "cod_00022",
                $this->text->getCreated() => date("Y-m-d H:i:s"),
                $this->text->getUpdated() => null
            ],
            [
                $this->text->getId() => 23,
                $this->text->getName() => "Edit product",
                $this->text->getCode() => "cod_00023",
                $this->text->getCreated() => date("Y-m-d H:i:s"),
                $this->text->getUpdated() => null
            ],
            [
                $this->text->getId() => 24,
                $this->text->getName() => "Disable product",
                $this->text->getCode() => "cod_00024",
                $this->text->getCreated() => date("Y-m-d H:i:s"),
                $this->text->getUpdated() => null
            ],
            [
                $this->text->getId() => 25,
                $this->text->getName() => "Delete product",
                $this->text->getCode() => "cod_00025",
                $this->text->getCreated() => date("Y-m-d H:i:s"),
                $this->text->getUpdated() => null
            ],
            [
                $this->text->getId() => 26,
                $this->text->getName() => "Assign product in category",
                $this->text->getCode() => "cod_00026",
                $this->text->getCreated() => date("Y-m-d H:i:s"),
                $this->text->getUpdated() => null
            ],
            [
                $this->text->getId() => 27,
                $this->text->getName() => "Assign category in catalog",
                $this->text->getCode() => "cod_00027",
                $this->text->getCreated() => date("Y-m-d H:i:s"),
                $this->text->getUpdated() => null
            ],
            [
                $this->text->getId() => 28,
                $this->text->getName() => "Generate report",
                $this->text->getCode() => "cod_00028",
                $this->text->getCreated() => date("Y-m-d H:i:s"),
                $this->text->getUpdated() => null
            ]
        ];
        ModelPermissions::insert($listPermissions);
    }
    
    private function setRols(){
        $listModelRols = [
            [
                $this->text->getId() => 1,
                $this->text->getName() => "Super Admin",
                $this->text->getCode() => "rol_00001",
                $this->text->getCreated() => date("Y-m-d H:i:s"),
                $this->text->getUpdated() => null
            ]
        ];
        ModelRol::insert($listModelRols);
    }

    private function setRolPermissions(){
        $listModelRolPermissions = [
            [
                $this->text->getIdRol() => 1,
                $this->text->getIdRolPermissions() => 1,
            ],
            [
                $this->text->getIdRol() => 1,
                $this->text->getIdRolPermissions() => 2,
            ],
            [
                $this->text->getIdRol() => 1,
                $this->text->getIdRolPermissions() => 3,
            ],
            [
                $this->text->getIdRol() => 1,
                $this->text->getIdRolPermissions() => 4,
            ],
            [
                $this->text->getIdRol() => 1,
                $this->text->getIdRolPermissions() => 5,
            ],
            [
                $this->text->getIdRol() => 1,
                $this->text->getIdRolPermissions() => 6,
            ],
            [
                $this->text->getIdRol() => 1,
                $this->text->getIdRolPermissions() => 7,
            ],
            [
                $this->text->getIdRol() => 1,
                $this->text->getIdRolPermissions() => 8,
            ],
            [
                $this->text->getIdRol() => 1,
                $this->text->getIdRolPermissions() => 9,
            ],
            [
                $this->text->getIdRol() => 1,
                $this->text->getIdRolPermissions() => 10,
            ],
            [
                $this->text->getIdRol() => 1,
                $this->text->getIdRolPermissions() => 11,
            ],
            [
                $this->text->getIdRol() => 1,
                $this->text->getIdRolPermissions() => 12,
            ],
            [
                $this->text->getIdRol() => 1,
                $this->text->getIdRolPermissions() => 13,
            ],
            [
                $this->text->getIdRol() => 1,
                $this->text->getIdRolPermissions() => 14,
            ],
            [
                $this->text->getIdRol() => 1,
                $this->text->getIdRolPermissions() => 15,
            ],
            [
                $this->text->getIdRol() => 1,
                $this->text->getIdRolPermissions() => 16,
            ],
            [
                $this->text->getIdRol() => 1,
                $this->text->getIdRolPermissions() => 17,
            ],
            [
                $this->text->getIdRol() => 1,
                $this->text->getIdRolPermissions() => 18,
            ],
            [
                $this->text->getIdRol() => 1,
                $this->text->getIdRolPermissions() => 19,
            ],
            [
                $this->text->getIdRol() => 1,
                $this->text->getIdRolPermissions() => 20,
            ],
            [
                $this->text->getIdRol() => 1,
                $this->text->getIdRolPermissions() => 21,
            ],
            [
                $this->text->getIdRol() => 1,
                $this->text->getIdRolPermissions() => 22,
            ],
            [
                $this->text->getIdRol() => 1,
                $this->text->getIdRolPermissions() => 23,
            ],
            [
                $this->text->getIdRol() => 1,
                $this->text->getIdRolPermissions() => 24,
            ],
            [
                $this->text->getIdRol() => 1,
                $this->text->getIdRolPermissions() => 25,
            ],
            [
                $this->text->getIdRol() => 1,
                $this->text->getIdRolPermissions() => 26,
            ],
            [
                $this->text->getIdRol() => 1,
                $this->text->getIdRolPermissions() => 27,
            ],
            [
                $this->text->getIdRol() => 1,
                $this->text->getIdRolPermissions() => 28,
            ]
        ];
        ModelRolPermissions::insert($listModelRolPermissions);
    }
}
