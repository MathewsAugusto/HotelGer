<?php

namespace App\Models;

use WilliamCosta\DatabaseManager\Database;

class Cliente{

    public $codigo;
    public $nome;
    public $cpf;
    public $celular;
    public $data_at;



    public static function getCliente($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('clientes'))->select($where, $order, $limit, $fields);
    }

    public static function getClienteLike($text){

        return self::getCliente("nome LIKE '$text%'");

    }

    /**
     * retorna um Ap Ativo
     *
     * @param int $numeroAp
     */
    public static function getClienteCnpj($cpf)
    {
        return self::getCliente("cpf = '$cpf'");
    }

    /**
     * insere um novo
     *
     */
    public function cadastrar()
    {
        date_default_timezone_set('America/Sao_Paulo');
       $this->codigo = (new Database('clientes'))->insert([
        'nome'   =>$this->nome,
        'celular'=>$this->celular,
        'cpf'    =>$this->cpf,
        'data_at'=>date('Y-m-d H:s:m')  
       ]);
        return true;
    }


      /**
     * update
     *
     */
    public function update()
    {
       date_default_timezone_set('America/Sao_Paulo');
       return  (new Database('clientes'))->update("cpf = '$this->cpf'",[
        'nome'   =>$this->nome,
        'celular'=>$this->celular,
        'data_at'=>date('Y-m-d H:s:m')  
       ]);
        
    }

}