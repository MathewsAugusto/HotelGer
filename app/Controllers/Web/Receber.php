<?php

namespace App\Controllers\Web;

use App\Controllers\Web\Aps\Aps;
use App\Models\Apartamentos;
use App\Models\Cliente_hospedado;
use App\Models\Log_Pagamentos;
use App\Models\Produtos_aps;
use App\Models\User;
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
        $itens = "";
        $container = "";
        $queryParams = $request->getQueryParams();

        if (isset($queryParams['buscar'])) {

            $aps = Apartamentos::getApsLikeName($queryParams['buscar']);

            
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

                //$cliente = Cliente_hospedado::getClienteHospedado($ap->codigo)->fetchObject(Cliente_hospedado::class);

                $itens .= View::render('receber/item', [
                    'ap'      => $ap->numero_ap,
                    'cliente' => $ap->nome,
                    'valor'   => number_format($somaProdutos + (($ap->valor_total * $diarias) + ($horas * $valorHoras)), 2, ',', '.'),
                    'codigo'  => $ap->codigo
                ]);
            }
          
            $container = View::render('receber/index', [
                'itens' => $itens
            ]);

        } else {
            $aps = Apartamentos::getApsReceber();

           
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
        }
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
        $container = View::render('receber/edit', [
            'numero'        => $ap->numero_ap,
            'data_r'        => date('d/m/Y H:i', strtotime($ap->data_reserva)),
            'data_e'        => date('d/m/Y H:i', strtotime($ap->data_entrada)),
            'data_s'        => date('d/m/Y H:i', strtotime($ap->data_saida)),
            'status'        => $statusAP,
            'clientes'      => $contentClientes,
            'valor'         => number_format($totalAll, 2, ",", "."),
            'consumo_prods' => "",
            'tablepro'      => $table,
            'diaria'        =>  $diferenca->h == 0 ? $diferenca->days . " Diária's" : $diferenca->days . ' Diárias e ' . $diferenca->h . 'Horas',
            'button-pagar'  => View::render('receber/btn_pagar', ['codigo' => $ap->codigo,]),
            'button-finish' => $statusAP == "Pago✅" ? View::render('receber/btn_finish', ['codigo' => $ap->codigo,]) : "",
        ]);

        return Page::getPage($container, $request);
    }

    public static function getReceber($request, $codigo)
    {

        $ap = Apartamentos::getApsReceberCodigo($codigo)->fetchObject(Apartamentos::class);


        if (!$ap instanceof Apartamentos) {
            $request->getRouter()->redirect('/?status=404');
        }


        $produtos = Produtos_aps::getProdutosbyAps($ap->codigo);
        $totalProduto = 0;
        while ($prod = $produtos->fetchObject(Produtos_aps::class)) {

            $totalProduto = $totalProduto + ($prod->valor * $prod->quantidade);
        }

        $lista_pagamentos = Log_Pagamentos::getByCodigo($ap->codigo);
        $viewPagamentos = "";

        while ($pag = $lista_pagamentos->fetchObject(Log_Pagamentos::class)) {


            $viewPagamentos .= View::render(
                'aps/pagar/item',
                [
                    'valor' => number_format($pag->valor, 2, ',', '.'),
                    'data'  => date('d/m/Y H:i:s', strtotime($pag->data)),
                    'usuario' => $pag->nome,
                    'tipo' => Aps::tipoPagamento($pag->tipo)
                ]
            );
        }
        $table = View::render('aps/pagar/table', ['itens' => $viewPagamentos]);


        $date1 = new DateTime($ap->data_entrada);
        $date2 = new DateTime($ap->data_saida);
        $difereca = $date1->diff($date2);

        $diarias = $difereca->days;
        $horas   = $difereca->h;
        $valorHoras = $ap->valor_total / 24;
        $valores = json_decode($ap->pagamentos);
        $total = ($ap->valor_total * $diarias) + ($horas * $valorHoras);

        $total_pago = json_decode($ap->pagamentos);

        $t = floatval(str_replace(",", ".", str_replace(".", "", $total_pago->dinheiro))) +
            floatval(str_replace(",", ".", str_replace(".", "", $total_pago->pix))) +
            floatval(str_replace(",", ".", str_replace(".", "", $total_pago->cartao)));


        $container = View::render('aps/pagar/index', [
            'numeroap'   => $ap->numero_ap,
            'dinheiro'   => $valores->dinheiro,
            'pix'        => $valores->pix,
            'cartao'     => $valores->cartao,
            'total'      => number_format($t, 2, ",", "."),
            'valor'      => number_format($total + $totalProduto, 2, ",", "."),
            'resto'      => number_format($total - $t + $totalProduto, 2, ",", "."),
            'rota'       => 'receber',
            'rota_voltar' => $ap->codigo,
            'table'      => $table,
            'codigo'        => $ap->codigo,
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
        $queryParams = $request->getQueryParams();

        $user = User::getUserByEmail($_SESSION['hotelger']['email'])->fetchObject(User::class);

        $ap = Apartamentos::getApsReceberCodigo($codigo)->fetchObject(Apartamentos::class);

        if (!$ap instanceof Apartamentos) {
            $request->getRouter()->redirect('/?status=404');
        }

        $produtos = Produtos_aps::getProdutosbyAps($ap->codigo);
        $totalProduto = 0;
        while ($prod = $produtos->fetchObject(Produtos_aps::class)) {

            $totalProduto = $totalProduto + ($prod->valor * $prod->quantidade);
        }


        date_default_timezone_set('America/Sao_Paulo');
        $ap->data_pag = date('Y-m-d H:i:s');

        //SOMA DOS VALORES JA PAGOS
        $s1 = json_decode($ap->pagamentos);
        $sDin = str_replace(".", "", $s1->dinheiro);
        $sPix = str_replace(".", "", $s1->pix);
        $sCart = str_replace(".", "", $s1->cartao);

        $ss1 = floatval(str_replace(",", ".", $sDin)
            + str_replace(",", ".", $sPix))
            + str_replace(",", ".", $sCart);

        //VALORES SENDO PAGOS POST
        $dinheiroConversao = str_replace('.', '', $postVars['dinheiro']);
        $din = str_replace(',', '.', $dinheiroConversao);

        //PIX CONVERSÃO
        $pixConversao = str_replace('.', '', $postVars['pix']);
        $pix = str_replace(',', '.', $pixConversao);

        //CARTAO CONVERSÃO
        $cartaoConversao = str_replace('.', '', $postVars['cartao']);
        $cart = str_replace(',', '.', $cartaoConversao);

        $date1 = new DateTime($ap->data_entrada);
        $date2 = new DateTime($ap->data_saida);
        $difereca = $date1->diff($date2);

        $diarias = $difereca->days;
        $horas   = $difereca->h;
        $valorHoras = $ap->valor_total / 24;
        $tt = ($ap->valor_total * $diarias) + ($horas * $valorHoras) + $totalProduto;
      

        if ($din + $pix + $cart > $tt || $din + $pix + $cart + $ss1 > $tt) {
            $request->getRouter()->redirect("/receber-pagar/$ap->codigo?status=pg00");
        }

        $ap->pagamentos = json_encode([
            "dinheiro" => number_format(floatval(str_replace(",", ".", $sDin)) + $din, 2, ",", "."),
            "pix" => number_format(floatval(str_replace(",", ".", $sPix)) + $pix, 2, ",", "."),
            "cartao" => number_format(floatval(str_replace(",", ".", $sCart)) + $cart, 2, ",", ".")
        ]);


        $ap->pagar();


        //PAGAR DINHEIRO
        $log = new Log_Pagamentos;
        $log->valor = $din;
        $log->tipo = 0;
        $log->codigo_ap = $ap->codigo;
        $log->data = date('Y-m-d H:i:s');
        $log->usuario = $user->codigo;
        if ($din > 0) $log->insert();

        //PAGAR PIX
        $log = new Log_Pagamentos;
        $log->valor = $pix;
        $log->tipo = 1;
        $log->codigo_ap = $ap->codigo;
        $log->data = date('Y-m-d H:i:s');
        $log->usuario = $user->codigo;
        if ($pix > 0) $log->insert();

        //PAGAR CARTAO
        $log = new Log_Pagamentos;
        $log->valor = $cart;
        $log->tipo  = 2;
        $log->codigo_ap = $ap->codigo;
        $log->data  = date('Y-m-d H:i:s');
        $log->usuario = $user->codigo;
        if ($cart > 0) $log->insert();

        $request->getRouter()->redirect('/receber-pagar/' . $ap->codigo);
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



    /**
     * FINALIZA UM AP PENDENTE
     *
     * @param Request $request
     * @param int $codigo
     */
    public static function getFinalizar($request, $codigo)
    {

        $receber = Apartamentos::getApsReceberCodigo($codigo)->fetchObject(Apartamentos::class);

        $user = User::getUserByEmail($_SESSION['hotelger']['email'])->fetchObject(User::class);
        if (!$receber instanceof Apartamentos) {
            $request->getRouter()->redirect('/?status=404');
        }

        $receber->pendente  = 0;
        $receber->usuario_pag = $user->codigo;

        $receber->finalizarReceber();

        $request->getRouter()->redirect('/receber?status=200');
    }
}
