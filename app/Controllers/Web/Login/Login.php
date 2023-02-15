<?php

namespace App\Controllers\Web\Login;

use App\Controllers\Web\Alert as WebAlert;
use App\Models\User;
use App\Session\Sessao;
use App\Utils\View;

class Login
{
    /**
     * LOGIN GET
     *
     * @return void
     */
    public static
    function getLogin($request)
    {
        return View::render('login/index', [
            'status'=> self::getStatus($request)
        ]);
    }
    ///LOGIN POST   
    public static
    function setLogin($request)
    {
        //VARIÁVEIS DIGITADAS NO LOGIN
        $postVars = $request->getPostVars();
        $email    = $postVars['email'];
        $senha    = $postVars['senha'];


        //VERIFICA SE O EMAIL EXISTE
        $userEmail = User::getUserByEmail($email)->fetchObject(User::class);

    
    
        //VERIFICA SE A CONSULTA RETORNA UMA INSTÂNCIA DE USUÁRIO
        if(!$userEmail instanceof User){
            $request->getRouter()->redirect('/login?status=404');   
        }

        //VERIFICA A SENHA
        if(!password_verify($senha, $userEmail->senha)){
            $request->getRouter()->redirect('/login?status=404');   
        }
        
        /* CRIA A SESSÃO DE LOGIN */
        Sessao::Login($email);
 
        $request->getRouter()->redirect('/');     
    }


    /**
     * Status 
     *
     * @param Request $request
     * @return string
     */
    public static function getStatus($request)
    {
        $queryParams = $request->getQueryParams();

        if (!isset($queryParams['status'])) return '';

        switch ($queryParams['status']) {    
            case '404':
                return WebAlert::getError("Usuário ou senha inválidos");
                break;
            
        }
    }

    /**
     * LOGOUT 
     *
     * @param Request $request
     */
    public static function setLogout($request)
    {
        Sessao::Logout();

        $request->getRouter()->redirect('/login');
        
    }

}
