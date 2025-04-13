<?php
namespace Firebase\JWT;

class JWT {
    public static function decode($jwt, $keyOrKeyArray) {
        // Simulação de lógica de decodificação
        list($header, $payload, $signature) = explode('.', $jwt);
        return json_decode(base64_decode(strtr($payload, '-_', '+/')));
    }
}
