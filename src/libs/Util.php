<?php

namespace RR\libs;

use Exception;
use Imagine\Image\Box;
use Imagine\Gd\Imagine;
use RR\model\Currencies;
use Imagine\Image\ImageInterface;

class Util
{

    public static function getIp()
    {
        return isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER['REMOTE_ADDR'];
    }

    public static function findArrayObjectElement($array, $key, $value)
    {
        foreach ($array as $item) {
            if ($value === $item->{$key}) {
                return true;
            }
        }

        return false;
    }

    public static function findIntersectionInMatrix($matrix)
    {
        if (count($matrix) == 1) {
            return $matrix[0];
        } else if (count($matrix) == 0) {
            return $matrix;
        }

        return array_intersect(...$matrix);
    }

    public static function compareArray($existents, $property, $new)
    {
        return [
            'add' => array_filter($new, function ($element) use ($existents, $property) {
                return empty(array_filter($existents, function ($existentElement) use ($element, $property) {
                    return $existentElement->{$property} === $element;
                }));
            }),
            'exc' => array_filter($existents, function ($existentElement) use ($new, $property) {
                return empty(array_filter($new, function ($element) use ($existentElement, $property) {
                    return $existentElement->{$property} === $element;
                }));
            })
        ];
    }

    public static function titleCase($string, $delimiters = array(" ", "-", ".", "'", "O'", "Mc"), $exceptions = array("de", "da", "dos", "das", "do", "I", "II", "III", "IV", "V", "VI"))
    {
        /*
         * Exceptions in lower case are words you don't want converted
         * Exceptions all in upper case are any words you don't want converted to title case
         *   but should be converted to upper case, e.g.:
         *   king henry viii or king henry Viii should be King Henry VIII
         */
        $string = mb_convert_case($string, MB_CASE_TITLE, "UTF-8");
        foreach ($delimiters as $dlnr => $delimiter) {
            $words = explode($delimiter, $string);
            $newwords = array();
            foreach ($words as $wordnr => $word) {
                if (in_array(mb_strtoupper($word, "UTF-8"), $exceptions)) {
                    // check exceptions list for any words that should be in upper case
                    $word = mb_strtoupper($word, "UTF-8");
                } elseif (in_array(mb_strtolower($word, "UTF-8"), $exceptions)) {
                    // check exceptions list for any words that should be in upper case
                    $word = mb_strtolower($word, "UTF-8");
                } elseif (!in_array($word, $exceptions)) {
                    // convert to uppercase (non-utf8 only)
                    $word = ucfirst($word);
                }
                array_push($newwords, $word);
            }
            $string = join($delimiter, $newwords);
        } //foreach
        return $string;
    }

    public static function zeroFill($value, $length)
    {
        while (($length - strlen($value)) > 0) {
            $value = '0' . $value;
        }

        return $value;
    }

    public static function mask($value, $mask)
    {
        $masked = "";
        $k = 0;

        for ($i = 0; $i < strlen($mask); $i++) {
            if ($mask[$i] == "#") {
                if (isset($value[$k])) {
                    $masked .= $value[$k++];
                }
            } else {
                if (isset($mask[$i])) {
                    $masked .= $mask[$i];
                }
            }
        }

        return $masked;
    }

    public static function maskCpfCnpj($value)
    {
        $value = self::zeroFill($value, ((strlen($value) <= 11) ? 11 : 14));

        return ((strlen($value) == 11) ? self::maskCpf($value) : self::maskCnpj($value));
    }

    public static function maskCpf($value)
    {
        $value = str_replace([".", "-", "/", " "], "", $value);
        if (strlen($value) != 11) {
            return null;
        }

        return self::mask($value, "###.###.###-##");
    }

    public static function maskAgency($value)
    {
        $value = preg_replace('/\D/', '', $value);

        if (strlen($value) <= 4) {
            return self::mask($value, "####");
        } elseif (strlen($value) <= 5) {
            return self::mask($value, "####-#");
        } elseif (strlen($value) <= 6) {
            return self::mask($value, "####-##");
        } else {
            return null;
        }
    }

    public static function maskAccount($value)
    {
        $value = preg_replace('/\D/', '', $value);

        if (strlen($value) <= 6) {
            return self::mask($value, "#####-#");
        } elseif (strlen($value) <= 7) {
            return self::mask($value, "######-#");
        } elseif (strlen($value) <= 8) {
            return self::mask($value, "#######-#");
        } elseif (strlen($value) <= 9) {
            return self::mask($value, "########-#");
        } elseif (strlen($value) <= 10) {
            return self::mask($value, "#########-#");
        } elseif (strlen($value) <= 11) {
            return self::mask($value, "##########-#");
        } else {
            return null;
        }
    }

    public static function maskRg($value)
    {
        $value = preg_replace('/\D/', '', $value);

        if (strlen($value) <= 7) {
            return self::mask($value, "#.###.###");
        } else if (strlen($value) <= 8) {
            return self::mask($value, "#.###.###-#");
        } else {
            return self::mask($value, "##.###.###-#");
        }
    }

    public static function maskCnpj($value)
    {
        $value = str_replace([".", "-", "/", ""], "", $value);
        if (strlen($value) != 14) {
            return null;
        }

        return self::mask($value, "##.###.###/####-##");
    }

    public static function maskCep($value)
    {
        $value = preg_replace('/[^0-9]{1,}/', '', $value);
        if (strlen($value) != 8) {
            return null;
        }

        return self::mask($value, "#####-###");
    }

    public static function maskTelefone($value)
    {
        if (!is_numeric($value)) {
            return $value;
        }

        $value = str_replace(["(", ")", "-", "+", " "], "", $value);
        if (strlen($value) < 8 || strlen($value) > 13) {
            return null;
        }

        switch (strlen($value)) {
            case 8:
                $mask = "####-####";
                break;
            case 9:
                $mask = "#####-####";
                break;
            case 10:
                $mask = "(##) ####-####";
                break;
            case 11:
                $mask = "(##) #####-####";
                break;
            case 12:
                $mask = "## (##) ####-####";
                break;
            case 13:
                $mask = "## (##) #####-####";
                break;
        }

        return self::mask($value, $mask);
    }

    public static function maskMoney($value)
    {
        return "R$ " . number_format($value, 2, ',', '.');
    }

    public static function maskCurrency($value, $curerncyId)
    {
        $currencies = (new Currencies)->getItemById($curerncyId);

        return $currencies->currency_symbol . " " . number_format($value, 2, ',', '.');
    }

    public static function maskMoneyInt($value)
    {
        return number_format($value, 2, ',', '.');
    }

    public static function maskInt($value)
    {
        return number_format($value, 0, ',', '.');
    }

    public static function br2nl($string)
    {
        return preg_replace('/\<br(\s*)?\/?\>/i', PHP_EOL, $string);
    }

    public static function removeNumberFormatting($strNumero)
    {
        $strNumero = trim(str_replace("R$", "", $strNumero));
        $vetVirgula = explode(",", $strNumero);
        if (count($vetVirgula) == 1) {
            $acentos = array(".");
            $resultado = str_replace($acentos, "", $strNumero);
            return $resultado;
        } else if (count($vetVirgula) != 2) {
            return $strNumero;
        }
        $strNumero = $vetVirgula[0];
        $strDecimal = mb_substr($vetVirgula[1], 0, 2);
        $acentos = array(".");
        $resultado = str_replace($acentos, "", $strNumero);
        $resultado = $resultado . "." . $strDecimal;
        return $resultado;
    }

    public static function converte($valor = 0, $bolExibirMoeda = true, $bolPalavraFeminina = false)
    {
        $valor = self::removeNumberFormatting($valor);
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
        $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezesete", "dezoito", "dezenove");
        $u = array("", "um", "dois", "três", "quatro", "cinco", "seis", "sete", "oito", "nove");
        if ($bolPalavraFeminina) {
            if ($valor == 1)
                $u = array("", "uma", "duas", "três", "quatro", "cinco", "seis", "sete", "oito", "nove");
            else
                $u = array("", "um", "duas", "três", "quatro", "cinco", "seis", "sete", "oito", "nove");
            $c = array("", "cem", "duzentas", "trezentas", "quatrocentas", "quinhentas", "seiscentas", "setecentas", "oitocentas", "novecentas");
        }
        $z = 0;
        $valor = number_format($valor, 2, ".", ".");
        $inteiro = explode(".", $valor);
        for ($i = 0; $i < count($inteiro); $i++)
            for ($ii = mb_strlen($inteiro[$i]); $ii < 3; $ii++)
                $inteiro[$i] = "0" . $inteiro[$i];
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

    public static function unmaskMoney($value)
    {
        $value = str_replace(".", "", $value);
        $value = str_replace(",", ".", $value);
        $value = preg_replace('/[^0-9.]+/', '', $value);

        return $value;
    }

    public static function removeNonNumericForFloat($value)
    {
        $value = str_replace(",", ".", $value);
        $value = preg_replace('/[^0-9.]+/', '', $value);

        return $value;
    }

    public static function removeNonNumericCharacters($string)
    {
        return preg_replace('/\D/', '', $string);
    }

    public static function matheval($equation)
    {
        $equation = preg_replace("/[^0-9+\-.*\/()%]/", "", $equation);
        // fix percentage calcul when percentage value < 10
        $equation = preg_replace("/([+-])([0-9]{1})(%)/", "*(1\$1.0\$2)", $equation);
        // calc percentage
        $equation = preg_replace("/([+-])([0-9]+)(%)/", "*(1\$1.\$2)", $equation);
        // you could use str_replace on this next line
        // if you really, really want to fine-tune this equation
        $equation = preg_replace("/([0-9]+)(%)/", ".\$1", $equation);
        if ($equation == "") {
            $return = 0;
        } else {
            eval("\$return=" . $equation . ";");
        }
        return $return;
    }

    public static function coalesce($value, $valueIsNull = NULL)
    {
        return empty($value) ? $valueIsNull : $value;
    }

    public static function removeAccentuation($string)
    {
        return preg_replace(array("/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/", "/(ç)/", "/(Ç)/"), explode(" ", "a A e E i I o O u U n N c C"), $string);
    }

    public static function slugify($string)
    {
        $string = trim($string);
        $string = mb_strtolower(strtolower(strip_tags(preg_replace(array('/[`^~\'"]/', '/([\s]{1,})/', '/[-]{2,}/'), array(null, '-', '-'), $string))), 'UTF-8');
        $string = str_replace("%", "", $string);
        $string = str_replace("@", "", $string);
        $string = str_replace("!", "", $string);
        $string = str_replace("?", "", $string);
        $string = str_replace("#", "", $string);
        $string = str_replace("$", "", $string);
        $string = str_replace("&", "", $string);
        $string = str_replace("*", "", $string);
        $string = str_replace("(", "", $string);
        $string = str_replace(")", "", $string);
        $string = str_replace("+", "", $string);
        $string = str_replace("=", "", $string);
        $string = str_replace("/", "", $string);
        $string = str_replace("|", "", $string);
        $string = str_replace("ª", "-", $string);
        $string = str_replace("º", "-", $string);
        $string = str_replace(",", "-", $string);
        $string = str_replace(".", "-", $string);
        $string = str_replace("'", "-", $string);
        $string = str_replace('"', "-", $string);
        $string = str_replace('¨', "-", $string);
        $string = preg_replace('~-+~', "-", $string);
        // $string = str_replace("---", "-", $string);
        // $string = str_replace("--", "-", $string);
        // $string = str_replace("-", "-", $string);
        return self::removeAccentuation($string);
    }

    public static function likePHP($needle, $haystack)
    {
        $regex = '/' . str_replace('%', '.*?', $needle) . '/';

        return preg_match($regex, $haystack) > 0;
    }


    /**
     * @param string|int|array|object $var varíavel
     * @param string $title
     * @param bool $die
     */
    public static function debug($var, $title = '', $die = false): void
    {
        if (ENVIRONMENT === 'development') {
            echo "<pre>";
            if (!empty($title)) echo "<div class='debug'><h1><strong>{$title}</strong></h1></div>";

            print_r($var);

            if ($die == true) die();
            echo "</pre>";
        }
    }

    /**
     * @param array $array
     * @param int $order
     * @param int $length
     * @param string|int $keys
     * @return array
     */
    public static function repositionArray(array $array, int $order, $keys)
    {
        if (!in_array($keys, $array)) return $array;

        $index = array_search($keys, $array);
        array_splice($array, $index, 1);
        array_splice($array, $order, 0, $keys);

        return $array;
    }

    /**
     * True implementation, based on this spec:
     * https://www.php.net/manual/pt_BR/language.types.boolean.php
     *
     * PHP PRO version
     *
     * @var boolean $true
     * @var bool $notFalse
     *
     * @category PHP
     * @package  Advanced PHP
     * @author   Elon Musk <@elonmusk>
     * @license  https://www.php.net/license/3_01.txt  PHP
     *
     * @return bool
     */
    public static function alwaysTrue()
    {
        /** @var $true used to return the truth value */
        (bool)$true = true;
        /** @var $notFalse return not a false value */
        (bool)$notFalse = !false;

        /** @return bool as a true value */
        return $true && $notFalse;
    }

    public static function removeAccentsTransformLowercase($value)
    {
        $value = iconv('UTF-8', 'ASCII//TRANSLIT', $value);

        $value = strtolower($value);

        return $value;
    }

    public static function gererateToken($length = 32)
    {
        $token = bin2hex(random_bytes($length));

        return $token;
    }

    public static function resizeImageWithCanvas($sourceImage, $targetWidth, $targetHeight, $outputName)
    {
        $imagine = new Imagine();

        // Abre a imagem original
        $image = $imagine->open($sourceImage);

        // Redimensiona a imagem mantendo a proporção
        $image->resize(new Box($targetWidth, $targetHeight), ImageInterface::FILTER_LANCZOS)->save($outputName);
    }

    private static $purifier = null;

    /**
     * A3: limpa HTML de campos com editor de texto rico (CKEditor) antes de exibir — mantém a formatação e remove
     * scripts, eventos (on*) e links javascript:. Usa o HTMLPurifier com a lista padrão de tags/atributos seguros.
     */
    public static function richText($html): string
    {
        if (self::$purifier === null) {
            $config = \HTMLPurifier_Config::createDefault();
            $config->set('Cache.DefinitionImpl', null); // sem cache em disco: a pasta vendor pode não ser gravável no servidor
            $config->set('Attr.AllowedFrameTargets', ['_blank']);
            self::$purifier = new \HTMLPurifier($config);
        }

        return self::$purifier->purify((string) $html);
    }

    /**
     * A3: escape de campos que misturam texto digitado com HTML gravado pelo próprio sistema (timeline de cheque e
     * descrição de lançamento/parcela gerada por cheque). Escapa tudo e restaura só o que o sistema grava:
     * <strong>, </strong>, </a> e <a href='{URL}rota/interna' target='_blank'>.
     */
    public static function escapeSystemHtml($text): string
    {
        $escaped = htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
        $escaped = str_replace(['&lt;strong&gt;', '&lt;/strong&gt;', '&lt;/a&gt;'], ['<strong>', '</strong>', '</a>'], $escaped);

        $url = preg_quote(htmlspecialchars(URL, ENT_QUOTES, 'UTF-8'), '/');

        return preg_replace("/&lt;a href=&#039;({$url}[A-Za-z0-9\/-]*)&#039; target=&#039;_blank&#039;&gt;/", "<a href='$1' target='_blank'>", $escaped);
    }
}
