<?php

namespace App\Http\Middleware;

use App\Http\Response;
use App\Controllers\Admin\Maintenance as mai;

class Maintenance{

    /**
     * Executa os middlewares
     *
     * @param Resquest $request
     * @param Closure $next
     * @return Resoponse
     */
    public function handle($request, $next)
    {   
        //ESTADO DE MANUNTENÇÃO DA PAGINA
       if(getenv('MAINTENANCE')=='true'){

        return new Response(200, mai::getHome());
      
        //   throw new Exception("Página em manutenção", 200);
       }
       //RETORNA O PROXIMO NIVEL DO MIDDLEWARE
       return $next($request);
    }

}