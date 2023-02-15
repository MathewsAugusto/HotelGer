<?php

namespace App\Session;

class Sessao
{
    /**
     * INICIALIZA A SESSION
     */
    public static function init()
    {
    
        
        //ATIVA A SESSÃO CASO ESTEJA DESATIVADA
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!session_id()) {
            session_start();
        }
    }


    /**
     * Cria o login do user
     *
     * @param User $obUser
     * @return boolean
     */
    public static function Login($email)
    {
        //inicia a sessção
        self::init();

        $_SESSION['hotelger']['email'] = $email;
                
              
        //SUCESSO
        return true;
    }
    /**
     * VERIFICA SE ESTÁ LOGADO
     *
     * @return boolean
     */
    public static function isLogged()
    {
        //START SESSION
        self::init();
        

        //VERIFICA SE TEM sessao 
        return isset($_SESSION['hotelger']);
    }
    /**
     * EXECUTA LOGOUT
     *
     * @return boolean
     */
    public static function Logout()
    {
        //INICIA A SESSION
        self::init();

        //DESLOGA O USER, EXCLUINDO O ARRAY
        unset($_SESSION['hotelger']);

        return true; //SUCESO
    }
}
