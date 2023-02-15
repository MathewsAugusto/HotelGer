<?php

namespace App\Http\Middleware;

use App\Http\Response;
use App\Controllers\Admin\Home;
use App\Models\Empresa;

class Licenca{

    /**
     * Executa os middlewares
     *
     * @param Resquest $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, $next)
    {  
        $empresa = Empresa::getEmpresa($_SESSION['admin']['usuario']['cnpj']);

        //LICENÇA CHAMA SEMPRE HOME SE ESTIVER COM A LICENÇA VENCIDA
        if($empresa->LICENCA == '0'){ 
          return new Response(200, Home::getHome());
        }
         
       //RETORNA O PROXIMO NIVEL DO MIDDLEWARE
       return $next($request);
    }

}