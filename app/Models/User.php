<?php

namespace App\Models;

use WilliamCosta\DatabaseManager\Database;

class User{

    public $email;
    public $senha;
    


    public static function getUser($where = null, $order = null, $limit = null, $fields = "*")
    {
        return (new Database('usuarios'))->select($where, $order, $limit, $fields);
    }

    /**
     * RETORNA O EMAIL REFERENTE
     *
     * @param String $email
     */
    public static function getUserByEmail($email)
    {
        return self::getUser("email = '$email'");
    }

}