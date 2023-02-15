<?php

namespace App\Models;

use WilliamCosta\DatabaseManager\Database;

class Produtos
{

    public $codigo;
    public $nome;
    public $valor;
    public $status = 0;

    public static function getProdutos($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('produtos'))->select($where, $order, $limit, $fields);
    }

    /**
     * retorna um Ap Ativo
     *
     * @param int $numeroAp
     */
    public static function getProdutosAtivos()
    {
        return self::getProdutos("status = 1");
    }

    public static function getById($codigo)
    {
        return self::getProdutos("codigo = '$codigo'");
    }

    /**
     * insere um novo
     *
     */
    public function cadastrar()
    {
        $this->codigo = (new Database('produtos'))->insert([
            'nome'   => $this->nome,
            'valor'  => str_replace(",", ".", $this->valor),
            'status' => $this->status
        ]);
        return true;
    }


    /**
     * update
     *
     */
    public function update()
    {

        return (new Database('produtos'))->update("codigo = '$this->codigo'", [
            'nome'   => $this->nome,
            'valor'  => str_replace(",", ".", $this->valor),
            'status' => $this->status
        ]);
    }

    
    public function delete()
    {
        return (new Database('produtos'))->delete("codigo = $this->codigo");
    }
}
