<?php

namespace App\Http\Middleware;

use Exception;

class Queue
{

    /**
     * Mapea do middlewares
     *
     * @var array
     */
    private static $map = [];

    /**
     * Mapeamento de middlewaes que é carregado em todas as rotas
     *
     * @var array
     */
    private static $default = [];


    /**
     * Fila de middlwares a serem executados
     * @var array
     */
    private $middlewares = [];

    /**
     * Funcão de execução do controlador
     *
     * @var Closure
     */
    private $controller;

    /**
     * Argumentos da função do controlador
     *
     * @var array
     */
    private $controlerArgs = [];

    /**
     * Método responsavel pela contrução
     *
     * @param array $middleware
     * @param Closure $controller
     * @param array $controlerArgs
     */
    public function __construct($middlewares, $controller, $controlerArgs)
    {
        $this->middlewares = array_merge(self::$default, $middlewares);
        $this->controller = $controller;
        $this->controlerArgs = $controlerArgs;
    }


    /**
     * Define o mapeamento de middlawares
     *
     * @param array $map
     */
    public static function setMap($map)
    {
        self::$map = $map;
    }

    /**
     * Undocumented function
     *
     */
    public static function setDefault($default)
    {
        self::$default = $default;
    }


    /**
     * Método responsavél por executar o próximo nível da fila de middleware
     *
     * @param Resquest $request
     * @return Response
     */
    public function next($request)
    {
        //VERIFICA SE A FILA ESTA VAZIA

        if (empty($this->middlewares)) return call_user_func_array($this->controller, $this->controlerArgs);

    
        $middleware = array_shift($this->middlewares);

        if (!isset(self::$map[$middleware])) {
            throw new Exception('Problemas ao executar a requisição', 500);
        }

        $queue = $this;

        $next = function ($request) use ($queue) {
            return $queue->next($request);
        };

        return (new self::$map[$middleware])->handle($request, $next);
    }
}
