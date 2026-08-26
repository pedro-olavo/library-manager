<?php

namespace App\Models;

use App\Core\Database;
use PDOException;

/**
 * Model responsável pelos exemplares (cópias físicas) de cada livro.
 *
 * Cada livro pode ter vários exemplares, cada um com seu próprio status:
 * disponivel | emprestado | reservado | manutencao.
 */
class Exemplar
{
    /**
     * Lista os exemplares de um livro específico.
     */
    public static function allByLivro(int $livroId): array
    {
        $stmt = Database::getConnection()->prepare(
            'SELECT id, codigo_patrimonio, status FROM exemplar WHERE livro_id = :livro_id ORDER BY id'
        );
        $stmt->execute(['livro_id' => $livroId]);

        return $stmt->fetchAll();
    }

    /**
     * Lista todos os exemplares com status 'disponivel', já com o título
     * do livro correspondente — usado no dropdown de registro de empréstimo.
     */
    public static function allDisponiveis(): array
    {
        $sql = "
            SELECT e.id, e.codigo_patrimonio, l.titulo AS livro_titulo
            FROM exemplar e
            JOIN livro l ON l.id = e.livro_id
            WHERE e.status = 'disponivel'
            ORDER BY l.titulo, e.codigo_patrimonio
        ";

        return Database::getConnection()->query($sql)->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::getConnection()->prepare(
            'SELECT id, livro_id, codigo_patrimonio, status FROM exemplar WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $exemplar = $stmt->fetch();

        return $exemplar === false ? null : $exemplar;
    }

    public static function create(int $livroId, string $codigoPatrimonio): int
    {
        $stmt = Database::getConnection()->prepare('
            INSERT INTO exemplar (livro_id, codigo_patrimonio, status)
            VALUES (:livro_id, :codigo, \'disponivel\')
            RETURNING id
        ');
        $stmt->execute([
            'livro_id' => $livroId,
            'codigo'   => $codigoPatrimonio !== '' ? $codigoPatrimonio : null,
        ]);

        return (int) $stmt->fetchColumn();
    }

    public static function updateStatus(int $id, string $status): void
    {
        $stmt = Database::getConnection()->prepare('UPDATE exemplar SET status = :status WHERE id = :id');
        $stmt->execute(['id' => $id, 'status' => $status]);
    }

    /**
     * Remove um exemplar. Se houver empréstimos associados a ele, o banco
     * rejeita a exclusão (chave estrangeira sem CASCADE) — o Controller
     * trata essa exceção e exibe uma mensagem amigável.
     *
     * @throws PDOException Se o exemplar tiver empréstimos associados.
     */
    public static function delete(int $id): void
    {
        $stmt = Database::getConnection()->prepare('DELETE FROM exemplar WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
