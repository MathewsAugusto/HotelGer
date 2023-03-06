<?php

namespace App\Controllers\Web;

use App\Models\User;
use App\Utils\View;

class Configuracao
{

    public static function getIndex($request)
    {

        $email = $_SESSION['hotelger']['email'];

        $user = User::getUserByEmail($email)->fetchObject(User::class);
       
        $users = User::getUser();
        $usersView = '';
        
        while($u = $users->fetchObject(User::class)){
            $usersView .= View::render('config/users/item',[
                'codigo'=> $u->codigo,
                'nome'  => $u->nome,
                'email' => $u->email
            ]);
        }

        $container = View::render('config/index', [
            'listaUsuarios'=>$user->perm_useredit == 1 ? View::render('config/users/table',['itens'=>$usersView]) : '',
            'btn-add' => $user->perm_useredit == 1 ? View::render('config/btn',[]) : ''
        ]);

        return Page::getPage($container, $request);

    }

}
