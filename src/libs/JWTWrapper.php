<?php

namespace RR\libs;

use Firebase\JWT\JWT;

class JWTWrapper
{
    const KEY = 'fa08aebc1c688d31204c1616c4277de2';

    /**
     * @param array $options
     */
    public static function encode($options)
    {
        return JWT::encode($options, self::KEY, 'HS256');
    }

    /**
     * @param string $jwt token
     */
    public static function decode($jwt)
    {
        return JWT::decode($jwt, self::KEY, 'HS256');
    }
}
