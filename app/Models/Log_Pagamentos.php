<?php

namespace App\Models;

use WilliamCosta\DatabaseManager\Database;

class Log_Pagamentos
{
    public $codigo;
    public $valor;
    public $tipo;
    public $codigo_ap;
    public $data;
    public $usuario;


    public static function getLog_pagamentos($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('log_pagamentos'))->select($where, $order, $limit, $fields);
    }

    public static function select($codigo)
    {
        return self::getLog_pagamentos("codigo = $codigo")->fecthObject(self::class);
    }

    public function insert()
    {
        return (new Database('log_pagamentos'))->insert([
            'valor' => $this->valor,
            'tipo' => $this->tipo,
            'codigo_ap' => $this->codigo_ap,
            'data' => $this->data,
            'usuario' => $this->usuario
        ]);
    }
}
