<?php

use App\Controllers\Web\Receber;
use App\Http\Response;

$router->get('/receber', [
    'middlewares'=>['sessao'],
    function($request){
        return new Response(200, Receber::getIndex($request));
    }
]);

$router->get('/receber/{codigo}', [
    'middlewares'=>['sessao'],
    function($request, $codigo){
        return new Response(200, Receber::getEdit($request, $codigo));
    }
]);

$router->get('/receber-pagar/{codigo}', [
    'middlewares'=>['sessao'],
    function($request, $codigo){
        return new Response(200, Receber::getReceber($request, $codigo));
    }
]);

$router->post('/receber-pagar/{codigo}', [
    'middlewares'=>['sessao'],
    function($request, $codigo){
        return new Response(201, Receber::setReceber($request, $codigo));
    }
]);

$router->get('/receber-excluir/{codigo}', [
    'middlewares'=>['sessao'],
    function($request, $codigo){
        return new Response(200, Receber::getCancelar($request, $codigo));
    }
]);
$router->post('/receber-excluir/{codigo}', [
    'middlewares'=>['sessao'],
    function($request, $codigo){
        return new Response(201, Receber::setCancelar($request, $codigo));
    }
]);

$router->get('/finalizar-receber/{codigo}', [
    'middlewares'=>['sessao'],
    function($request, $codigo){
        return new Response(200, Receber::getFinalizar($request, $codigo));
    }
]);