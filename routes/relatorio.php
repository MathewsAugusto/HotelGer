<?php

use App\Controllers\Web\login\Login;
use App\Controllers\Web\Reports\Reports;
use App\Http\Response;

$router->get('/relatorio', [
    'middlewares' => [
        'login'
    ],
    function($request){
        return new Response(200, Reports::getIndex($request));
    }
]);






