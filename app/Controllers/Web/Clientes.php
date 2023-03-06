<?php

namespace App\Controllers\Web;

use App\Models\Cliente;
use App\Utils\View;

class Clientes
{


    public static function getIndex($request)
    {

        $clientes = Cliente::getCliente();
        $viewCliente = '';

        while ($cli = $clientes->fetchObject(Cliente::class)) {
            $viewCliente .= View::render('client/item', [
                'nome' =>$cli->nome,
                'cpf'  =>$cli->cpf,
                'celular' => $cli->celular
            ]);
        }
        $container = View::render('client/index', ['itens'=>$viewCliente]);

        return Page::getPage($container, $request);
    }
}
