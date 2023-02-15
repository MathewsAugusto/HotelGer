<?php

require __DIR__.'/includes/app.php';

use App\Http\Router;

$router = new Router(URL);

include __DIR__.'/routes/main.php';
include __DIR__.'/routes/login.php';
include __DIR__.'/routes/aps.php';
include __DIR__.'/routes/tiposaps.php';
include __DIR__.'/routes/produtos.php';


$router->run()->sendResponse();