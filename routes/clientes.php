<?php

use App\Controllers\Web\Clientes;
use App\Http\Response;

$router->get('/clientes', [
    'middlewares' => [
        'sessao'
    ],
    function($request){
        return new Response(200, Clientes::getIndex($request));
    }

]);