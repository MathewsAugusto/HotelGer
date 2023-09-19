<?php

namespace App\Models;

use WilliamCosta\DatabaseManager\Database;

class User
{
    public $codigo;
    public $nome;
    public $email;
    public $senha;
    public $perm_ap = 0;
    public $perm_produtos = 0;
    public $perm_tipo = 0;
    public $perm_relatorio = 0;
    public $perm_clientes = 0;
    public $perm_config = 0;
    public $perm_edituser = 0;

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

    public static function getUserById($codigo)
    {
        return self::getUser("codigo = '$codigo'");
    }

    public function updateSenha()
    {
        return (new Database('usuarios'))->update("codigo = $this->codigo", [
            'senha' => password_hash($this->senha, PASSWORD_DEFAULT)
        ]);
    }

    public function insert()
    {
        return (new Database('usuarios'))->insert([
            'nome' => $this->nome,
            'senha' => password_hash($this->senha,PASSWORD_DEFAULT),
            'email' => $this->email,
            'perm_ap' =>$this->perm_ap,
            'perm_produtos' =>$this->perm_produtos,
            'perm_tipo' =>$this->perm_tipo,
            'perm_relatorio' =>$this->perm_relatorio,
            'perm_clientes' =>$this->perm_clientes,
            'perm_config' =>$this->perm_config,
            'perm_useredit' =>$this->perm_edituser,
            
        ]);
    }


    public function update()
    {
        return (new Database('usuarios'))->update("codigo = $this->codigo",[
            'nome' => $this->nome,
            'senha' => password_hash($this->senha,PASSWORD_DEFAULT),
            'email' => $this->email,
            'perm_ap' =>$this->perm_ap,
            'perm_produtos' =>$this->perm_produtos,
            'perm_tipo' =>$this->perm_tipo,
            'perm_relatorio' =>$this->perm_relatorio,
            'perm_clientes' =>$this->perm_clientes,
            'perm_config' =>$this->perm_config,
            'perm_useredit' =>$this->perm_edituser,
            
        ]);
    }
    public function delete()
    {
       return (new Database('usuarios'))->delete('codigo = '.$this->codigo);
    }

}
