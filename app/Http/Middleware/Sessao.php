<?php

namespace App\Http\Middleware;

use App\Session\Sessao as SessionSessao;

class Sessao{
 /**
     * Executa os middlewares
     *
     * @param Resquest $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, $next)
    {
       if(!SessionSessao::isLogged()){

        return $request->getRouter()->Redirect('/login');

       }

       return $next($request);
    }

}