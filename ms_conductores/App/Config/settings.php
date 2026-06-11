<?php
namespace App\Config;

class Settings
{
    public static function getAppName(): string
    {
        return $_ENV['APP_NAME'] ?? 'ms_conductores';
    }

    public static function getRoles(): array
    {
        $roles = $_ENV['APP_ROLES'] ?? 'admin,operador';
        return array_map('trim', explode(',', $roles));
    }
}
