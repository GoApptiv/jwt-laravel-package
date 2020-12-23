<?php

namespace GoApptiv\JWT;

use Exception;

class JWT {

    /**
     * Generate token
     *
     * @return mixed
     */
    public static function encrypt($data) {
        $header = self::encodeHeader();
        $payload = self::encodePayload(self::generatePayload($data));
        return implode(".", [
            $header,
            $payload,
            self::generateSignature($header, $payload),
        ]);
    }

    /**
     * Decrypt token
     *
     * @param $token
     * @return mixed
     * @throws Exception
     */
    public static function decrypt($token) {
        $parts = explode(".", $token);
        if (count($parts) != 3) {
            throw new Exception("Invalid Token");
        }

        if (self::generateSignature($parts[0], $parts[1]) !== $parts[2]) {
            throw new Exception("Invalid Token");
        }

        $decoded = self::decodePayload($parts[1]);
        if ($decoded['time'] < time()) {
            throw new Exception("Token Expired");
        }

        return $decoded;
    }

    private static function generatePayload($payload) {
        $payload['time'] = time() + (2 * 24 * 60 * 60);
        return $payload;
    }

    /**
     * Encode header
     *
     * @return string
     */
    private static function encodeHeader() {
        return base64_encode(json_encode([
            "alg" => "MD5",
            "type" => "JWT",
        ]));
    }

    private static function encodePayload($data) {
        return base64_encode(json_encode($data));
    }

    private static function decodePayload($payload) {
        return json_decode(base64_decode($payload), true);
    }

    private static function generateSignature($header, $payload) {
        return md5($header . "." . $payload . "." . env("TOKEN_SECRET_KEY"));
    }
}
