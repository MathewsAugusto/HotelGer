<?php

use App\Controllers\Web\Saidas;
use App\Http\Response;

$router->get('/saidas', [
    'middlewares' => [ 'sessao'],
    function ($request) {
        return new Response(200, Saidas::getIndex($request));
    }

]);

$router->get('/saidas/novo', [
    'middlewares' => [ 'sessao'],
    function ($request) {
        return new Response(200, Saidas::getNovo($request));
    }

]);

$router->post('/saidas/novo', [
    'middlewares' => [ 'sessao'],
    function ($request) {
        return new Response(201, Saidas::setNovo($request));
    }

]);

$router->get('/saidas/editar/{codigo}', [
    'middlewares' => [ 'sessao'],
    function ($request, $codigo) {
        return new Response(200, Saidas::getEdite($request, $codigo));
    }

]);

$router->post('/saidas/editar/{codigo}', [
    'middlewares' => [ 'sessao'],
    function ($request, $codigo) {
        return new Response(201, Saidas::postSaidaEdit($request, $codigo));
    }

]);

$router->get('/saidas/pagar/{codigo}', [
    'middlewares' => [ 'sessao'],
    function ($request, $codigo) {
        return new Response(200, Saidas::getPagar($request, $codigo));
    }

]);


$router->post('/saidas/pagar/{codigo}', [
    'middlewares' => [ 'sessao'],
    function ($request, $codigo) {
        return new Response(201, Saidas::postPagar($request, $codigo));
    }

]);

$router->get('/saidas/cancelar/{codigo}', [
    'middlewares' => [ 'sessao'],
    function ($request, $codigo) {
        return new Response(200, Saidas::getCancelar($request, $codigo));
    }

]);

$router->post('/saidas/cancelar/{codigo}', [
    'middlewares' => [ 'sessao'],
    function ($request, $codigo) {
        return new Response(201, Saidas::postCancelar($request, $codigo));
    }

]);