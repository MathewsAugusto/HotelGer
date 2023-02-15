<?php

namespace App\Models;

use WilliamCosta\DatabaseManager\Database;



class Cliente_hospedado
{

    public $codigo;
    public $codigo_hospedagem;
    public $cliente_codigo;

    public static function getCliente($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('cliente_hospedes'))->select($where, $order, $limit, $fields);
    }

    public static function getClienteHospedado($codigo)
    {
        return (new Database("cliente_hospedes JOIN clientes ON cliente_hospedes.clientes_codigo = clientes.codigo"))
            ->select("codigo_hospedagem = $codigo", null, null, "clientes.nome, clientes.celular, clientes.cpf");
    }

    public function insert()
    {
        $this->codigo = (new Database('cliente_hospedes'))->insert([
            'codigo_hospedagem' => $this->codigo_hospedagem,
            'clientes_codigo'  => $this->cliente_codigo
        ]);
        return true;
    }
}
