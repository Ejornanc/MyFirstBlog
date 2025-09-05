<?php

namespace App\Database;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $pdo = null;

    /**
     * Lecture d'une variable d'environnement avec valeur par défaut.
     */
    private static function env(string $key, $default = null)
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?? $default;
    }

    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            try {
                // 1) Option A : une seule variable DATABASE_URL (ex: mysql://user:pass@mysql:3306/blog?charset=utf8mb4)
                $databaseUrl = self::env('DATABASE_URL');
                if ($databaseUrl) {
                    $dsnUser = $dsnPass = null;
                    $dsn = self::dsnFromDatabaseUrl($databaseUrl, $dsnUser, $dsnPass);
                    self::$pdo = new PDO($dsn, $dsnUser, $dsnPass, self::pdoOptions());
                    return self::$pdo;
                }

                // 2) Option B : variables unitaires
                $driver  = self::env('DB_DRIVER',  'mysql');
                $host    = self::env('DB_HOST',    '127.0.0.1');
                $port    = (int) self::env('DB_PORT', 3306);
                $name    = self::env('DB_NAME',    'blog');
                $user    = self::env('DB_USER',    'user');
                $pass    = self::env('DB_PASS',    'mdp');
                $charset = self::env('DB_CHARSET', 'utf8mb4');

                $dsn = sprintf('%s:host=%s;port=%d;dbname=%s;charset=%s', $driver, $host, $port, $name, $charset);

                self::$pdo = new PDO($dsn, $user, $pass, self::pdoOptions());
            } catch (PDOException $e) {
                // En prod, loggez l'erreur et affichez un message générique
                // error_log($e->getMessage());
                die('Database connection error.');
            }
        }

        return self::$pdo;
    }

    /**
     * Options PDO standard (sécurité et DX).
     */
    private static function pdoOptions(): array
    {
        $persistent = filter_var(self::env('DB_PERSISTENT', 'false'), FILTER_VALIDATE_BOOLEAN);

        return [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // remonte les erreurs
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // fetch assoc par défaut
            PDO::ATTR_EMULATE_PREPARES   => false,                  // vraies requêtes préparées
            PDO::ATTR_PERSISTENT         => $persistent,            // connexion persistante (optionnel)
        ];
    }

    /**
     * Construit un DSN à partir d'un DATABASE_URL (mysql://user:pass@host:port/db?charset=utf8mb4)
     */
    private static function dsnFromDatabaseUrl(string $url, ?string &$user = null, ?string &$pass = null): string
    {
        $parts = parse_url($url);
        if ($parts === false || !isset($parts['scheme'])) {
            throw new PDOException('Invalid DATABASE_URL');
        }

        $scheme = $parts['scheme'];          // mysql | pgsql | ...
        $host   = $parts['host'] ?? '127.0.0.1';
        $port   = $parts['port'] ?? null;
        $db     = isset($parts['path']) ? ltrim($parts['path'], '/') : '';
        $user   = $parts['user'] ?? null;
        $pass   = $parts['pass'] ?? null;

        // Query params (ex: ?charset=utf8mb4)
        $query = [];
        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
        }
        $charset = $query['charset'] ?? self::env('DB_CHARSET', 'utf8mb4');

        // MySQL DSN
        if ($scheme === 'mysql') {
            $dsn = "mysql:host={$host};dbname={$db};charset={$charset}";
            if ($port) {
                $dsn .= ";port={$port}";
            }
            return $dsn;
        }

        // PostgreSQL DSN (si besoin)
        if ($scheme === 'pgsql') {
            $dsn = "pgsql:host={$host};dbname={$db}";
            if ($port) {
                $dsn .= ";port={$port}";
            }
            // charset côté pgsql se gère souvent via client_encoding
            return $dsn;
        }

        throw new PDOException("Unsupported driver in DATABASE_URL: {$scheme}");
    }
}
