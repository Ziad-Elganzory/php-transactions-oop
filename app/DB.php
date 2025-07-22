<?php

declare(strict_types = 1);

namespace App;

use PDO;

/**
 * @mixin PDO
 */
class DB
{
    private PDO $pdo;

    public function __construct(array $config)
    {
        $defaultOptions = [
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        try {
            $this->pdo = new PDO(
                $config['driver'] . ':host=' . $config['host'],
                $config['user'],
                $config['pass'],
                $config['options'] ?? $defaultOptions
            );

            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->query('CREATE DATABASE IF NOT EXISTS ' . $config['database']);
            $this->pdo->query('USE ' . $config['database']);
        } catch (\PDOException $e) {
            echo $e;
        }
    }

    public function __call(string $name, array $arguments)
    {
        return call_user_func_array([$this->pdo, $name], $arguments);
    }
}
