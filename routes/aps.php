<?php

use App\Controllers\Web\Aps\Aps;
use App\Controllers\Web\Main\Main;
use App\Http\Response;

$router->get('/novo/{numeroap}', [
    'middlewares' => [
        'sessao'
    ],
    function($request, $numeroap){
        return new Response(200, Aps::getNovo($request, $numeroap));
    }

]);

$router->get('/{numeroap}/hospedar/{tipo}', [
    'middlewares' => [
        'sessao'
    ],
    function($request, $numeroap, $tipo){
        return new Response(200, Aps::getHospedar($request, $numeroap, $tipo));
    }

]);

$router->post('/{numeroap}/hospedar/{tipo}', [
    'middlewares' => [
        'sessao'
    ],
    function($request, $numeroap, $tipo){
        return new Response(201, Aps::setHospedar($request, $numeroap, $tipo));
    }

]);

$router->get('/{numeroap}/reservar/{tipo}', [
    'middlewares' => [
        'sessao'
    ],
    function($request, $numeroap, $tipo){
        return new Response(200, Aps::getReservar($request, $numeroap, $tipo));
    }

]);

$router->post('/{numeroap}/reservar/{tipo}', [
    'middlewares' => [
        'sessao'
    ],
    function($request, $numeroap, $tipo){
        return new Response(201, Aps::setReservar($request, $numeroap, $tipo));
    }

]);

$router->get('/add/{numeroap}', [
    'middlewares' => [
        'sessao'
    ],
    function($request, $numeroap){
        return new Response(200, Aps::getListaAddProduto($request, $numeroap));
    }

]);



$router->get('/{numeroap}/{codprod}/add', [
    'middlewares' => [
        'sessao'
    ],
    function($request, $numeroap, $codprod){
        return new Response(200, Aps::getAddProdutoConfirma($request, $codprod,$numeroap));
    }

]);

$router->post('/{numeroap}/{codprod}/add', [
    'middlewares' => [
        'sessao'
    ],
    function($request, $numeroap, $codprod){
        return new Response(201, Aps::setAddProdutoConfirma($request, $codprod,$numeroap));
    }

]);

$router->get('/{numeroap}/{codprod}/excluir', [
    'middlewares' => [
        'sessao'
    ],
    function($request, $numeroap, $codprod){
        return new Response(200, Aps::setExcluir($request, $codprod,$numeroap));
    }

]);

$router->get('/pagar/{codigo}', [
    'middlewares' => [
        'sessao'
    ],
    function($request, $codigo){
        return new Response(200, Aps::getPagar($request, $codigo));
    }

]);

$router->post('/pagar/{codigo}', [
    'middlewares' => [
        'sessao'
    ],
    function($request, $codigo){
        return new Response(201, Aps::postPagar($request, $codigo));
    }

]);

$router->get('/cancelar/{codigo}', [
    'middlewares' => [
        'sessao'
    ],
    function($request, $codigo){
        return new Response(200, Aps::getCancelar($request, $codigo));
    }

]);

$router->post('/cancelar/{codigo}', [
    'middlewares' => [
        'sessao'
    ],
    function($request, $codigo){
        return new Response(201, Aps::postCancelar($request, $codigo));
    }

]);


$router->get('/hospedar-agora/{codigo}', [
    'middlewares' => [
        'sessao'
    ],
    function($request, $codigo){
        return new Response(200, Aps::getHospedarAgora($request, $codigo));
    }

]);

$router->get('/hora-saida/{codigo}', [
    'middlewares' => [
        'sessao'
    ],
    function($request, $codigo){
        return new Response(200, Aps::getHoraSaida($request, $codigo));
    }

]);

$router->post('/hora-saida/{codigo}', [
    'middlewares' => [
        'sessao'
    ],
    function($request, $codigo){
        return new Response(201, Aps::setHoraSaida($request, $codigo));
    }

]);


$router->get('/hora-entrada/{codigo}', [
    'middlewares' => [
        'sessao'
    ],
    function($request, $codigo){
        return new Response(200, Aps::getHoraEntrada($request, $codigo));
    }

]);

$router->post('/hora-entrada/{codigo}', [
    'middlewares' => [
        'sessao'
    ],
    function($request, $codigo){
        return new Response(201, Aps::setHoraEntrada($request, $codigo));
    }

]);

$router->get('/finalizar/{codigo}', [
    'middlewares' => [
        'sessao'
    ],
    function($request, $codigo){
        return new Response(200, Aps::getFinalizarAp($request, $codigo));
    }

]);

$router->post('/finalizar/{codigo}', [
    'middlewares' => [
        'sessao'
    ],
    function($request, $codigo){
        return new Response(200, Aps::setFinalizarAp($request, $codigo));
    }

]);




