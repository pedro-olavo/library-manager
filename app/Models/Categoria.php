<?php

namespace App\Models;

use App\Core\Database;

class Categoria
{
    public static function all(): array
    {
        $stmt = Database::getConnection()->query('SELECT id, nome FROM categoria ORDER BY nome');

        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::getConnection()->prepare('SELECT id, nome FROM categoria WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $categoria = $stmt->fetch();

        return $categoria === false ? null : $categoria;
    }

    public static function create(string $nome): int
    {
        $stmt = Database::getConnection()->prepare(
            'INSERT INTO categoria (nome) VALUES (:nome) RETURNING id'
        );
        $stmt->execute(['nome' => $nome]);

        return (int) $stmt->fetchColumn();
    }

    public static function update(int $id, string $nome): void
    {
        $stmt = Database::getConnection()->prepare(
            'UPDATE categoria SET nome = :nome WHERE id = :id'
        );
        $stmt->execute(['id' => $id, 'nome' => $nome]);
    }

    public static function delete(int $id): void
    {
        $stmt = Database::getConnection()->prepare('DELETE FROM categoria WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
