<?php

namespace App\Security;

class Csrf
{
    private const SESSION_KEY = 'csrf_token';

    public static function getToken(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            // Session should be started by Router; do nothing else here
        }
        if (empty($_SESSION[self::SESSION_KEY]) || !is_string($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::SESSION_KEY];
    }

    public static function validate(?string $token): bool
    {
        if (!$token) {
            return false;
        }
        $sessionToken = $_SESSION[self::SESSION_KEY] ?? '';
        return is_string($sessionToken) && hash_equals($sessionToken, (string)$token);
    }
}
