<?php

namespace App\Models;

use WilliamCosta\DatabaseManager\Database;

class Apartamentos
{
    public $codigo;
    public $numero_ap;
    public $data_reserva;
    public $data_entrada;
    public $data_saida;
    public $data_pag;
    public $tipo_pagamento;
    public $valor_total;
    public $quantidade;
    public $desconto;
    public $usuario_pag;
    public $status;
    public $usuario_create;

    public static function getAps($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('apartamentos'))->select($where, $order, $limit, $fields);
    }

    /**
     * retorna um Ap Ativo
     *
     * @param int $numeroAp
     */
    public static function getApsByAtivos($numeroAp)
    {
        return self::getAps("numero_ap = '$numeroAp' AND status < 2");
    }

    public static function getRecibo($numeroAp)
    {
        return self::getAps("codigo = '$numeroAp' AND status = 2");
    }


    public static function getApsOcupados()
    {
        return self::getAps("status = 1");
    }
    public static function getApsRervados()
    {
        return self::getAps("status = 0");
    }


    public function cadastrarHopedagem()
    {
        $this->codigo = (new Database('apartamentos'))->insert([
            'numero_ap' => $this->numero_ap,
            'data_reserva' => $this->data_reserva,
            'data_entrada' => $this->data_entrada,
            'data_saida'   => $this->data_saida,
            'valor_total' => $this->valor_total,
            'status'      => $this->status,
            'usuario_create' => $this->usuario_create,
            'quantidade'   => $this->quantidade
        ]);
        return true;
    }

    /**
     *ATUALIZA A QUANTIDADE 
     */
    public function updateQuantidade()
    {
        return (new Database('apartamentos'))->update(
            "codigo = $this->codigo",
            [
                'quantidade' => $this->quantidade
            ]
        );
    }

    public function pagar()
    {
        return (new Database('apartamentos'))->update(
            "codigo = $this->codigo",
            [
                'tipo_pagamento' => $this->tipo_pagamento,
                'status' => $this->status,
                'data_pag' => $this->data_pag
            ]
        );
    }

    public function cancelar()
    {
        return (new Database('apartamentos'))->update(
            "codigo = $this->codigo",
            [
                'status' => 3
            ]
        );
    }
}
