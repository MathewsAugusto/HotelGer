<?php

use App\Controllers\Web\Produtos\Produtos;
use App\Http\Response;

$router->get('/produtos', [
    'middlewares' => ['sessao'],
    function ($request) {
        return new Response(200, Produtos::getProdutos($request));   
    }
]);

$router->get('/produtos/novo', [
    'middlewares' => ['sessao'],
    function ($request) {
        return new Response(200, Produtos::getNovo($request));   
    }
]);

$router->post('/produtos/novo', [
    'middlewares' => ['sessao'],
    function ($request) {
        return new Response(201, Produtos::setNovo($request));   
    }
]);

$router->get('/excluir/{codigo}/produto', [
    'middlewares' => ['sessao'],
    function ($request, $codigo) {
        return new Response(200, Produtos::getExcluir($request, $codigo));   
    }
]);

$router->post('/excluir/{codigo}/produto', [
    'middlewares' => ['sessao'],
    function ($request, $codigo) {
        return new Response(201, Produtos::setExcluir($request, $codigo));   
    }
]);

$router->get('/produtos/{codigo}/editar', [
    'middlewares' => ['sessao'],
    function ($request, $codigo) {
        return new Response(200, Produtos::getEditar($request, $codigo));   
    }
]);

$router->post('/produtos/{codigo}/editar', [
    'middlewares' => ['sessao'],
    function ($request, $codigo) {
        return new Response(201, Produtos::setEditar($request, $codigo));   
    }
]);
