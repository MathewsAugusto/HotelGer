<?php

namespace App\Controllers\Web;

use App\Models\User;
use App\Utils\View;

class Page
{

    public $modules = ['/home'=>"", '/reservas'=>"",
     '/receber'=>"",  '/saidas'=>"",  '/produtos'=>"",
     '/tipos'=>"",  '/relatorios'=>"",  '/clientes'=>"",  '/configuracao'=>""];

    public static function getPage($container, $request, $menu = true)
    {
        $currentModule = $request->getQueryParams();
     
       
       if(isset($currentModule['route'])){

        $modules[$currentModule['route']] = "menu-select";

       }else{
        $modules['/home'] = "menu-select";
       }
     
        return View::render(
            'page/index',
            [
                'menu'      => $menu == true ? Page::rendeMenu($modules) : "",
                'container' => $container,
                'status'    => self::getStatus($request),
                'footer'    => self::footer()
            ]
        );
    }

    public static function rendeMenu($datas = [])
    {
        return View::render('menu/index', $datas);
    }

   

    public static function footer()
    {
        $email = $_SESSION['hotelger']['email'];

        $user = User::getUserByEmail($email)->fetchObject(User::class);

        return View::render('page/footer', [
            'usuario' => $user->nome
        ]);
    }

    public static function getStatus($request)
    {

        $queryParams = $request->getQueryParams();
        if (!isset($queryParams['status'])) return '';
        $status = $queryParams['status'];
        $view = '';

        switch ($status) {

            case '200':
                $view = Alert::getSucess("Ação realizada com sucesso");
                break;
            case '203':
                $view = Alert::getSucess("Deletado com Suceso 🚮");
                break;
            case '401':
                $view = Alert::getError("Acão negada");
                break;
            case '404':
                $view = Alert::getError("Acão não encontrada");
                break;
            case '405':
                $view = Alert::getError("Acão bloqueada");
                break;
            case '406':
                $view = Alert::getError("As senhas não são iguais");
                break;
            case '407':
                $view = Alert::getError("Insira uma senha diferente");
                break;
            case '500':
                $view = Alert::getError("Ap está ocupado, escolha outro Ap");
                break;
            case '501':
                $view = Alert::getError("O AP já está ocupado, reserva não aceita");
                break;
            case 'pg00':
                $view = Alert::getError("O valor de pagamento não pode ser maior que o valor total do Ap");
                break;
        }
        return $view;
    }

   
}
