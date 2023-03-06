<?php

use App\Controllers\Web\Recibo\Recibo;
use App\Http\Response;

$router->get('/recibo/{codigo}', [
    'middlewares' => [
        'sessao'
    ],
    function($request, $codigo){
        return new Response(200, Recibo::getRecibo($request, $codigo));
    }

]);