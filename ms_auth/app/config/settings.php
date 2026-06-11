<?php
namespace App\Config;

class Settings
{
    public static function getJwtSecret(): string
    {
        return $_ENV['JWT_SECRET'] ?? 'secret_key_local';
    }

    public static function getJwtAlgorithm(): string
    {
        return $_ENV['JWT_ALGORITHM'] ?? 'HS256';
    }

    public static function getJwtExpiration(): int
    {
        return intval($_ENV['JWT_EXPIRES_IN'] ?? 3600);
    }

    public static function getRoles(): array
    {
        $roles = $_ENV['APP_ROLES'] ?? 'admin,operador';
        return array_map('trim', explode(',', $roles));
    }
}
