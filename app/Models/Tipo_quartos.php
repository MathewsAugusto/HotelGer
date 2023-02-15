<?php

namespace App\Models;

use WilliamCosta\DatabaseManager\Database;

class Tipo_quartos
{
    public $codigo;
    public $descricao;
    public $valor;
    public $max;
    public $status;

    public static function getAllQuatos($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('tipos_quartos'))->select($where, $order, $limit, $fields);
    }

    public static function getAtivos()
    {
        return self::getAllQuatos("status = 1");
    }

    public static function getByCodigo($codigo)
    {
        return self::getAllQuatos("codigo = $codigo");
    }

    public function insert()
    {
         $this->codigo = (new Database('tipos_quartos'))->insert([
            'descricao'=>$this->descricao,
            'valor'    =>$this->valor,
            'max'      =>$this->max,
            'status'   =>$this->status  
         ]);
         return true;
    }

    public function update()
    {

         return (new Database('tipos_quartos'))->update("codigo = $this->codigo", [
            'descricao'=>$this->descricao,
            'valor'    =>$this->valor,
            'max'      =>$this->max,
            'status'   =>$this->status  
         ]);
    }

}
