<?php

namespace App\Models;

use WilliamCosta\DatabaseManager\Database;

class Reports
{


    public static function getReportSimple($dataI, $dataF)
    {
        return (new Database('`apartamentos` as ap LEFT JOIN produto_ap as prod ON ap.codigo = prod.codigo_ap'))
            ->select(
                "ap.status = 2 AND data_entrada > '$dataI 00:00:00'  AND data_entrada < '$dataF 23:59:59'",
                null,
                null,
                'ap.quantidade as quanti_ap, ap.valor_total, ap.tipo_pagamento, ap.codigo as codigo_ap, prod.valor, prod.quantidade'
            );
    }

    public static function getReportDetalhado($dataI, $dataF)
    {
        return (new Database('apartamentos'))
            ->select(
                "status = 2 AND data_entrada > '$dataI 00:00:00'  AND data_entrada < '$dataF 23:59:59'"
            );
    }


    public static function getReportSaidaProduto($dataI, $dataF)
    {
        return (new Database("produto_ap as prods JOIN apartamentos as ap ON prods.codigo_ap = ap.codigo JOIN produtos ON prods.codigo_pro = produtos.codigo"))
        ->select("ap.status = 2 AND ap.status = 2 AND ap.data_entrada > '$dataI 00:00:00'  AND ap.data_entrada < '$dataF 23:59:59' GROUP BY prods.codigo_pro, prods.valor", 'quantidade DESC', null,'produtos.nome, SUM(prods.quantidade) as quantidade, prods.valor, SUM(prods.valor * prods.quantidade) as total');
    }


}
