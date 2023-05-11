<?php

namespace App\Controllers\Web;

use App\Models\Apartamentos;
use App\Models\Cliente_hospedado;
use App\Models\Produtos_aps;
use App\Utils\View;
use DateTime;

class Receber
{


    /**
     * MAIN
     *
     * @param Request $request
     * @return string
     */
    public static function getIndex($request)
    {

        $aps = Apartamentos::getApsReceber();

        $itens = "";

        while ($ap = $aps->fetchObject(Apartamentos::class)) {



            $produtos = Produtos_aps::getProdutosbyAps($ap->codigo);
            $somaProdutos =  0;
            while ($prods = $produtos->fetchObject(Produtos_aps::class)) {
                $somaProdutos += $prods->valor * $prods->quantidade;
            }

            $date1 = new DateTime($ap->data_entrada);
            $date2 = new DateTime($ap->data_saida);
            $difereca   = $date1->diff($date2);
            $diarias    = $difereca->days;
            $horas      = $difereca->h;
            $valorHoras = $ap->valor_total / 24;

            $cliente = Cliente_hospedado::getClienteHospedado($ap->codigo)->fetchObject(Cliente_hospedado::class);

            $itens .= View::render('receber/item', [
                'ap'      => $ap->numero_ap,
                'cliente' => $cliente->nome,
                'valor'   => number_format($somaProdutos + (($ap->valor_total * $diarias) + ($horas * $valorHoras)), 2, ',', '.'),
                'codigo'  => $ap->codigo
            ]);
        }

        $container = View::render('receber/index', [
            'itens' => $itens
        ]);


        return Page::getPage($container, $request);
    }

    public static function getEdit($request, $codigo)
    {
        $ap = Apartamentos::getApsReceberCodigo($codigo)->fetchObject(Apartamentos::class);

        if (!$ap instanceof Apartamentos) {
            $request->getRouter()->redirect('/receber?status=404');
        }

        $clientes = Cliente_hospedado::getClienteHospedado($ap->codigo);

        $contentClientes = '';
        while ($cli = $clientes->fetchObject(Cliente_hospedado::class)) {

            $contentClientes .= View::render('aps/listaclientes', [
                'nome' => $cli->nome,
                'celular' => $cli->celular,
                'cpf' => $cli->cpf

            ]);
        }

        $produtos = Produtos_aps::getProdutosbyAps($ap->codigo);
        $listaProdutos = '';
        $totalProduto = 0;
        while ($prod = $produtos->fetchObject(Produtos_aps::class)) {

            $listaProdutos .= View::render('receber/itemlist', [
                'nome' => $prod->nome,
                'valor' => $prod->valor,
                'quantidade' => $prod->quantidade,
                'numero_ap'  => $ap->numero_ap,
                'codprod'    => $prod->codigo
            ]);
            $totalProduto = $totalProduto + ($prod->valor * $prod->quantidade);
        }
        $table = View::render('receber/tableitens', [
            'produtos' => $listaProdutos
        ]);



        $date1 = new DateTime($ap->data_entrada);
        $date2 = new DateTime($ap->data_saida);

        $diferenca = $date1->diff($date2);
        $totalAll = $totalProduto + ($ap->valor_total * $diferenca->days);
        $totalAll = $totalAll + (($ap->valor_total / 24) * $diferenca->h);

        $container = View::render('receber/edit', [
            'numero' => $ap->numero_ap,
            'data_r' => date('d/m/Y H:i', strtotime($ap->data_reserva)),
            'data_e' => date('d/m/Y H:i', strtotime($ap->data_entrada)),
            'data_s' => date('d/m/Y H:i', strtotime($ap->data_saida)),
            'status' => 'Pendente',
            'clientes' => $contentClientes,
            'valor'   => number_format($totalAll, 2, ",", "."),
            'consumo_prods' => "",
            'tablepro' => $table,
            'diaria' =>  $diferenca->h == 0 ? $diferenca->days . " Diária's" : $diferenca->days . ' Diárias e ' . $diferenca->h . 'Horas',
            'button-pagar' => View::render('receber/btn_pagar', ['codigo' => $ap->codigo,])
        ]);

        return Page::getPage($container, $request);
    }

    public static function getReceber($request, $codigo)
    {
       
        $ap = Apartamentos::getApsReceberCodigo($codigo)->fetchObject(Apartamentos::class);


        if (!$ap instanceof Apartamentos) {
            $request->getRouter()->redirect('/?status=404');
        }

        $container = View::render('aps/pagar/index', [
            'numeroap' => $ap->numero_ap
        ]);

        return Page::getPage($container, $request);
    }

     /**
     * POST PAGAR AP
     *
     * @param Request $request
     * @param int $numeroap
     */
    public static function setReceber($request, $codigo)
    {
        $postVars = $request->getPostVars();

        $ap = Apartamentos::getApsReceberCodigo($codigo)->fetchObject(Apartamentos::class);

        if (!$ap instanceof Apartamentos) {
            $request->getRouter()->redirect('/?status=404');
        }
        date_default_timezone_set('America/Sao_Paulo');
        $ap->data_pag = date('Y-m-d H:i:s');
        $ap->tipo_pagamento = $postVars['pagamento'];
        $ap->pagar();

        $request->getRouter()->redirect('/recibo/' . $ap->codigo);
    }


        /**
     * GET CANCELAR
     *
     * @param Request $request
     * @param int $numeroap
     */
    public static function getCancelar($request, $codigo)
    {
        $ap = Apartamentos::getApsReceberCodigo($codigo)->fetchObject(Apartamentos::class);

        if (!$ap instanceof Apartamentos) {
            $request->getRouter()->redirect('/');
        }

        $container = View::render('aps/cancelar/index', [
            'numeroap' => $ap->numero_ap
        ]);
        return Page::getPage($container, $request);
    }

    /**
     * POST CANCELAR
     *
     * @param Request $request
     * @param int $numeroap
     */
    public static function setCancelar($request, $codigo)
    {
        $ap = Apartamentos::getApsReceberCodigo($codigo)->fetchObject(Apartamentos::class);

        if (!$ap instanceof Apartamentos) {
            $request->getRouter()->redirect('/?status=404');
        }

        $ap->status = 3;
        $ap->cancelar();

        $request->getRouter()->redirect('/receber?status=200');
    }
}
