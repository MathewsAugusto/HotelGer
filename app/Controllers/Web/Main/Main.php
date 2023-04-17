<?php

namespace App\Controllers\Web\Main;

use App\Controllers\Web\Page;
use App\Models\Apartamentos;
use App\Models\Cliente;
use App\Models\Cliente_hospedado;
use App\Models\Produtos_aps;
use App\Utils\View;
use DateTime;

class Main
{

    /**
     * GET
     *
     * @param Request5 $request
     */
    public static function getQuartosAps($request)
    {
        $quartosOcupado = Apartamentos::getApsOcupados();
        $quartosReservados = Apartamentos::getApsRervados();


        $boxs = [];
        while ($quartos = $quartosOcupado->fetchObject(Apartamentos::class)) {
            $boxs[] = $quartos->numero_ap;
        }

        $boxsre = [];
        while ($quart = $quartosReservados->fetchObject(Apartamentos::class)) {
            $boxsre[] = $quart->numero_ap;
        }

        $container = "";
        for ($i = 1; $i <= 30; $i++) {
            if (in_array($i, $boxs)) {
                $container .= View::render(
                    'page/container',
                    [
                        'class'  => "apartamento-ativo",
                        'numb'   => $i,
                        'numero' => $i,
                        'sts'    => 'Hospedado'
                    ]
                );
            } else if (in_array($i, $boxsre)) {
                $container .= View::render(
                    'page/container',
                    [
                        'class' => "apartamento-reserv",
                        'numb' => $i,
                        'numero' => $i,
                        'sts'    => 'Reservado'
                    ]
                );
            } else {

                $container .= View::render(
                    'page/container',
                    [
                        'class' => "apartamento",
                        'numb' => $i,
                        'numero' => $i,
                        'sts'    => 'Livre'
                    ]
                );
            }
        }
        return Page::getPage($container, $request);
    }



    /**
     * GET UM AP SE TIVER
     *
     * @param Request $request
     * @param int $codigo
     */
    public static function getQuartosDetalhes($request, $codigo)
    {
        $ap = Apartamentos::getApsByAtivos($codigo)->fetchObject(Apartamentos::class);


        if (!$ap instanceof Apartamentos) {

            

            $desativado = View::render('aps/desativado', [
                'codigo' => $codigo,
                'ap'     => $codigo,
              
            ]);

            $content = View::render('aps/index', [
                'content' => $desativado,
                'codigo' => $codigo
            ]);
        } else {

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


            $totalAll = $totalProduto + ($ap->valor_total * $ap->quantidade);
            $date1 = new DateTime($ap->data_entrada);
            $date2 = new DateTime($ap->data_saida);

            $diferenca = $date1->diff($date2);

            $totalAll = $totalAll + (($ap->valor_total / 24) * $diferenca->h);


            $ativado = View::render('aps/ativo', [
                'numero' => $ap->numero_ap,
                'data_r' => date('H:i d/m/Y', strtotime($ap->data_reserva)),
                'data_e' => date('H:i d/m/Y', strtotime($ap->data_entrada)),
                'data_s' => date('H:i d/m/Y', strtotime($ap->data_saida)),
                'valor'  => number_format($totalAll, 2, ",", "."),
                'diaria' => $diferenca->h == 0 ? $ap->quantidade . " Diária's" : $ap->quantidade . ' Diárias e ' . $diferenca->h . 'Horas',
                'clientes' => $contentClientes,
                'tablepro' => $ap->status == 0 ? "" :$table,
                'codigo'  => $ap->codigo,
                'numeroap' => $ap->numero_ap,
                'button-hospedar'   => $ap->status == 0 ? View::render('aps/button', ['numeroap' => $ap->numero_ap]) : '',
                'button-pagar' => $ap->status == 0 ? "" : View::render('aps/button_pagar', ['numeroap' => $ap->numero_ap]),
                'consumo_prods'=> $ap->status == 0 ? "" : View::render('aps/consumo_prods', ['codigo' => $ap->numero_ap])

            ]);

            $content = View::render('aps/index', ['content' =>  $ativado]);
        }

        return Page::getPage($content, $request);
    }

    /**
     * SET QUANTIDADE
     *
     * @param Request $request
     * @param string $type
     * @param int $codigo
     */
    public static function setQuantidade($request, $type, $numeroap)
    {
        $ap = Apartamentos::getApsByAtivos($numeroap)->fetchObject(Apartamentos::class);

        if (!$ap instanceof Apartamentos) {
            $request->getRouter()->redirect("/");
        }

        $date1 = new DateTime($ap->data_entrada);
        $date2 = new DateTime($ap->data_saida);

        switch ($type) {
            case 'mais':
                $date2->modify("+12 hours");
                $diferenca = $date1->diff($date2);

                $ap->data_saida = $date2->format("Y-m-d H:i:s");
                $ap->quantidade = $diferenca->days;
                break;
            case 'menos':
                $date2->modify("-12 hours");
                $diferenca = $date1->diff($date2);

                $ap->data_saida = $date2->format("Y-m-d H:i:s");
                $ap->quantidade = $diferenca->days;
                break;
        }
        $ap->updateQuantidade();

        $request->getRouter()->redirect("/ap/$numeroap");
    }
}
