<?php

namespace App\Controllers\Web;

use App\Controllers\Web\Main\Main;
use App\Models\Apartamentos;
use App\Models\Cliente_hospedado;
use App\Models\Produtos_aps;
use App\Utils\View;
use DateTime;

class Reservas
{

    public static function getIndex($request)
    {
        $reservas = Apartamentos::getReservas();

        $itens = "";
        while ($res = $reservas->fetchObject(Apartamentos::class)) {
            $hospede  = Cliente_hospedado::getClienteHospedado($res->codigo)->fetchObject(Cliente_hospedado::class);


            $itens .= View::render('reservas/item', [
                "ap"         => $res->numero_ap,
                "entrada"    => date('d/m/Y H:i', strtotime($res->data_entrada)),
                "saida"      => date('d/m/Y H:i', strtotime($res->data_saida)),
                "codigo"     => $res->codigo,
                "cliente"    => $hospede->nome

            ]);
        }

        $container = View::render('reservas/index', [
            "itens" => $itens,
            "quarto" => "",
            'button'   => "hidden"
        ]);

        return Page::getPage($container, $request);
    }

    /**
     * GET RESERVAS
     *
     * @param Request $request
     * @param int $codigo
     * @return void
     */
    public static function getReservas($request, $codigo)
    {

        $ap = Apartamentos::getApsByRervado($codigo)->fetchObject(Apartamentos::class);

        if (!$ap instanceof Apartamentos) {
            $request->getRouter()->redirect('/');
        }

        $produtos = Produtos_aps::getProdutosbyAps($ap->codigo);

        $totalProduto = 0;
        while ($prod = $produtos->fetchObject(Produtos_aps::class)) {
            $totalProduto = $totalProduto + ($prod->valor * $prod->quantidade);
        }

        $date1 = new DateTime($ap->data_entrada);
        $date2 = new DateTime($ap->data_saida);

        $diferenca = $date1->diff($date2);
        $totalAll = $totalProduto + ($ap->valor_total * $diferenca->days);
        $totalAll = $totalAll + (($ap->valor_total / 24) * $diferenca->h);
        $din =  $cart = $pix = 0;



        $valores = json_decode($ap->pagamentos);

        //DINHEIRO CONVERSÃO
        $dinheiroConversao = str_replace('.', '', $valores->dinheiro);
        $din = str_replace(',', '.', $dinheiroConversao);

        //PIX CONVERSÃO
        $pixConversao = str_replace('.', '', $valores->pix);
        $pix = str_replace(',', '.', $pixConversao);


        //CARTAO CONVERSÃO
        $cartaoConversao = str_replace('.', '', $valores->cartao);
        $cart = str_replace(',', '.', $cartaoConversao);


        if ($din + $pix + $cart < $totalAll)  $statusAP = "Pago Parcialmente";
        if ($din + $pix + $cart == $totalAll) $statusAP = "Pago✅";

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

            $listaProdutos .= View::render('main/itemlist', [
                'nome' => $prod->nome,
                'valor' => $prod->valor,
                'quantidade' => $prod->quantidade,
                'numero_ap'  => $ap->numero_ap,
                'codprod'    => $prod->codigo
            ]);
            $totalProduto = $totalProduto + ($prod->valor * $prod->quantidade);
        }
        $table = View::render('main/tableitens', [
            'produtos' => $listaProdutos
        ]);


       /*  $totalAll = $totalProduto + ($ap->valor_total * $ap->quantidade);
        $date1 = new DateTime($ap->data_entrada);
        $date2 = new DateTime($ap->data_saida);

        $diferenca = $date1->diff($date2);

        $totalAll = $totalAll + (($ap->valor_total / 24) * $diferenca->h); */

        $ativado = View::render('aps/ativo', [
            'title' => 'Reserva',
            'numero' => $ap->numero_ap,
            'data_r' => date('H:i d/m/Y', strtotime($ap->data_reserva)),
            'data_e' => date('H:i d/m/Y', strtotime($ap->data_entrada)),
            'data_s' => date('H:i d/m/Y', strtotime($ap->data_saida)),
            'valor'  => number_format($totalAll, 2, ",", "."),
            'diaria' => $diferenca->h == 0 ? $ap->quantidade . " Diária's" : $ap->quantidade . ' Diárias e ' . $diferenca->h . 'Horas',
            'clientes' => $contentClientes,
            'tablepro' => $ap->status == 0 ? "" : $table,
            'codigo'   => $codigo,
            'numeroap' => $ap->numero_ap,
            'tipo'     => $ap->data_pag != "" ? Main::tipoPagamento($ap->tipo_pagamento) : "",
            'button-hospedar'   => $ap->status == 0 ? View::render('aps/button', ['numeroap' => $ap->codigo]) : '',
            'button-pagar' => $din + $pix + $cart == $totalAll ? "" : View::render('aps/button_pagar', ['numeroap' => $ap->codigo, 'rota'=>'reservas']),
            'consumo_prods' => $ap->status == 0 ? "" : View::render('aps/consumo_prods', ['codigo' => $ap->numero_ap]),
            'button-finalizar' => $ap->data_pag == "" ? View::render('aps/button_pagar', ['numeroap' => $ap->codigo]) : "",
            'status' => $statusAP,
            




        ]);

        $content = View::render('aps/index', ['content' =>  $ativado]);


        return Page::getPage($content, $request);
    }

    public static function getListaReserAPs($request, $ap)
    {

        $reservas = Apartamentos::getApsByRervados($ap);

        $itens = "";
        while ($res = $reservas->fetchObject(Apartamentos::class)) {

            $cliente = Cliente_hospedado::getClienteHospedado($res->codigo)->fetchObject(Cliente_hospedado::class);

            $itens .= View::render('reservas/item', [
                "ap"         => $res->numero_ap,
                "entrada"    => date('d-m-Y H:i', strtotime($res->data_entrada)),
                "saida"      => date('d-m-Y H:i', strtotime($res->data_saida)),
                "codigo"     => $res->codigo,
                "cliente"       => $cliente->nome
            ]);
        }

        $container = View::render('reservas/index', [
            "itens" =>  $itens,
            "quarto" => 'Ap ' . $ap,
            "ap"    =>  $ap
        ]);

        return Page::getPage($container, $request);
    }



    public static function getExcluiReserva($request, $codigo)
    {
        $reserva = Apartamentos::getApsByRervado($codigo)->fetchObject(Apartamentos::class);

        if (!$reserva instanceof Apartamentos) {
            $request->getRouter()->redirect('/');
        }
        $reserva->excluir();
        $request->getRouter()->redirect('/reservas');
    }
}
