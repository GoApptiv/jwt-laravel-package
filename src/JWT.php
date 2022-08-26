<?php

namespace GoApptiv\JWT;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;

class JWT
{

    /**
     * Generate token
     *
     * @param Collection $data
     * @param Carbon $expiryTime
     *
     * @return mixed
     */
    public static function encrypt(Collection $data, Carbon $expiryTime = null)
    {
        $header = self::encodeHeader();
        $payload = self::encodePayload(self::generatePayload($data, $expiryTime));
        return implode(".", [
            $header,
            $payload,
            self::base64url_encode(self::generateSignature($header, $payload)),
        ]);
    }

    /**
     * Decrypt token
     *
     * @param $token
     * @return mixed
     * @throws Exception
     */
    public static function decrypt($token)
    {
        $parts = explode(".", $token);
        if (count($parts) != 3) {
            throw new Exception("Invalid Token");
        }

        if (self::base64url_encode(self::generateSignature($parts[0], $parts[1])) !== $parts[2]) {
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
    private static function generatePayload(Collection $payload, Carbon $expiryTime = null)
    {
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
    private static function encodeHeader()
    {
        return self::base64url_encode(json_encode([
            "alg" => "HS256",
            "type" => "JWT",
            "kid" => env("JWT_APPLICATION_KID"),
        ]));
    }

    private static function encodePayload($data)
    {
        return self::base64url_encode(json_encode($data));
    }

    private static function decodePayload($payload)
    {
        return json_decode(base64_decode($payload), true);
    }

    private static function generateSignature($header, $payload)
    {
        return hash_hmac('sha256', "$header.$payload", env("TOKEN_SECRET_KEY"), true);
    }

    private static function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
