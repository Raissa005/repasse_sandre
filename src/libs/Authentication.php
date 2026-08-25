<?php

namespace RR\libs;

class Authentication
{
    public static function centralizedAuthentication($email, $pass)
    {
        // if (ENVIRONMENT == 'production') {
        $url = addslashes($email) . "?" . addslashes(base64_encode($pass));
        $url_encode = urlencode($url);
        $ip = isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER['REMOTE_ADDR'];
        $response = file_get_contents("http://www.ydealtecnologia.com.br/autenticacao/autentica_ymoveis.php?ip={$ip}&token={$url_encode}");
        $lista = explode(";", $response);

        for ($i = 0; $i < count($lista); $i++) {
            $lista2 = explode(":", $lista[$i]);
            $autentica[trim($lista2[0])] = trim($lista2[1]);
        }

        // }

        return $autentica;
    }
}
