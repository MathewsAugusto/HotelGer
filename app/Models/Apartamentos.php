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
    public static function getApsByAtivos($numeroap)
    {
        return self::getAps("numero_ap = '$numeroap' AND status = 1");
    }

    /**
     * retorna um Ap Ativo parametro codigo
     *
     * @param int $codigo
     */
    public static function getApsByAtivosID($codigo)
    {
        return self::getAps("codigo = '$codigo' AND status = 1");
    }

  

    public static function getApsByAtivosCodigo($codigo)
    {
        return self::getAps("codigo = '$codigo' AND status <= 1");
    }

    public static function getApsReceber()
    {
        return self::getAps("data_pag is NULL AND status = 2");
    }

    public static function getApsReceberCodigo($codigo)
    {
        return self::getAps("data_pag is NULL AND status = 2 AND codigo = $codigo");
    }

    public static function getReservas()
    {
        return self::getAps("status = 0", 'data_entrada ASC');
    }


    public static function getRecibo($numeroAp)
    {
        return self::getAps("codigo = '$numeroAp' AND status <= 2");
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
                'quantidade' => $this->quantidade,
                "data_saida" => $this->data_saida

            ]
        );
    }


    public function atualizaDataSaida()
    {
        return (new Database('apartamentos'))->update(
            "codigo = $this->codigo",
            [
                'quantidade' => $this->quantidade,
                "data_saida" => $this->data_saida,
                "numero_ap"  => $this->numero_ap

            ]
        );
    }
    public function atualizaDataEntrada()
    {
        return (new Database('apartamentos'))->update(
            "codigo = $this->codigo",
            [
                'quantidade' => $this->quantidade,
                "data_entrada" => $this->data_entrada

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

    public function finalizar()
    {
        return (new Database('apartamentos'))->update(
            "codigo = $this->codigo",
            [
                'status' => $this->status,

            ]
        );
    }

    public function cancelar()
    {
        return (new Database('apartamentos'))->update(
            "codigo = $this->codigo",
            [
                'status' => 3,
                'data_pag'=>null
            ]
        );
    }

    public static function getApsByRervado($codigo)
    {
        return self::getAps("status = 0 AND codigo = $codigo");
    }

    public static function getApsByRervados($ap)
    {
        return self::getAps("status = 0 AND numero_ap = '$ap'");
    }

    public function setAtiveReservaToHospeda()
    {
        return (new Database('apartamentos'))->update("numero_ap = $this->numero_ap AND codigo = $this->codigo", [
            "status" => $this->status
        ]);
    }

    public function excluir()
    {
        return (new Database('apartamentos'))->update(
            "codigo = $this->codigo",
            [
                'data_pag' => null,
                'status' => 3
            ]

        );
    }

    public static function getApEditeOcupado($codigo)
    {
        return self::getAps("codigo = $codigo");
    }
}
