<?php

namespace App\Models;

use WilliamCosta\DatabaseManager\Database;

class Reports
{


    public static function getReportSimple($dataI, $dataF)
    {
        return (new Database('log_pagamentos as pag JOIN  apartamentos as ap ON ap.codigo = pag.codigo_ap'))
            ->select(
                "pag.data > '$dataI 00:00:00'  AND pag.data < '$dataF 23:59:59' AND ap.status < 3",
                null,
                null,
                'ap.codigo, ap.data_entrada, ap.data_saida, pag.valor as valor_total, pag.tipo as tipo_pagamento'
            );
    }

    public static function getReportDetalhado($dataI, $dataF)
    {
        return (new Database('log_pagamentos as pag JOIN apartamentos as ap  ON ap.codigo = pag.codigo_ap'))
            ->select(
                "pag.data  > '$dataI 00:00:00'  AND pag.data < '$dataF 23:59:59' AND ap.status < 3",
                null,
                null,
                'ap.codigo,ap.numero_ap, ap.data_reserva,ap.data_entrada, ap.data_saida, pag.valor as valor_pago, ap.valor_total as valor_total, pag.tipo as tipo_pagamento'
            );
    }


    public static function getReportSaidaProduto($dataI, $dataF)
    {
        return (new Database("produto_ap as prods JOIN apartamentos as ap ON prods.codigo_ap = ap.codigo JOIN produtos ON prods.codigo_pro = produtos.codigo"))
            ->select("ap.status < 3 AND ap.data_pag > '$dataI 00:00:00'  AND ap.data_pag < '$dataF 23:59:59' GROUP BY prods.codigo_pro, prods.valor", 'quantidade DESC', null, 'produtos.nome, SUM(prods.quantidade) as quantidade, prods.valor, SUM(prods.valor * prods.quantidade) as total');
    }

    /**
     * Soma dos valores pagos referente ao AP
     *
     */
    public static function getValoresPagos($codigo, $dataI, $dataF)
    {
        return (new Database('log_pagamentos'))->select(
            "codigo_ap = $codigo AND data  > '$dataI 00:00:00'  AND data < '$dataF 23:59:59'",
            null,
            null,
            'SUM(valor) as valor'
        )->fetchObject(self::class);
    }

    public static function getSaidas($dataI, $dataF)
    {
        return (new Database('saidas'))->select("status = 1 AND data_pagamento > '$dataI 00:00:00'  AND data_pagamento < '$dataF 23:59:59'");
    }

    public static function getSaidasSomaTotal($dataI, $dataF)
    {
        return (new Database('saidas'))->select("status = 1 AND data_pagamento > '$dataI 00:00:00'  AND data_pagamento < '$dataF 23:59:59'", null, null, "SUM(valor) as soma");
    }
}
