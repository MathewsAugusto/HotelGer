<?php

namespace App\Models;

use WilliamCosta\DatabaseManager\Database;

class Produtos_aps
{

    public $codigo;
    public $codigo_ap;
    public $codigo_pro;
    public $valor;
    public $quantidade;

    public static function getProdutos($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('produto_ap'))->select($where, $order, $limit, $fields);
    }

    /**
     * retorna um Ap Ativo
     *
     * @param int $numeroAp
     */
    public static function getProdutosbyAps($codigo_ap)
    {
       return (new Database('produto_ap as proap JOIN produtos as pro ON proap.codigo_pro = pro.codigo'))
       ->select("proap.codigo_ap = $codigo_ap", null, null, 'proap.codigo, pro.nome, proap.valor, proap.quantidade as quantidade');
    }

   
    public static function getById($codPedido, $codigo)
    {
        return self::getProdutos("codigo = '$codigo' AND codigo_ap = $codPedido");
    }

    /**
     * insere um novo
     *
     */
    public function cadastrar()
    {
        $this->codigo = (new Database('produto_ap'))->insert([
            'codigo_ap'   => $this->codigo_ap,
            'codigo_pro'  => $this->codigo_pro,
            'valor'       => str_replace(",", ".", $this->valor),
            'quantidade'  => $this->quantidade
            
        ]);
        return true;
    }
    
    public function delete()
    {
        return (new Database('produto_ap'))->delete("codigo = $this->codigo");
    }
}
