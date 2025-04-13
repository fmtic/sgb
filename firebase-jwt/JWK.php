<?php
namespace Firebase\JWT;

class JWK {
    public static function parseKeySet($jwks) {
        return $jwks['keys'][0]; // Simulação para testar
    }
}
