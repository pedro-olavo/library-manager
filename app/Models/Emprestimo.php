<?php

namespace App\Models;

use App\Core\Database;
use RuntimeException;

/**
 * Model responsável pelo controle de empréstimos de exemplares.
 *
 * Ciclo de vida de um empréstimo:
 *   1. create()            -> status 'em_dia', exemplar passa a 'emprestado'
 *   2. markOverdue()       -> 'em_dia' vira 'atrasado' quando passa da previsão
 *   3. registrarDevolucao() -> status 'devolvido', exemplar volta a 'disponivel'
 */
class Emprestimo
{
    /**
     * Atualiza para 'atrasado' os empréstimos cuja previsão de devolução já
     * passou e que ainda não foram devolvidos. Chamado antes de toda listagem
     * para manter o status sempre correto, já que não há um job agendado.
     */
    public static function markOverdue(): void
    {
        Database::getConnection()->exec("
            UPDATE emprestimo
            SET status = 'atrasado'
            WHERE status = 'em_dia' AND data_prevista_devolucao < CURRENT_DATE
        ");
    }

    /**
     * Lista empréstimos, com dados do usuário, do livro e do exemplar.
     *
     * @param int|null $usuarioId Se informado, restringe aos empréstimos desse usuário.
     * @param bool     $somenteAtivos Se true, oculta os já devolvidos.
     */
    public static function all(?int $usuarioId = null, bool $somenteAtivos = false): array
    {
        $sql = "
            SELECT
                emp.id,
                emp.data_emprestimo,
                emp.data_prevista_devolucao,
                emp.data_devolucao,
                emp.status,
                u.nome AS usuario_nome,
                l.titulo AS livro_titulo,
                ex.codigo_patrimonio
            FROM emprestimo emp
            JOIN usuario  u  ON u.id = emp.usuario_id
            JOIN exemplar ex ON ex.id = emp.exemplar_id
            JOIN livro    l  ON l.id = ex.livro_id
            WHERE 1 = 1
        ";

        $params = [];

        if ($usuarioId !== null) {
            $sql .= ' AND emp.usuario_id = :usuario_id';
            $params['usuario_id'] = $usuarioId;
        }

        if ($somenteAtivos) {
            $sql .= " AND emp.status != 'devolvido'";
        }

        $sql .= ' ORDER BY emp.id DESC';

        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Quantidade de empréstimos ainda não devolvidos (usado no dashboard).
     */
    public static function countAtivos(): int
    {
        $stmt = Database::getConnection()->query(
            "SELECT COUNT(*) FROM emprestimo WHERE status != 'devolvido'"
        );

        return (int) $stmt->fetchColumn();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::getConnection()->prepare('SELECT * FROM emprestimo WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $emprestimo = $stmt->fetch();

        return $emprestimo === false ? null : $emprestimo;
    }

    /**
     * Registra um novo empréstimo para um livro: o sistema seleciona
     * automaticamente um exemplar disponível desse livro (o usuário não
     * escolhe qual cópia física sai). Executado em transação com trava de
     * linha (FOR UPDATE) para evitar que dois empréstimos sejam registrados
     * simultaneamente para o mesmo exemplar (condição de corrida).
     *
     * @throws RuntimeException Se não houver exemplar disponível para o livro.
     */
    public static function create(int $usuarioId, int $livroId, string $dataPrevistaDevolucao): int
    {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare("
                SELECT id FROM exemplar
                WHERE livro_id = :livro_id AND status = 'disponivel'
                ORDER BY id
                LIMIT 1
                FOR UPDATE
            ");
            $stmt->execute(['livro_id' => $livroId]);
            $exemplarId = $stmt->fetchColumn();

            if ($exemplarId === false) {
                throw new RuntimeException('Não há exemplares disponíveis para este livro no momento.');
            }

            $stmt = $pdo->prepare('
                INSERT INTO emprestimo (usuario_id, exemplar_id, data_emprestimo, data_prevista_devolucao, status)
                VALUES (:usuario_id, :exemplar_id, CURRENT_DATE, :data_prevista, \'em_dia\')
                RETURNING id
            ');
            $stmt->execute([
                'usuario_id'     => $usuarioId,
                'exemplar_id'    => $exemplarId,
                'data_prevista'  => $dataPrevistaDevolucao,
            ]);
            $emprestimoId = (int) $stmt->fetchColumn();

            $pdo->prepare("UPDATE exemplar SET status = 'emprestado' WHERE id = :id")
                ->execute(['id' => $exemplarId]);

            $pdo->commit();

            return $emprestimoId;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Registra a devolução de um empréstimo: marca a data de devolução,
     * muda o status para 'devolvido' e libera o exemplar ('disponivel').
     */
    public static function registrarDevolucao(int $id): void
    {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare('SELECT exemplar_id FROM emprestimo WHERE id = :id FOR UPDATE');
            $stmt->execute(['id' => $id]);
            $emprestimo = $stmt->fetch();

            if ($emprestimo === false) {
                throw new RuntimeException('Empréstimo não encontrado.');
            }

            $pdo->prepare("
                UPDATE emprestimo
                SET data_devolucao = CURRENT_DATE, status = 'devolvido'
                WHERE id = :id
            ")->execute(['id' => $id]);

            $pdo->prepare("UPDATE exemplar SET status = 'disponivel' WHERE id = :id")
                ->execute(['id' => $emprestimo['exemplar_id']]);

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
