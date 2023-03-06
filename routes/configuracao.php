<?php

use App\Controllers\Web\Aps\Aps;
use App\Controllers\Web\Configuracao;
use App\Http\Response;

$router->get('/configuracao', [
    'middlewares' => [
        'sessao'
    ],
    function($request){
        return new Response(200, Configuracao::getIndex($request));
    }

]);