<?php

namespace App\Http\Middleware;

use App\Models\User;

class Permissoes
{
    /**
     * Executa os middlewares
     *
     * @param Resquest $request
     * @param Closure $next
     * @return Resoponse
     */
    public function handle($request, $next)
    {

        $email = isset($_SESSION['hotelger']['email']) ? $_SESSION['hotelger']['email'] : '';
        $user = User::getUserByEmail($email)->fetchObject(User::class);


        $modulo = explode('/', $request->getUri());

        $mod = $modulo[2];

        switch ($mod) {
            case 'produtos':
                if ($user->perm_produtos == 0) {
                    $request->getRouter()->redirect('/?status=401');
                }
                break;
            case 'tipos':
                if ($user->perm_tipos == 0) {
                    $request->getRouter()->redirect('/?status=401');
                }
                break;
            case 'relatorio':
                if ($user->perm_relatorio == 0) {
                    $request->getRouter()->redirect('/?status=401');
                }
                break;
            case 'clientes':
                if ($user->perm_clientes == 0) {
                    $request->getRouter()->redirect('/?status=401');
                }
                break;
            case 'configuracao':
                if ($user->perm_config == 0) {
                    $request->getRouter()->redirect('/?status=401');
                }
                break;
          
        }

        //RETORNA O PROXIMO NIVEL DO MIDDLEWARE
        return $next($request);
    }
}
