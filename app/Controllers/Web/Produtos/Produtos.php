<?php

namespace App\Controllers\Web\Produtos;

use App\Controllers\Web\Page;
use App\Models\Produtos as ModelsProdutos;
use App\Utils\View;

class Produtos
{

    /**
     * retorna todos os produtos
     *
     * @param Request $request
     */
    public static function getProdutos($request)
    {
        $produtos = ModelsProdutos::getProdutos();
        $itens  = '';

        while ($pro = $produtos->fetchObject(ModelsProdutos::class)) {
            $itens .= View::render('produtos/item', [
                'nome'   => $pro->nome,
                'valor'  => number_format($pro->valor, 2, ",", "."),
                'status' => $pro->status == 1 ? 'Ativo' : 'Desativado',
                'codigo' => $pro->codigo
            ]);
        }


        $container = View::render('produtos/index', [
            'lista' => $itens
        ]);

        return Page::getPage($container);
    }

    /**
     * NOVO GET
     *
     * @param [type] $request
     * @param [type] $codigo
     * @return void
     */
    public static function getNovo($request)
    {

        $container = View::render('produtos/form', [
            'value_nome' => '',
            'value_valor' => ''
        ]);

        return Page::getPage($container);
    }

    /**
     * POST NOVO
     *
     * @param Request $request
     */
    public static function setNovo($request)
    {
        $postVars = $request->getPostVars();

        $produto = new ModelsProdutos;
        $produto->nome = $postVars['nome'];
        $produto->valor = $postVars['valor'];

        if (isset($postVars['status'])) {
            $produto->status = $postVars['status'] == 'on' ? 1 : 0;
        }


        $produto->cadastrar();

        $request->getRouter()->redirect('/produtos');
    }

    /**
     * GET EXCLUIR
     *
     * @param Request $request
     */
    public static function getExcluir($request, $codigo)
    {
        $pro =  ModelsProdutos::getById($codigo)->fetchObject(ModelsProdutos::class);

        if (!$pro instanceof ModelsProdutos) {
            $request->getRouter()->redirect('/produtos');
        }

        $container = View::render('produtos/excluir', [
            'descricao' => $pro->nome
        ]);

        return Page::getPage($container);
    }

    /**
     * POST EXCLUIR
     *
     * @param Request $request
     * @param int $codigo
     */
    public static function setExcluir($request, $codigo)
    {
        $pro =  ModelsProdutos::getById($codigo)->fetchObject(ModelsProdutos::class);
        if (!$pro instanceof ModelsProdutos) {
            $request->getRouter()->redirect('/produtos');
        }
        $pro->delete();
        $request->getRouter()->redirect('/produtos');
    }

    /**
     * EDITA GET
     *
     * @param Request $request
     * @param int $codigo
     */
    public static function getEditar($request, $codigo)
    {
        $pro =  ModelsProdutos::getById($codigo)->fetchObject(ModelsProdutos::class);
        if (!$pro instanceof ModelsProdutos) {
            $request->getRouter()->redirect('/produtos');
        }

        $container = View::render('produtos/form', [
            'value_nome' => $pro->nome,
            'value_valor' => number_format($pro->valor, 2, ",", "."),
            'checked'    => $pro->status == 1 ? 'checked' : ''
        ]);

        return Page::getPage($container);
    }

    /**
     * EDITA POST
     *
     * @param Request $request
     * @param int $codigo
     */
    public static function setEditar($request, $codigo)
    {
        $postVars = $request->getPostVars();

        $pro =  ModelsProdutos::getById($codigo)->fetchObject(ModelsProdutos::class);
        if (!$pro instanceof ModelsProdutos) {
            $request->getRouter()->redirect('/produtos');
        }

        $pro->nome  = $postVars['nome'];
        $pro->valor = $postVars['valor'];

        if (isset($postVars['status'])) {
            if ($postVars['status'] == 'on') {
                $pro->status = 1;
            }
        }else{
            $pro->status = 0;
        }

       $pro->update();
       $request->getRouter()->redirect('/produtos');
      
    }
}
