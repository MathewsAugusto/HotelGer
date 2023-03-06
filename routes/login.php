<?php

use App\Controllers\Web\login\Login;
use App\Http\Response;

$router->get('/login', [
    'middlewares' => [
        'logout'
    ],
    function($request){
        return new Response(200, Login::getLogin($request));
    }
]);
$router->post('/login', [
    'middlewares' => [
       'logout'
    ],
    function($request){
       return new Response(201, Login::setLogin($request));
    }
]);

$router->post('/login/senha', [
    'middlewares' => [
       'login'
    ],
    function($request){
       return new Response(201, Login::postSenha($request));
    }
]);


$router->get('/logout', [
    'middlewares' => [
       'login'
    ],
    function($request){
       return new Response(201, Login::setLogout($request));
    }
]);






