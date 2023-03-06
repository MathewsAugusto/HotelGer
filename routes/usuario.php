<?php

use App\Controllers\Web\Usuario;
use App\Http\Response;

$router->get('/usuario/novo', [
    'middlewares' => [
        'sessao'
    ],
    function ($request) {
        return new Response(200, Usuario::getNovo($request));
    }

]);
$router->post('/usuario/novo', [
    'middlewares' => [
        'sessao'
    ],
    function ($request) {
        return new Response(201, Usuario::postNovo($request));
    }

]);

$router->get('/usuario/{codigo}/edita', [
    'middlewares' => [
        'sessao'
    ],
    function ($request, $codigo) {
        return new Response(200, Usuario::getEdita($request, $codigo));
    }

]);

$router->post('/usuario/{codigo}/edita', [
    'middlewares' => [
        'sessao'
    ],
    function ($request, $codigo) {
        return new Response(201, Usuario::postEdita($request, $codigo));
    }

]);

$router->get('/usuario/{codigo}/exclui', [
    'middlewares' => [
        'sessao'
    ],
    function ($request, $codigo) {
        return new Response(200, Usuario::getExlui($request, $codigo));
    }

]);