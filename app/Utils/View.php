<?php

namespace App\Utils;

class View
{
    /**
     * @var array
     */
    private static $vars = [];
    /**
     * Método responsavél por definir dados iniciais da classe
     * @param array $vars
     */
    public static function int($vars = [])
    {
        self::$vars = $vars;
        
    }


    //retorna o conteudo da view
    private static function getContentView($view)
    {
        $file = __DIR__ . '/../../src/view/' . $view . '.html';
        return file_exists($file) ? file_get_contents($file) : '';
    }

    // retorna o conteudo renderizado de uma view

    public static function render($view, $vars = [])
    {
        $contentView = self::getContentView($view);//conteudo da view

        //unir duas variaveis de array
        $vars = array_merge(self::$vars, $vars);

        $keys = array_keys($vars);
        $keys = array_map(function ($item) {
            return '{{' . $item . '}}';
        }, $keys);

        return str_replace($keys, array_values($vars), $contentView);
    }
}
