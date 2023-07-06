<?php

namespace App\Controllers\Web\Aps;

use App\Controllers\Web\Page;
use App\Models\Apartamentos;
use App\Models\Cliente;
use App\Models\Cliente_hospedado;
use App\Models\Log_Pagamentos;
use App\Models\Produtos;
use App\Models\Produtos_aps;
use App\Models\Tipo_quartos;
use App\Models\User;
use App\Utils\View;
use DateTime;

class Aps
{

    /**
     * GET O FORM PARA NOVA ENTRADA DE QUARTO/APS
     *
     * @param Request $request
     * @param int $codigo
     */
    public static function getNovo($request, $numeroap)
    {

        $tipos_q = Tipo_quartos::getAtivos();
        $lista = '';
        while ($tipo = $tipos_q->fetchObject(Tipo_quartos::class)) {
            $lista .= View::render('aps/item', [
                'descricao'   => $tipo->descricao,
                'valor'       => $tipo->valor,
                'maximo'      => $tipo->max,
                'codigotipo'  => $tipo->codigo,
                'numeroap'    => $numeroap
            ]);
        }

        $container = View::render('aps/header-item', [
            'itens' => $lista,
            'numeroap' => $numeroap
        ]);


        return Page::getPage($container, $request);
    }


    public static function getHospedar($request, $numeroap, $tipo)
    {
        $tipo = Tipo_quartos::getByCodigo($tipo)->fetchObject(Tipo_quartos::class);
        $quartoap = Apartamentos::getApsByAtivos($numeroap)->fetchObject(Apartamentos::class);


        if ($quartoap instanceof Apartamentos) {
            $request->getRouter()->redirect("/");
        }


        if (!$tipo instanceof Tipo_quartos) {
            $request->getRouter()->redirect('/');
        }
        $item = '';
        for ($i = 0; $i < $tipo->max; $i++) {
            if ($i == 0) {
                $item .= View::render('aps/item_form', ['required' => "required"]);
            } else {
                $item .= View::render('aps/item_form', ['required' => ""]);
            }
        }
        date_default_timezone_set('America/Sao_Paulo');
        $container = View::render('aps/form', [
            'item' => $item,
            'data' => date('H:i d/m/Y'),
            'numero' => $numeroap
        ]);

        return Page::getPage($container, $request);
    }

    public static function setHospedar($request, $numeroap, $tipo)
    {
        $postVars = $request->getPostVars();

        date_default_timezone_set('America/Sao_Paulo');
        $tipos_q = Tipo_quartos::getByCodigo($tipo)->fetchObject(Tipo_quartos::class);
        if (!$tipos_q instanceof Tipo_quartos) {
            $request->getRouter()->redirect('/');
        }



        $usuario = User::getUserByEmail($_SESSION['hotelger']['email'])->fetchObject(User::class);

        $apartamentos = new Apartamentos;
        $apartamentos->numero_ap    = $numeroap;
        $apartamentos->data_reserva = date('Y-m-d H:i:s');
        $apartamentos->data_entrada = date('Y-m-d H:i:s');
        $apartamentos->data_saida   = $postVars['datesaida'] . ' ' . $postVars['horasaida'] . ':00';
        $apartamentos->valor_total  = $tipos_q->valor;
        $apartamentos->status       = 1;
        $apartamentos->usuario_create = $usuario->codigo;


        $date1 = new DateTime($apartamentos->data_entrada);
        $date2 = new DateTime($apartamentos->data_saida);
        $diferencas  = $date1->diff($date2);
        $diarias = $diferencas->days;

        $apartamentos->quantidade   = $diarias;


        $apartamentos->cadastrarHopedagem();

        for ($i = 0; count($postVars['nome']) > $i; $i++) {

            if (!empty($postVars['nome'][$i])) {

                $cliente = Cliente::getClienteCnpj($postVars['cpf'][$i])->fetchObject(Cliente::class);

                if (!$cliente instanceof Cliente) {
                    $cliente = new Cliente;
                    $cliente->nome = $postVars['nome'][$i];
                    $cliente->cpf  = $postVars['cpf'][$i];
                    $cliente->celular = $postVars['celular'][$i];
                    $cliente->cadastrar();
                } else {
                    $cliente->nome = $postVars['nome'][$i];
                    $cliente->celular = $postVars['celular'][$i];
                    $cliente->update();
                }

                $cliente_hos = new Cliente_hospedado;
                $cliente_hos->codigo_hospedagem = $apartamentos->codigo;
                $cliente_hos->cliente_codigo    = $cliente->codigo;
                $cliente_hos->insert();
            }
        }


        $request->getRouter()->redirect('/');
    }

    public static function getReservar($request, $numeroap, $tipo)
    {
        $tipo = Tipo_quartos::getByCodigo($tipo)->fetchObject(Tipo_quartos::class);
        $quartoap = Apartamentos::getApsByAtivos($numeroap)->fetchObject(Apartamentos::class);

        /*  if ($quartoap instanceof Apartamentos) {
            $request->getRouter()->redirect("/");
        }
        */

        if (!$tipo instanceof Tipo_quartos) {
            $request->getRouter()->redirect('/');
        }
        $item = '';
        for ($i = 0; $i < $tipo->max; $i++) {
            $item .= View::render('aps/item_form', []);
        }
        date_default_timezone_set('America/Sao_Paulo');
        $container = View::render('aps/reservar/index', [
            'item' => $item,
            'data' => date('H:i d/m/Y'),
            'datareserva' => date('H:i d/m/Y'),
            'numero' => $numeroap
        ]);

        return Page::getPage($container, $request);
    }

    public static function setReservar($request, $numeroap, $tipo)
    {
        $postVars = $request->getPostVars();

        date_default_timezone_set('America/Sao_Paulo');
        $tipos_q = Tipo_quartos::getByCodigo($tipo)->fetchObject(Tipo_quartos::class);
        if (!$tipos_q instanceof Tipo_quartos) {
            $request->getRouter()->redirect('/');
        }


        $usuario = User::getUserByEmail($_SESSION['hotelger']['email'])->fetchObject(User::class);

        $data_entrada = $postVars['dateentrada'] . ' ' . $postVars['horaentrada'] . ':00';
        $data_saida   = $postVars['datesaida'] . ' ' . $postVars['horasaida'] . ':00';
        $apartamentos = new Apartamentos;
        $apartamentos->numero_ap    = $numeroap;
        $apartamentos->data_reserva = date('Y-m-d H:i:s');
        $apartamentos->data_entrada = $data_entrada;
        $apartamentos->data_saida   = $data_saida;
        $apartamentos->valor_total  = $tipos_q->valor;
        $apartamentos->status       = 0;
        $apartamentos->usuario_create = $usuario->codigo;



        $date1 = new DateTime($apartamentos->data_entrada);
        $date2 = new DateTime($apartamentos->data_saida);
        $diferencas  = $date1->diff($date2);
        $diarias = $diferencas->days;

        $apartamentos->quantidade   = $diarias;

        $apartamentos->cadastrarHopedagem();

        for ($i = 0; count($postVars['nome']) > $i; $i++) {

            if (!empty($postVars['nome'][$i])) {

                $cliente = Cliente::getClienteCnpj($postVars['cpf'][$i])->fetchObject(Cliente::class);

                if (!$cliente instanceof Cliente) {
                    $cliente = new Cliente;
                    $cliente->nome = $postVars['nome'][$i];
                    $cliente->cpf  = $postVars['cpf'][$i];
                    $cliente->celular = $postVars['celular'][$i];
                    $cliente->cadastrar();
                } else {
                    $cliente->nome = $postVars['nome'][$i];
                    $cliente->celular = $postVars['celular'][$i];
                    $cliente->update();
                }

                $cliente_hos = new Cliente_hospedado;
                $cliente_hos->codigo_hospedagem = $apartamentos->codigo;
                $cliente_hos->cliente_codigo    = $cliente->codigo;
                $cliente_hos->insert();
            }
        }


        $request->getRouter()->redirect('/');
    }

    /**
     * ADD PRODUTO NO AP GET
     *
     * @param Request$request
     */
    public static function getListaAddProduto($request, $numeroap)
    {
        $ap = Apartamentos::getApsByAtivos($numeroap)->fetchObject(Apartamentos::class);
        if (!$ap instanceof Apartamentos) {
            $request->getRouter()->redirect('/');
        }


        $produtos = Produtos::getProdutosAtivos();
        $container = '';
        while ($pro = $produtos->fetchObject(Produtos::class))
            $container .= View::render('aps/itemaddprod', [
                'nome' => $pro->nome,
                'valor' => number_format($pro->valor, 2, ",", "."),
                'numeroap' => $numeroap,
                'codprod' => $pro->codigo
            ]);

        $index = View::render('aps/addprodindex', [
            'produtos' => $container,
            'numeroap' => $numeroap
        ]);

        return Page::getPage($index, $request);
    }

    public static function getAddProdutoConfirma($request, $codiproProd, $numeroap)
    {

        $ap = Apartamentos::getApsByAtivos($numeroap)->fetchObject(Apartamentos::class);
        if (!$ap instanceof Apartamentos) {
            $request->getRouter()->redirect('/');
        }
        $produto = Produtos::getById($codiproProd)->fetchObject(Produtos::class);
        if (!$produto instanceof Produtos) {
            $request->getRouter()->redirect('/');
        }
        $container = View::render('aps/quantidadeaddpro', [
            'nome' => $produto->nome,
            'valor' => number_format($produto->valor, 2, ",", "."),
            'numero' => $numeroap

        ]);


        return Page::getPage($container, $request);
    }

    /**
     * POST ADD PRODUTO
     *
     * @param Request $request
     * @param int $codiproProd
     * @param int $numeroap
     */
    public static function setAddProdutoConfirma($request, $codiproProd, $numeroap)
    {
        $postVars = $request->getPostVars();

        $ap = Apartamentos::getApsByAtivos($numeroap)->fetchObject(Apartamentos::class);

        if (!$ap instanceof Apartamentos) {
            $request->getRouter()->redirect('/');
        }

        $produto = Produtos::getById($codiproProd)->fetchObject(Produtos::class);
        if (!$produto instanceof Produtos) {
            $request->getRouter()->redirect('/');
        }

        if ($postVars['quantidade'] <= 0) {
            $request->getRouter()->redirect('/');
        }

        $produto_ap  = new Produtos_aps;
        $produto_ap->codigo_ap  = $ap->codigo;
        $produto_ap->valor      = $produto->valor;
        $produto_ap->quantidade = $postVars['quantidade'];
        $produto_ap->codigo_pro = $produto->codigo;

        $produto_ap->cadastrar();

        $request->getRouter()->redirect("/add/$numeroap");
    }



    /**
     * EXCLUI UM PRODUTO DO AP
     *
     * @param Request $request
     */
    public static function setExcluir($request, $codprod, $numeroap)
    {
        $ap = Apartamentos::getApsByAtivos($numeroap)->fetchObject(Apartamentos::class);

        if (!$ap instanceof Apartamentos) {
            $request->getRouter()->redirect('/');
        }

        $pro = Produtos_aps::getById($ap->codigo, $codprod)->fetchObject(Produtos_aps::class);

        if (!$pro instanceof Produtos_aps) {
            $request->getRouter()->redirect('/');
        }

        $pro->delete();

        $request->getRouter()->redirect("/ap/$numeroap");
    }

    /**
     * PAGA UM AP GET
     *
     * @param Request $request
     * @param int $numeroap
     */
    public static function getPagar($request, $codigo)
    {

        $ap = Apartamentos::getApsByAtivosCodigo($codigo)->fetchObject(Apartamentos::class);
        if (!$ap instanceof Apartamentos) {
            $request->getRouter()->redirect('/');
        }

        $rota = $request->getQueryParams();



        $date1 = new DateTime($ap->data_entrada);
        $date2 = new DateTime($ap->data_saida);
        $difereca = $date1->diff($date2);

        $diarias = $difereca->days;
        $horas   = $difereca->h;
        $valorHoras = $ap->valor_total / 24;
        $valores = json_decode($ap->pagamentos);
        $total = ($ap->valor_total * $diarias) + ($horas * $valorHoras);

        $total_pago = json_decode($ap->pagamentos);

        $t = floatval(str_replace(",", ".", str_replace(".", "", $total_pago->dinheiro))
            + str_replace(",", ".", str_replace(".", "", $total_pago->pix))
            + str_replace(",", ".", str_replace(".", "", $total_pago->cartao)));


        $container = View::render('aps/pagar/index', [
            'numeroap' => $ap->numero_ap,
            'dinheiro' => $valores->dinheiro,
            'pix'      => $valores->pix,
            'cartao'   => $valores->cartao,
            'total'    => number_format($t, 2, ",", "."),
            'valor'    => number_format($total, 2, ",", "."),
            'resto'    => number_format($total - $t, 2, ",", "."),
            'rota'     => $rota['r'],
            'codigo'   => $ap->codigo,
            'rota_voltar' => $rota['r'] == 'ap' ? $ap->numero_ap : $ap->codigo
        ]);

        return Page::getPage($container, $request);
    }

    /**
     * POST PAGAR AP
     *
     * @param Request $request
     * @param int $numeroap
     */
    public static function postPagar($request, $codigo)
    {
        $postVars = $request->getPostVars();
        $queryParams = $request->getQueryParams();


        $user = User::getUserByEmail($_SESSION['hotelger']['email'])->fetchObject(User::class);

        $ap = Apartamentos::getApsByAtivosCodigo($codigo)->fetchObject(Apartamentos::class);

        if (!$ap instanceof Apartamentos) {
            $request->getRouter()->redirect('/');
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
        $tt = ($ap->valor_total * $diarias) + ($horas * $valorHoras);



        if ($din + $pix + $cart > $tt || $din + $pix + $cart + $ss1 > $tt) {
            $request->getRouter()->redirect("/pagar/$ap->codigo?status=pg00");
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

        $request->getRouter()->redirect('/pagar/' . $ap->codigo.'?r='.$queryParams['r']);
    }

    /**
     * GET CANCELAR
     *
     * @param Request $request
     * @param int $numeroap
     */
    public static function getCancelar($request, $codigo)
    {
        $ap = Apartamentos::getApsByAtivosCodigo($codigo)->fetchObject(Apartamentos::class);

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
    public static function postCancelar($request, $codigo)
    {
        $ap = Apartamentos::getApsByAtivosCodigo($codigo)->fetchObject(Apartamentos::class);

        if (!$ap instanceof Apartamentos) {
            $request->getRouter()->redirect('/');
        }

        $ap->status = 3;
        $ap->cancelar();

        $request->getRouter()->redirect('/');
    }


    public static function getHospedarAgora($request, $codigo)
    {
        $ap = Apartamentos::getApsByRervado($codigo)->fetchObject(Apartamentos::class);

        //EXIST RESERVA?
        if (!$ap instanceof Apartamentos) {
            $request->getRouter()->redirect('/');
        }
        $app = Apartamentos::getApsByAtivos($ap->numero_ap)->fetchObject(Apartamentos::class);

        if ($app instanceof Apartamentos) {
            $request->getRouter()->redirect("/reservas/$ap->codigo?status=501");
        }
        //EXIST ATIVO

        $ap->status = 1;
        $ap->setAtiveReservaToHospeda();

        $request->getRouter()->redirect('/?status=200');
    }

    public static function getHoraSaida($request, $codigo)
    {
        $ap = Apartamentos::getApEditeOcupado($codigo)->fetchObject(Apartamentos::class);

        $container = View::render("aps/edite_ap", [
            "numeroap"  => $ap->numero_ap,
            "reserva"   => date("d/m/Y H:i", strtotime($ap->data_reserva)) . "hrs",
            "entrada"   => date("d/m/Y H:i", strtotime($ap->data_entrada)) . "hrs",
            "saida"     => date("d/m/Y H:i", strtotime($ap->data_saida)) . "hrs",
            "numero-ap" => $ap->numero_ap . "hrs"
        ]);

        return Page::getPage($container, $request);
    }

    public static function setHoraSaida($request, $codigo)
    {
        $ap = Apartamentos::getApEditeOcupado($codigo)->fetchObject(Apartamentos::class);

        if (!$ap instanceof Apartamentos) {
            $request->getRouter()->redirect('/');
        }

        $postVars = $request->getPostVars();
        $numeroap = intval($postVars['numero-ap']);


        if ($ap->numero_ap != $numeroap) {
            $newAp = Apartamentos::getApsByAtivos($numeroap)->fetchObject(Apartamentos::class);

            if ($newAp instanceof Apartamentos) {
                $request->getRouter()->redirect("/hora-saida/$ap->codigo?status=500");
            }
            $ap->numero_ap  = $numeroap;
        }
        $ap->data_saida = $postVars['saida-data'] . ' ' . $postVars['saida-hora'] . ':00';

        $date1 = new DateTime($ap->data_entrada);
        $date2 = new DateTime($ap->data_saida);

        $diferenca = $date1->diff($date2);

        $ap->quantidade = $diferenca->days;

        $ap->atualizaDataSaida();

        $request->getRouter()->redirect("/ap/$ap->numero_ap");
    }


    public static function getHoraEntrada($request, $codigo)
    {
        $ap = Apartamentos::getApEditeOcupado($codigo)->fetchObject(Apartamentos::class);

        $container = View::render("aps/edite_ap_s", [
            "numeroap"  => $ap->numero_ap,
            "reserva"   => date("d/m/Y H:i", strtotime($ap->data_reserva)) . "hrs",
            "entrada"   => date("d/m/Y H:i", strtotime($ap->data_entrada)) . "hrs",
            "saida"     => date("d/m/Y H:i", strtotime($ap->data_saida)) . "hrs",
            "numero-ap" => $ap->numero_ap . "hrs"
        ]);

        return Page::getPage($container, $request);
    }

    public static function setHoraEntrada($request, $codigo)
    {
        $ap = Apartamentos::getApEditeOcupado($codigo)->fetchObject(Apartamentos::class);

        if (!$ap instanceof Apartamentos) {
            $request->getRouter()->redirect('/');
        }

        $postVars         = $request->getPostVars();
        $ap->data_entrada = $postVars['entrada-data'] . ' ' . $postVars['entrada-hora'] . ':00';

        $date1 = new DateTime($ap->data_entrada);
        $date2 = new DateTime($ap->data_saida);

        $diferenca = $date1->diff($date2);

        $ap->quantidade = $diferenca->days;

        $ap->atualizaDataEntrada();

        $request->getRouter()->redirect("/ap/$ap->numero_ap");
    }


    public static function getFinalizarAp($request, $codigo)
    {
        $ap = Apartamentos::getApsByAtivosID($codigo)->fetchObject(Apartamentos::class);

        if (!$ap instanceof Apartamentos) {
            $request->getRouter()->redirect("/?status=404");
        }

        $container = View::render('aps/finalizar', ['numeroap' => $ap->numero_ap]);

        return Page::getPage($container, $request);
    }

    public static function setFinalizarAp($request, $codigo)
    {
        $ap = Apartamentos::getApsByAtivosID($codigo)->fetchObject(Apartamentos::class);

        if (!$ap instanceof Apartamentos) {
            $request->getRouter()->redirect("/?status=404");
        }
        $ap->status = 2;

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


        if ($din + $pix + $cart < $totalAll)  $ap->pendente = 1;
        if ($din + $pix + $cart == $totalAll) $ap->pendente = 0;

        $ap->finalizar();

        $request->getRouter()->redirect("/?status=200");
    }
}
