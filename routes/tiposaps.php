<?php


use App\Controllers\Web\Tipos\Tipos;
use App\Http\Response;

$router->get('/tipos', [
    'middlewares' => [
        'sessao'
    ],
    function($request, $codigo){
        return new Response(200, Tipos::getTipos($request));
    }

]);

$router->get('/tipos/novo', [
    'middlewares' => [
        'sessao'
    ],
    function($request){
        return new Response(200, Tipos::getNovo($request));
    }

]);

$router->post('/tipos/novo', [
    'middlewares' => [
        'sessao'
    ],
    function($request){
        return new Response(201, Tipos::setNovo($request));
    }

]);

$router->get('/tipos/{codigo}/edite', [
    'middlewares' => [
        'sessao'
    ],
    function($request, $codigo){
        return new Response(200, Tipos::getEdite($request, $codigo));
    }

]);

$router->post('/tipos/{codigo}/edite', [
    'middlewares' => [
        'sessao'
    ],
    function($request, $codigo){
        return new Response(200, Tipos::setEdite($request, $codigo));
    }

]);



