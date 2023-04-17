<?php

namespace App\Controllers\Web\Reports;

use App\Controllers\Web\Page;
use App\Models\Cliente_hospedado;
use App\Models\Produtos;
use App\Models\Produtos_aps;
use App\Models\Reports as ModelsReports;
use App\Utils\View;
use DateTime;
use JetBrains\PhpStorm\Deprecated;

class Reports
{


    public static function getIndex($request)
    {
        $queryParams = $request->getQueryParams();

        if (isset($queryParams['rel'])) {

            switch ($queryParams['rel']) {
                case 'simples':
                    return self::reportSimples($request);
                    break;
                case 'detalhado':
                    return self::reportDetalhado($request);
                    break;
                case 'produtos-data':
                    return self::reportSaidaProdutos($request);
                    break;
            }
        }



        $content = View::render('reports/list', []);

        return Page::getPage($content, $request);
    }

    public static function reportSimples($request)
    {
        $queryParams = $request->getQueryParams();

        $dataI = $queryParams['dataini'];
        $dataF = $queryParams['datafin'];

        $query = ModelsReports::getReportSimple($dataI, $dataF);

        $somaProdutos = 0;
        $somaQuartos = 0;
        $dinheiroValor = 0;
        $pixValor = 0;
        $CartaoValor = 0;
        $pix = 0;
        $dinheiro = 0;
        $cartao = 0;

        while ($ap = $query->fetchObject(ModelsReports::class)) {

            $produtos = Produtos_aps::getProdutosbyAps($ap->codigo);

            while ($prod = $produtos->fetchObject(Produtos_aps::class)) {

                $somaProdutos += $prod->quantidade * $prod->valor;
                switch ($ap->tipo_pagamento) {

                    case 0:
                        $dinheiroValor += $prod->quantidade * $prod->valor;
                        break;
                    case 1:
                        $pixValor += $prod->quantidade * $prod->valor;
                        break;
                    case 2:
                        $CartaoValor += $prod->quantidade * $prod->valor;
                        break;
                }
            }

            $date1 = new DateTime($ap->data_entrada);
            $date2 = new DateTime($ap->data_saida);
            $difereca = $date1->diff($date2);

            $diarias = $difereca->days;
            $horas   = $difereca->h;
            $valorHoras = $ap->valor_total / 24;

            switch ($ap->tipo_pagamento) {
                case 0:
                    $dinheiro++;
                    $dinheiroValor += $diarias * $ap->valor_total + ($valorHoras * $horas);
                    break;
                case 1:
                    $pix++;
                    $pixValor += $diarias * $ap->valor_total + ($valorHoras * $horas);
                    break;
                case 2:
                    $cartao++;
                    $CartaoValor += $diarias * $ap->valor_total + ($valorHoras * $horas);
                    break;
            }
        }

        $itens = View::render(
            'reports/simples/item',
            [
                'qntdin'   => $dinheiro,
                'qntpix'   => $pix,
                'qntcar'   => $cartao,
                'totaldin' => number_format($dinheiroValor, 2, ",", "."),
                'totalpix' => number_format($pixValor, 2, ",", "."),
                'totalcar' => number_format($CartaoValor, 2, ",", "."),
                'totalpro' => number_format($somaProdutos, 2, ",", "."),
                'total'    =>  number_format($pixValor + $CartaoValor + $dinheiroValor, 2, ",", ".")

            ]

        );

        $content = View::render('reports/simples/table', [
            'itens' => $itens,
            'dataI'    => date('d/m/Y', strtotime($dataI)),
            'dataF'    => date('d/m/Y', strtotime($dataF))
        ]);

        return Page::getPage($content, $request);
    }

    public static function reportDetalhado($request)
    {
        $queryParams = $request->getQueryParams();
        $dataI = $queryParams['dataini'];
        $dataF = $queryParams['datafin'];
        $query =  ModelsReports::getReportDetalhado($dataI, $dataF);
        $viewAps = '';
        $valoDinheiro = 0;
        $valorPix = 0;
        $valorCartao = 0;



        while ($ap = $query->fetchObject(ModelsReports::class)) {
            $viewHospedes = '';
            $viewProdutos = '';
            $hospedes = Cliente_hospedado::getClienteHospedado($ap->codigo);
            while ($hosp = $hospedes->fetchObject(ModelsReports::class)) {
                $viewHospedes .= View::render('reports/detalhado/hospedes', [
                    'nome' => $hosp->nome,
                    'cpf' => $hosp->cpf,
                    'celular' => $hosp->celular
                ]);
            }

            $date1 = new DateTime($ap->data_entrada);
            $date2 = new DateTime($ap->data_saida);
            $difereca = $date1->diff($date2);

            $diarias = $difereca->days;
            $horas   = $difereca->h;
            $valorHoras = $ap->valor_total / 24;

            switch ($ap->tipo_pagamento) {
                case 0:
                    $valoDinheiro += ($ap->valor_total * $diarias) + ($horas * $valorHoras);
                    break;
                case 1:
                    $valorPix += ($ap->valor_total * $diarias) + ($horas * $valorHoras);
                    break;
                case 2:
                    $valorCartao += ($ap->valor_total * $diarias) + ($horas * $valorHoras);
                    break;
            }

            $produtos = Produtos_aps::getProdutosbyAps($ap->codigo);
            $somaProdutos =  0;
            while ($prods = $produtos->fetchObject(Produtos_aps::class)) {
                $somaProdutos += $prods->valor * $prods->quantidade;
                $viewProdutos .= View::render('reports/detalhado/item', [
                    'produto' => $prods->nome,
                    'quantidade' => $prods->quantidade,
                    'valor' => $prods->valor
                ]);



                switch ($ap->tipo_pagamento) {
                    case 0:
                        $valoDinheiro += $prods->valor * $prods->quantidade;
                        break;
                    case 1:
                        $valorPix += $prods->valor * $prods->quantidade;
                        break;
                    case 2:
                        $valorCartao += $prods->valor * $prods->quantidade;
                        break;
                }
            }



            $viewAps .= View::render('reports/detalhado/ap', [
                'numeroap' => $ap->numero_ap,
                'reserva'  => $ap->data_reserva,
                'entrada'  => $ap->data_entrada,
                'saida'    => $ap->data_saida,
                'hospedes' => $viewHospedes,
                'codigo'   => $ap->codigo,
                'diarias'  => $horas == 0 ? $ap->quantidade." Diária's" : $ap->quantidade." Diária's e ".$horas."hr's",
                'valord'   => number_format(($ap->valor_total * $diarias) + ($horas * $valorHoras), 2, ',', '.'),
                'total'    => number_format($somaProdutos + (($ap->valor_total * $diarias) + ($horas * $valorHoras)), 2, ',', '.'),
                'produtos' => View::render('reports/detalhado/table', [
                    'list' => $viewProdutos
                ])
            ]);
        }


        $content = View::render('reports/detalhado/index', [
            'dataI'    => date('d-m-Y', strtotime($dataI)),
            'dataF'    => date('d-m-Y', strtotime($dataF)),
            'content'  => $viewAps,
            'din'      => number_format($valoDinheiro, 2, ',', '.'),
            'pix'      => number_format($valorPix, 2, ',', '.'),
            'card'      => number_format($valorCartao, 2, ',', '.'),
            'total'      => number_format($valoDinheiro + $valorCartao + $valorPix, 2, ',', '.')
        ]);

        return Page::getPage($content, $request);
    }

    public static function reportSaidaProdutos($request)
    {
        $queryParams = $request->getQueryParams();
        $dataI = $queryParams['dataini'];
        $dataF = $queryParams['datafin'];
        $produtos = ModelsReports::getReportSaidaProduto($dataI, $dataF);

        $viewProd = '';
        $all = 0;
        while ($prod = $produtos->fetchObject(ModelsReports::class)) {

            $viewProd .= View::render('reports/produtos/item', [
                'Produto'   => $prod->nome,
                'quantidade' => $prod->quantidade,
                'valor'     => number_format($prod->valor, 2, ',', '.'),
                'total'     => number_format($prod->total, 2, ',', '.')
            ]);

            $all += $prod->total;
        }

        $container = View::render('reports/produtos/index', [
            'itens' => $viewProd,
            'totalall' => number_format($all, 2, ',', '.'),
            'dataI' => date('d/m/Y', strtotime($dataI)),
            'dataF' => date('d/m/Y', strtotime($dataF))
        ]);

        return Page::getPage($container, $request);
    }
}
