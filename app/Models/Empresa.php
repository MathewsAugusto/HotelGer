<?php

namespace App\Models;

use WilliamCosta\DatabaseManager\Database;

class Empresa{


    public static function getEmpresa()
    {
        return (new Database('empresa'))->select();
    }

}