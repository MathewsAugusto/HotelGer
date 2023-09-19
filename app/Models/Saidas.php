<?php

namespace App\Models;

use WilliamCosta\DatabaseManager\Database;

class Saidas
{

    public $codigo;
    public $descricao;
    public $valor;
    public $data_create;
    public $data_vencimento;
    public $data_pagamento;
    public $user_create;
    public $user_pago;
    public $status;
    public $tipo_pagamento;
    public $user_cancel;
    public $data_cancel;


    public static function getSaidas($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('saidas'))->select($where, $order, $limit, $fields);
    }

    public static function getSaidasLancadas()
    {
        return self::getSaidas("status = 0", "data_vencimento ASC");
    }

    public function update()
    {
        return (new Database('saidas'))->update("codigo = $this->codigo", [
            'descricao' => $this->descricao,
            'valor'   => $this->valor,
            'data_vencimento' => $this->data_vencimento
        ]);
    }

    public static function getSaidaByCodigo($codigo)
    {
        return self::getSaidas("codigo = $codigo")->fetchObject(self::class);
    }

    public static function getCancelar($codigo)
    {
        return self::getSaidas("codigo = $codigo AND status = 0")->fetchObject(self::class);
    }


    public function novo()
    {
        $this->codigo = (new Database('saidas'))->insert(
            [
                'descricao'         => $this->descricao,
                'data_create'       => $this->data_create,
                'data_vencimento'   => $this->data_vencimento,
                'user_create'       => $this->user_create,
                'status'            => $this->status,
                'valor'             => $this->valor
            ]
        );

        return true;
    }


    public function pagar()
    {
        return (new Database('saidas'))->update("codigo = $this->codigo", [
            'data_pagamento' => $this->data_pagamento,
            'status     '    => $this->status,
            'tipo_pagamento' => $this->tipo_pagamento,
            'user_pago'           => $this->user_pago
        ]);
    }

    public function cancelar()
    {
        return (new Database('saidas'))->update("codigo = $this->codigo", [
            'data_cancel' => $this->data_cancel,
            'status     '    => $this->status,
            'user_cancel'           => $this->user_cancel
        ]);
    }

}
