<?php

use App\Controllers\Web\Main\Main;
use App\Http\Response;

$router->get('/', [
    'middlewares' => [
        'sessao'
    ],
    function ($request) {
        return new Response(200, Main::getQuartosAps($request));
    }

]);

$router->get('/ap/{codigo}', [
    'middlewares' => [
        'sessao'
    ],
    function ($request, $codigo) {
        return new Response(200, Main::getQuartosDetalhes($request, $codigo));
    }

]);

$router->get('/diaria/{type}/{codigo}', [
    'middlewares' => [
        'sessao'
    ],
    function ($request, $type, $codigo) {
        return new Response(200, Main::setQuantidade($request, $type, $codigo));
    }

]);
