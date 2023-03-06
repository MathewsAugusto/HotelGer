<?php

namespace App\Controllers\Web;

use App\Models\User;
use App\Utils\View;

class Usuario
{

    public static function getNovo($request)
    {
        $container = View::render('usuario/novo', [
            'nome' => '',
            'email' => '',
            'tipo'  => 'Novo Usuário'
        ]);
        return Page::getPage($container, $request);
    }

    public static function postNovo($request)
    {

        $postVars = $request->getPostVars();
        $senha1 = $postVars['senha1'];
        $senha2 = $postVars['senha2'];

        if ($senha1 != $senha2) {
            $request->getRouter()->redirect('/usuario/novo?status=406');
        }



        $user = new User;
        $user->nome = $postVars['nome'];
        $user->senha = $senha1;
        $user->email = $postVars['email'];
        $user->perm_ap = isset($postVars['perm_ap'])           == 1 ? 1 : 0;
        $user->perm_produtos = isset($postVars['perm_produtos'])     == 1 ? 1 : 0;
        $user->perm_tipo = isset($postVars['perm_tipo'])         == 1 ? 1 : 0;
        $user->perm_relatorio = isset($postVars['perm_relatorio'])    == 1 ? 1 : 0;
        $user->perm_clientes = isset($postVars['perm_clientes'])     == 1 ? 1 : 0;
        $user->perm_config = isset($postVars['perm_configuracao']) == 1 ? 1 : 0;
        $user->perm_edituser = isset($postVars['perm_useredit'])     == 1 ? 1 : 0;

        $user->insert();

        $request->getRouter()->redirect('/configuracao?status=200');
    }

    public static function getEdita($request, $codigo)
    {


        $user = User::getUserById($codigo)->fetchObject(User::class);

        if (!$user instanceof User) {
            $request->getRouter()->redirect('/configuracao?status=404');
        }


        $container = View::render('usuario/novo', [
            'tipo' =>'Editando Usuário',
            'nome' => $user->nome,
            'email' => $user->email,
            'perm_ap'=>$user->perm_ap == 1 ? 'checked' : '',
            'perm_produtos'=>$user->perm_produtos == 1 ? 'checked' : '',
            'perm_tipos'=>$user->perm_tipo == 1 ? 'checked' : '',
            'perm_relatorios'=>$user->perm_relatorio == 1 ? 'checked' : '',
            'perm_clientes'=>$user->perm_clientes == 1 ? 'checked' : '',
            'perm_configuracao'=>$user->perm_config == 1 ? 'checked' : '',
            'perm_useredit'=>$user->perm_useredit == 1 ? 'checked' : '',

        ]);

        return Page::getPage($container, $request);
    }

    public static function postEdita($request, $codigo)
    {
        $postVars = $request->getPostVars();
        $senha1 = $postVars['senha1'];
        $senha2 = $postVars['senha2'];

        if ($senha1 != $senha2) {
            $request->getRouter()->redirect("/usuario/$codigo/edita?status=406");
        }

        $user = User::getUserById($codigo)->fetchObject(User::class);

        if (!$user instanceof User) {
            $request->getRouter()->redirect('/configuracao?status=404');
        }

        $user->nome = $postVars['nome'];
        $user->senha = $senha1;
        $user->email = $postVars['email'];
        $user->perm_ap = isset($postVars['perm_ap'])           == 1 ? 1 : 0;
        $user->perm_produtos = isset($postVars['perm_produtos'])     == 1 ? 1 : 0;
        $user->perm_tipo = isset($postVars['perm_tipo'])         == 1 ? 1 : 0;
        $user->perm_relatorio = isset($postVars['perm_relatorio'])    == 1 ? 1 : 0;
        $user->perm_clientes = isset($postVars['perm_clientes'])     == 1 ? 1 : 0;
        $user->perm_config = isset($postVars['perm_configuracao']) == 1 ? 1 : 0;
        $user->perm_edituser = isset($postVars['perm_useredit'])     == 1 ? 1 : 0;

        $user->update();
        
        $request->getRouter()->redirect('/configuracao?status=200');

    }

    public static function getExlui($request, $codigo)
    {
       
        $user = User::getUserById($codigo)->fetchObject(User::class);

        if(!$user instanceof User){
        $request->getRouter()->redirect('/configuracao?status=404');
        }

        $user->delete();

        $request->getRouter()->redirect('/configuracao?status=203');
        
    }
}
