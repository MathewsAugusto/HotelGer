<?php

namespace App\Models;

use WilliamCosta\DatabaseManager\Database;

class Reports
{


    public static function getReportSimple($dataI, $dataF)
    {
        return (new Database('`apartamentos`'))
            ->select(
                "data_pag > '$dataI 00:00:00'  AND data_pag < '$dataF 23:59:59'",
                null,
                null
        
            );
    }

    public static function getReportDetalhado($dataI, $dataF)
    {
        return (new Database('apartamentos'))
            ->select(
                "data_pag > '$dataI 00:00:00'  AND data_pag < '$dataF 23:59:59'"
            );
    }


    public static function getReportSaidaProduto($dataI, $dataF)
    {
        return (new Database("produto_ap as prods JOIN apartamentos as ap ON prods.codigo_ap = ap.codigo JOIN produtos ON prods.codigo_pro = produtos.codigo"))
        ->select("ap.data_pag > '$dataI 00:00:00'  AND ap.data_pag < '$dataF 23:59:59' GROUP BY prods.codigo_pro, prods.valor", 'quantidade DESC', null,'produtos.nome, SUM(prods.quantidade) as quantidade, prods.valor, SUM(prods.valor * prods.quantidade) as total');
    }


}
