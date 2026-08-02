<?php

namespace App\Models;

use App\Core\Database;

class Autor
{
    public static function all(): array
    {
        $stmt = Database::getConnection()->query('SELECT id, nome, nacionalidade FROM autor ORDER BY nome');

        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::getConnection()->prepare('SELECT id, nome, nacionalidade FROM autor WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $autor = $stmt->fetch();

        return $autor === false ? null : $autor;
    }

    public static function create(string $nome, ?string $nacionalidade): int
    {
        $stmt = Database::getConnection()->prepare(
            'INSERT INTO autor (nome, nacionalidade) VALUES (:nome, :nacionalidade) RETURNING id'
        );
        $stmt->execute([
            'nome'          => $nome,
            'nacionalidade' => $nacionalidade ?: null,
        ]);

        return (int) $stmt->fetchColumn();
    }

    public static function update(int $id, string $nome, ?string $nacionalidade): void
    {
        $stmt = Database::getConnection()->prepare(
            'UPDATE autor SET nome = :nome, nacionalidade = :nacionalidade WHERE id = :id'
        );
        $stmt->execute([
            'id'            => $id,
            'nome'          => $nome,
            'nacionalidade' => $nacionalidade ?: null,
        ]);
    }

    public static function delete(int $id): void
    {
        $stmt = Database::getConnection()->prepare('DELETE FROM autor WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
