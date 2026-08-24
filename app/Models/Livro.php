<?php

namespace App\Models;

use App\Core\Database;

/**
 * Model responsável pelo acesso aos dados da entidade Livro.
 *
 * Entrega Parcial 3: implementa Create (cadastro) e Read (listagem)
 * via PDO. Update e Delete serão implementados na Entrega Parcial 4.
 *
 * Relacionamento com Autor simplificado para N:1 (um autor por livro),
 * selecionado via dropdown — ver observação em database/init.sql.
 */
class Livro
{
    /**
     * Retorna todos os livros cadastrados, já com o nome do autor,
     * da categoria e da editora relacionados (via LEFT JOIN).
     */
    public static function all(): array
    {
        $sql = "
            SELECT
                l.id,
                l.titulo,
                l.isbn,
                l.ano_publicacao,
                c.nome AS categoria,
                e.nome AS editora,
                a.nome AS autor
            FROM livro l
            LEFT JOIN categoria c ON c.id = l.categoria_id
            LEFT JOIN editora   e ON e.id = l.editora_id
            LEFT JOIN autor     a ON a.id = l.autor_id
            ORDER BY l.id DESC
        ";

        return Database::getConnection()->query($sql)->fetchAll();
    }

    /**
     * Retorna a quantidade total de livros cadastrados (usado no dashboard).
     */
    public static function count(): int
    {
        $stmt = Database::getConnection()->query('SELECT COUNT(*) FROM livro');

        return (int) $stmt->fetchColumn();
    }

    /**
     * Busca um único livro pelo id, com os mesmos dados relacionados de all().
     */
    public static function find(int $id): ?array
    {
        $sql = "
            SELECT
                l.id,
                l.titulo,
                l.isbn,
                l.ano_publicacao,
                l.categoria_id,
                l.editora_id,
                l.autor_id,
                c.nome AS categoria,
                e.nome AS editora,
                a.nome AS autor
            FROM livro l
            LEFT JOIN categoria c ON c.id = l.categoria_id
            LEFT JOIN editora   e ON e.id = l.editora_id
            LEFT JOIN autor     a ON a.id = l.autor_id
            WHERE l.id = :id
        ";

        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute(['id' => $id]);
        $livro = $stmt->fetch();

        return $livro === false ? null : $livro;
    }

    /**
     * Insere um novo livro.
     *
     * @param array $data ['titulo', 'isbn', 'ano_publicacao', 'categoria_id', 'editora_id', 'autor_id']
     * @return int Id do livro recém-criado.
     */
    public static function create(array $data): int
    {
        $stmt = Database::getConnection()->prepare("
            INSERT INTO livro (titulo, isbn, ano_publicacao, categoria_id, editora_id, autor_id)
            VALUES (:titulo, :isbn, :ano_publicacao, :categoria_id, :editora_id, :autor_id)
            RETURNING id
        ");

        $stmt->execute([
            'titulo'         => $data['titulo'],
            'isbn'           => $data['isbn'] ?: null,
            'ano_publicacao' => $data['ano_publicacao'] ?: null,
            'categoria_id'   => $data['categoria_id'] ?: null,
            'editora_id'     => $data['editora_id'] ?: null,
            'autor_id'       => $data['autor_id'] ?: null,
        ]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Atualiza um livro existente.
     *
     * @param int   $id   Id do livro a ser atualizado.
     * @param array $data ['titulo', 'isbn', 'ano_publicacao', 'categoria_id', 'editora_id', 'autor_id']
     */
    public static function update(int $id, array $data): void
    {
        $stmt = Database::getConnection()->prepare("
            UPDATE livro
            SET titulo = :titulo,
                isbn = :isbn,
                ano_publicacao = :ano_publicacao,
                categoria_id = :categoria_id,
                editora_id = :editora_id,
                autor_id = :autor_id
            WHERE id = :id
        ");

        $stmt->execute([
            'id'             => $id,
            'titulo'         => $data['titulo'],
            'isbn'           => $data['isbn'] ?: null,
            'ano_publicacao' => $data['ano_publicacao'] ?: null,
            'categoria_id'   => $data['categoria_id'] ?: null,
            'editora_id'     => $data['editora_id'] ?: null,
            'autor_id'       => $data['autor_id'] ?: null,
        ]);
    }

    /**
     * Remove um livro do banco de dados.
     */
    public static function delete(int $id): void
    {
        $stmt = Database::getConnection()->prepare('DELETE FROM livro WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
