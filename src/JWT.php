<?php

namespace GoApptiv\JWT;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;

class JWT {

    /**
     * Generate token
     *
     * @param Collection $data
     * @param Carbon $expiryTime
     *
     * @return mixed
     */
    public static function encrypt(Collection $data, Carbon $expiryTime = null) {
        $header = self::encodeHeader();
        $payload = self::encodePayload(self::generatePayload($data, $expiryTime));
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

        if (array_key_exists('token_expiry_timestamp', $decoded) && Carbon::now()->gt(Carbon::createFromTimestamp($decoded['token_expiry_timestamp']))) {
            throw new Exception("Token Expired");
        }

        return $decoded;
    }

    /**
     *
     * Generates Payload
     *
     *
     */
    private static function generatePayload(Collection $payload, Carbon $expiryTime = null) {

        if ($expiryTime !== null) {
            $payload['token_expiry_timestamp'] = $expiryTime->timestamp;
        }

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
