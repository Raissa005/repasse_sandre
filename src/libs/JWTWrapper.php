<?php

namespace RR\libs;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTWrapper
{
    /**
     * M4: a chave fica em JWT_KEY, no src/config/config.php (não versionado), e não mais no código.
     */
    private static function key(): string
    {
        if (!defined('JWT_KEY') || strlen(JWT_KEY) < 32) {
            throw new \RuntimeException('JWT_KEY não configurada em src/config/config.php (mínimo de 32 caracteres).');
        }

        return JWT_KEY;
    }

    /**
     * @param array $options
     */
    public static function encode($options)
    {
        return JWT::encode($options, self::key(), 'HS256');
    }

    /**
     * @param string $jwt token
     */
    public static function decode($jwt)
    {
        // A versão instalada do firebase/php-jwt exige o algoritmo dentro de um Key; com a chave em texto o decode falhava sempre
        return JWT::decode($jwt, new Key(self::key(), 'HS256'));
    }
}
