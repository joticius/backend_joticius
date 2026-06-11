<?php
namespace App\Config;

class Database
{
    private static $connection = null;

    public static function connect()
    {
        if (self::$connection === null) {
            try {
                $host = $_ENV['DB_HOST'] ?? 'localhost';
                $port = $_ENV['DB_PORT'] ?? 3306;
                $database = $_ENV['DB_NAME'] ?? 'cargamesta';
                $user = $_ENV['DB_USER'] ?? 'root';
                $password = $_ENV['DB_PASSWORD'] ?? '';

                $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";

                self::$connection = new \PDO(
                    $dsn,
                    $user,
                    $password,
                    [
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                        \PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
            } catch (\PDOException $e) {
                die('Error de conexión: ' . $e->getMessage());
            }
        }

        return self::$connection;
    }

    public static function getConnection()
    {
        return self::connect();
    }
}
