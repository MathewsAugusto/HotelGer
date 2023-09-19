<?php

namespace App\Controllers\Web;

use App\Models\Saidas as ModelsSaidas;
use App\Models\User;
use App\Utils\View;
use DateTime;

class Saidas
{


    public static function getIndex($request)
    {
        $saidas = ModelsSaidas::getSaidasLancadas();
        $itens = "";


        while ($sa = $saidas->fetchObject(ModelsSaidas::class)) {
            date_default_timezone_set('America/Sao_Paulo');

            $status = $sa->data_vencimento > date('Y-m-d') ? "A vencer" : "Vencido";

            $itens .= View::render('saidas/item', [
                'codigo'     => $sa->codigo,
                'descricao'  => $sa->descricao,
                'valor'      => number_format($sa->valor, 2, ",", "."),
                'status'     => $status,
                'vencimento' => date("d/m/Y ", strtotime($sa->data_vencimento)),
                'cor_sts'    => $sa->data_vencimento > date('Y-m-d') ? "" : 'style="background: red;"'
            ]);
        }

        $table = $itens == "" ? "" : View::render('saidas/table', ["itens" => $itens]);

        $content =  View::render('saidas/index', ['table' => $table]);


        return Page::getPage($content, $request);
    }

    public static function getNovo($request)
    {
        $content =  View::render('saidas/novo', [
            'descricao'  => "",
            'valor'      => "",
            'vencimento' => "",
            'tipo'       => "Nova Saída"
        ]);
        return Page::getPage($content, $request);
    }

    /**
     * Insere uma saída
     *
     * @param Request $request
     */
    public static function setNovo($request)
    {

        date_default_timezone_set('America/Sao_Paulo');

        $user = User::getUserByEmail($_SESSION['hotelger']['email'])->fetchObject(User::class);

        $postVars = $request->getPostVars();

        $saida = new ModelsSaidas;
        $saida->descricao = $postVars['descricao'];
        $saida->data_create = date('Y-m-d h:i:s');
        $saida->data_vencimento = $postVars['data-vencimento'];
        $saida->user_create = $user->codigo;
        $saida->status = 0;
        $saida->valor = $postVars['valor'];

        $saida->novo();


        $request->getRouter()->redirect('/saidas');
    }

    public static function getEdite($request, $codigo)
    {
        $saida = ModelsSaidas::getSaidaByCodigo($codigo);

        if (!$saida instanceof ModelsSaidas) {
            $request->getRouter()->redirect('/saidas?status=404');
        }

        $content = View::render(
            'saidas/novo',
            [
                'tipo'       => "Editando",
                'descricao'  => $saida->descricao,
                'valor'      => $saida->valor,
                'vencimento' => $saida->data_vencimento
            ]
        );


        return Page::getPage($content, $request);
    }


    public static function postSaidaEdit($request, $codigo)
    {

        $saida = ModelsSaidas::getSaidaByCodigo($codigo);

        if (!$saida instanceof ModelsSaidas) {
            $request->getRouter()->redirect('/saidas?status=404');
        }

        $postVars = $request->getPostVars();

        $saida->descricao = $postVars['descricao'];
        $saida->valor     = $postVars['valor'];
        $saida->data_vencimento = $postVars['data-vencimento'];
        $saida->update();
        $request->getRouter()->redirect('/saidas?status=200');
    }


    public static function getPagar($request, $codigo)
    {

        $saida = ModelsSaidas::getSaidaByCodigo($codigo);

        if (!$saida instanceof ModelsSaidas) {
            $request->getRouter()->redirect('/saidas?status=404');
        }


        $container = View::render('/saidas/pagar', [
            'descricao' => $saida->descricao,
            'valor'     => number_format($saida->valor, 2, ",", "."),
            'vencimento' => date('d-M-Y', strtotime($saida->data_vencimento))
        ]);
        return Page::getPage($container, $request);
    }



    public static function postPagar($request, $codigo)
    {
        $saida = ModelsSaidas::getSaidaByCodigo($codigo);
        $postVars = $request->getPostVars();
        $user = User::getUserByEmail($_SESSION['hotelger']['email'])->fetchObject(User::class);

        if (!$saida instanceof ModelsSaidas) {
            $request->getRouter()->redirect('/saidas?status=404');
        }

        date_default_timezone_set("America/Sao_Paulo");
        $saida->data_pagamento = date('Y-m-d H:i:s');
        $saida->status = 1;
        $saida->tipo_pagamento = $postVars['pagamento'];
        $saida->user_pago = $user->codigo;

        $saida->pagar();

        $request->getRouter()->redirect('/saidas?status=200');
    }



    public static function getCancelar($request, $codigo)
    {

        $saida = ModelsSaidas::getCancelar($codigo);
                
        if (!$saida instanceof ModelsSaidas) {
            $request->getRouter()->redirect('/saidas?status=404');
        }
        
        $container = View::render('saidas/delete', [
            'descricao'=>$saida->descricao,
            'valor'    =>number_format($saida->valor, 2, ",", "."),
            'vencimento'=> date('d-m-Y', strtotime($saida->data_vencimento))
        ]);


        return Page::getPage($container, $request);
    }

    public static function postCancelar($request, $codigo)
    {
        $saida = ModelsSaidas::getCancelar($codigo);
        $user = User::getUserByEmail($_SESSION['hotelger']['email'])->fetchObject(User::class);

        if (!$saida instanceof ModelsSaidas) {
            $request->getRouter()->redirect('/saidas?status=404');
        }

        date_default_timezone_set("America/Sao_Paulo");
        $saida->data_cancel = date('Y-m-d H:i:s');
        $saida->status = 2;
        $saida->user_cancel   = $user->codigo;

        $saida->cancelar();

        $request->getRouter()->redirect('/saidas?status=200');
    }

}
