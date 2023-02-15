<?php

namespace App\Http\Middleware;

use App\Session\Admin\Login as SessionAdminLogin;
use App\Session\Sessao;

class RequireAdminLogin
{

    /**
     * Executa o middleware
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, $next)
    {

        if(!Sessao::isLogged()){
            return $request->getRouter()->Redirect('/login');
        }
        
        return $next($request);

    }
}
