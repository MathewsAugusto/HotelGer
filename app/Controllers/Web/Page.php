<?php

namespace App\Controllers\Web;

use App\Utils\View;

class Page
{

    public static function getPage($container){

        return View::render(
            'page/index',
            [
                'menu'      => Page::rendeMenu(), 
                'container' => $container
            ]
        );
    }

    public static function rendeMenu()
    {
       return View::render('menu/index',[]);
    }

}