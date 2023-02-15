<?php

namespace App\Controllers\Web\Tipos;

use App\Controllers\Web\Page;
use App\Models\Tipo_quartos;
use App\Utils\View;

class Tipos
{


    /**RETORNA OS TIPOS QUE EXISTEM  */
    public static function getTipos($request)
    {
        $tipos_q = Tipo_quartos::getAtivos();
        $lista = '';
        while ($tipo = $tipos_q->fetchObject(Tipo_quartos::class)) {
            $lista .= View::render('tipos/item', [
                'descricao' =>$tipo->descricao,
                'valor'  => $tipo->valor,
                'maximo' => $tipo->max,
                'codigo' => $tipo->codigo
                
            ]);
        }

        $content = View::render('tipos/index', [
            'lista' => $lista
        ]);

        return Page::getPage($content);
    }

    /**
     * GET form para novo Tipo de quarto
     *
     * @param Request $request
     */
    public static function getNovo($request)
    {
        $form = View::render('tipos/form', [
            'descricao'=>"",
            'valor'    =>"",
            'max'      =>""
        ]);
        
        return Page::getPage($form); 

        
    }


    /**
     * POST NOVO
     *
     * @param Request $request
     */
    public static function setNovo($request)
    {
        $postVars = $request->getPostVars();

        $tipo = new Tipo_quartos;
        $tipo->descricao = $postVars['descricao'];
        $tipo->valor     = str_replace(",", ".",$postVars['valor']);
        $tipo->max       = $postVars['max'];
        $tipo->status    = 1;
        $tipo->insert();

        $request->getRouter()->redirect('/tipos');
        
    }

     /**
     * GET EDITE
     *
     * @param Request $request
     * @param int $codigo
     * 
     */
    public static function getEdite($request, $codigo)
    {
        $tipo = Tipo_quartos::getByCodigo($codigo)->fetchObject(Tipo_quartos::class);
        if(!$tipo instanceof Tipo_quartos){
            return Page::getPage('<h3>Não existe</h3>');
        }

        $content = View::render('tipos/form', [
            'descricao'=>$tipo->descricao,
            'valor'    =>number_format($tipo->valor, 2, ",", "."),
            'max'      =>$tipo->max         

        ]);

        return Page::getPage($content);
        
    }

    /**
     * POST EDIT
     *
     * @param Request $request
     * @param int $codigo
     */
    public static function setEdite($request, $codigo)
    {
        $tipo =  Tipo_quartos::getByCodigo($codigo)->fetchObject(Tipo_quartos::class);
        if(!$tipo instanceof Tipo_quartos){
            return Page::getPage('<h3>Não existe</h3>');
        }
        $postVars = $request->getPostVars();
        $tipo->descricao = $postVars['descricao'];
        $tipo->valor     = str_replace(",", ".",$postVars['valor']);
        $tipo->max       = $postVars['max'];
        $tipo->status    = 1;
        $tipo->update();

        $request->getRouter()->redirect('/tipos');

    }

}
