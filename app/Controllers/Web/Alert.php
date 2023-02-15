<?php

namespace App\Controllers\Web;

use App\Utils\View;

class Alert
{

    /**
     * Mensagem de sucesso
     *
     * @param string $message
     * @return string
     */
    public static function getSucess($message)
    {

        return View::render(
            'alert/status',
            [
                'tipo' => 'success',
                'mensage' => $message
            ]
        );
    }
    /**
     * Mensagem de error
     *
     * @param string $message
     * @return string
     */
    public static function getError($message)
    {

        return View::render(
            'alert/status',
            [
                'tipo' => 'danger',
                'mensage' => $message
            ]
        );
    }
}
