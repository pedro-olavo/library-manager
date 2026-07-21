<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Classe responsável pela conexão com o banco de dados PostgreSQL via PDO.
 *
 * Implementa o padrão Singleton para reaproveitar a mesma conexão durante
 * o ciclo de vida da requisição.
 *
 * OBS: A conexão efetiva com o banco e as consultas via PDO serão
 * utilizadas a partir da Entrega Parcial 3 (CRUD inicial). Nesta etapa,
 * a classe já é disponibilizada como parte da estrutura MVC.
 */
class Database
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $config = require dirname(__DIR__, 2) . '/config/config.php';
            $db = $config['db'];

            $dsn = "pgsql:host={$db['host']};port={$db['port']};dbname={$db['database']}";

            try {
                self::$instance = new PDO($dsn, $db['username'], $db['password'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $e) {
                die('Erro de conexão com o banco de dados: ' . $e->getMessage());
            }
        }

        return self::$instance;
    }
}
