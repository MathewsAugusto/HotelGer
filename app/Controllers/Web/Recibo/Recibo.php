<?php

namespace App\Controllers\Web\Recibo;

use App\Controllers\Web\Page;
use App\Models\Apartamentos;
use App\Models\Cliente;
use App\Models\Cliente_hospedado;
use App\Models\Empresa;
use App\Models\Produtos_aps;
use App\Utils\View;
use DateTime;

class Recibo
{


    public static function getRecibo($request, $codigo)
    {
        date_default_timezone_set('America/Sao_Paulo');
        $empresa = Empresa::getEmpresa()->fetchObject(Empresa::class);
        $ap = Apartamentos::getRecibo($codigo)->fetchObject(Apartamentos::class);


        if(!$ap instanceof Apartamentos){
           $request->getRouter()->redirect('/');     
        }

        $date1 = new DateTime($ap->data_entrada);
        $date2 = new DateTime($ap->data_saida);
        $difereca = $date1->diff($date2);

        $diarias = $difereca->days;
        $horas   = $difereca->h;
        $valorHoras = $ap->valor_total / 24;

        $prods = Produtos_aps::getProdutosbyAps($ap->codigo);
        $cliente = Cliente_hospedado::getClienteHospedado($ap->codigo)->fetchObject(Cliente_hospedado::class);
       
        
        $somaProdutos = 0;
        while ($p = $prods->fetchObject(Produtos_aps::class)) {

            $somaProdutos += $p->valor * $p->quantidade;
        }

        $container = View::render('recibo/index', [
            'valor' => number_format(($ap->valor_total * $diarias) + ($horas * $valorHoras) + $somaProdutos, 2, ',', '.'),
            'cidade' => $empresa->cidade,
            'empresa' => $empresa->descricao,
            'cnpj' => $empresa->cnpj,
            'endereco' => $empresa->endereco,
            'contato' => $empresa->contato,
            'cep' => 'CEP ' . $empresa->cep,
            'cliente' => $cliente->nome,
            'referente'=> $horas == 0 ? $ap->quantidade . " de diária's de hospedagem" : $ap->quantidade . " diária's e $horas hr's de hospedagem",
            'dia' => date('d'),
            'mes' => self::Mes(date('m')),
            'ano' => date('Y'),
            'valor-desc' =>self::valorPorExtenso(($ap->valor_total * $diarias) + ($horas * $valorHoras) + $somaProdutos, false, false)
        ]);

        return Page::getPage($container, $request, false);
    }

    public static function Mes($mes)
    {
        switch ($mes) {

            case 1:
                return 'Janeiro';
                break;
            case 2:
                return 'Fevereiro';
                break;
            case 3:
                return 'Março';
                break;
            case 4:
                return 'Abril';
                break;
            case 5:
                return 'Maio';
                break;
            case 6:
                return 'Junho';
                break;
            case 7:
                return 'Julho';
                break;
            case 8:
                return 'Agosto';
                break;
            case 9:
                return 'Setembro';
                break;
            case 10:
                return 'Outubro';
                break;
            case 11:
                return 'Novembro';
                break;
            case 12:
                return 'Dezembro';
                break;
        }
    }

    public static function valorPorExtenso($valor = 0, $bolExibirMoeda = true, $bolPalavraFeminina = false)
    {


        $singular = null;
        $plural = null;

        if ($bolExibirMoeda) {
            $singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
            $plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões", "quatrilhões");
        } else {
            $singular = array("", "", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
            $plural = array("", "", "mil", "milhões", "bilhões", "trilhões", "quatrilhões");
        }

        $c = array("", "cem", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
        $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa");
        $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezessete", "dezoito", "dezenove");
        $u = array("", "um", "dois", "três", "quatro", "cinco", "seis", "sete", "oito", "nove");


        if ($bolPalavraFeminina) {

            if ($valor == 1) {
                $u = array("", "uma", "duas", "três", "quatro", "cinco", "seis", "sete", "oito", "nove");
            } else {
                $u = array("", "um", "duas", "três", "quatro", "cinco", "seis", "sete", "oito", "nove");
            }


            $c = array("", "cem", "duzentas", "trezentas", "quatrocentas", "quinhentas", "seiscentas", "setecentas", "oitocentas", "novecentas");
        }


        $z = 0;

        $valor = number_format($valor, 2, ".", ".");
        $inteiro = explode(".", $valor);

        for ($i = 0; $i < count($inteiro); $i++) {
            for ($ii = mb_strlen($inteiro[$i]); $ii < 3; $ii++) {
                $inteiro[$i] = "0" . $inteiro[$i];
            }
        }

        // $fim identifica onde que deve se dar junção de centenas por "e" ou por "," ;)
        $rt = null;
        $fim = count($inteiro) - ($inteiro[count($inteiro) - 1] > 0 ? 1 : 2);
        for ($i = 0; $i < count($inteiro); $i++) {
            $valor = $inteiro[$i];
            $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
            $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
            $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

            $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd && $ru) ? " e " : "") . $ru;
            $t = count($inteiro) - 1 - $i;
            $r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
            if ($valor == "000")
                $z++;
            elseif ($z > 0)
                $z--;

            if (($t == 1) && ($z > 0) && ($inteiro[0] > 0))
                $r .= (($z > 1) ? " de " : "") . $plural[$t];

            if ($r)
                $rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? (($i < $fim) ? ", " : " e ") : " ") . $r;
        }

        $rt = mb_substr($rt, 1);

        return ($rt ? trim($rt) : "zero");
    }
}
