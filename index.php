<?php

require __DIR__.'/includes/app.php';

use App\Http\Router;

$router = new Router(URL);

include __DIR__.'/routes/main.php';
include __DIR__.'/routes/login.php';
include __DIR__.'/routes/aps.php';
include __DIR__.'/routes/tiposaps.php';
include __DIR__.'/routes/produtos.php';
include __DIR__.'/routes/relatorio.php';
include __DIR__.'/routes/recibo.php';
include __DIR__.'/routes/configuracao.php';
include __DIR__.'/routes/usuario.php';
include __DIR__.'/routes/clientes.php';
include __DIR__.'/routes/reservas.php';


$router->run()->sendResponse();