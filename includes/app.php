<?php
require __DIR__.'/../vendor/autoload.php';
session_start();
use App\Utils\View;
use WilliamCosta\DotEnv\Environment;
use WilliamCosta\DatabaseManager\Database;
use App\Http\Middleware\Queue as MiddlewareQueue;

Environment::load(__DIR__.'/../');
define('URL', getenv('URL'));
View::int(['URL'=>URL]);

Database::config(
    getenv('DB_HOST'),
    getenv('DB_NAME'),
    getenv('DB_USER'),
    getenv('DB_PASS'),
    getenv('DB_PORT')
);

//define o mapeamento de middlewares
MiddlewareQueue::setMap([
    'sessao'            =>\App\Http\Middleware\Sessao::class,
    'login'            =>\App\Http\Middleware\RequireAdminLogin::class,
    'logout'            =>\App\Http\Middleware\RequireAdminLogout::class,
    ]); 
    

