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
     * Lista os livros que possuem ao menos um exemplar disponível para
     * empréstimo, com a contagem de cópias disponíveis de cada um.
     * Usado no formulário de registro de empréstimo — o usuário escolhe
     * o livro, e o sistema seleciona automaticamente qual exemplar sai.
     */
    public static function livrosComDisponibilidade(): array
    {
        $sql = "
            SELECT l.id AS livro_id, l.titulo, COUNT(e.id) AS disponiveis
            FROM exemplar e
            JOIN livro l ON l.id = e.livro_id
            WHERE e.status = 'disponivel'
            GROUP BY l.id, l.titulo
            HAVING COUNT(e.id) > 0
            ORDER BY l.titulo
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
