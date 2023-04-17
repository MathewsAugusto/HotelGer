<?php

use App\Controllers\Web\Reservas;
use App\Http\Response;

$router->get('/reservas', [
    'middlewares' => [
        'sessao'
    ],
    function($request){
        return new Response(200, Reservas::getIndex($request));
    }

]);
$router->get('/reservas/{codigo}', [
    'middlewares' => [
        'sessao'
    ],
    function($request, $codigo){
        return new Response(200, Reservas::getReservas($request, $codigo));
    }

]);



$router->get('/res/lista/{ap}', [
    'middlewares' => [
        'sessao'
    ],
    function($request, $ap){
        return new Response(200, Reservas::getListaReserAPs($request, $ap));
    }

]);

$router->get('/reser/excluir/{codigo}', [
    'middlewares' => [
        'sessao'
    ],
    function($request, $codigo){
        return new Response(200, Reservas::getExcluiReserva($request, $codigo));
    }

]);