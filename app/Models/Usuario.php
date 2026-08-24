<?php

namespace App\Models;

use App\Core\Database;

/**
 * Model responsável pelo acesso aos dados da entidade Usuario e pela
 * verificação de credenciais de login.
 *
 * As senhas nunca são armazenadas em texto puro: usa-se password_hash()
 * (algoritmo bcrypt) no cadastro e password_verify() na autenticação.
 */
class Usuario
{
    public static function all(): array
    {
        $stmt = Database::getConnection()->query(
            'SELECT id, nome, email, perfil, data_cadastro FROM usuario ORDER BY nome'
        );

        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::getConnection()->prepare(
            'SELECT id, nome, email, perfil FROM usuario WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $usuario = $stmt->fetch();

        return $usuario === false ? null : $usuario;
    }

    public static function findByEmail(string $email): ?array
    {
        $stmt = Database::getConnection()->prepare(
            'SELECT id, nome, email, senha_hash, perfil FROM usuario WHERE email = :email'
        );
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch();

        return $usuario === false ? null : $usuario;
    }

    /**
     * Verifica as credenciais informadas no login.
     * Retorna os dados do usuário (sem o hash da senha) em caso de sucesso,
     * ou null se o e-mail não existir ou a senha estiver incorreta.
     */
    public static function authenticate(string $email, string $senha): ?array
    {
        $usuario = self::findByEmail($email);

        if ($usuario === null || !password_verify($senha, $usuario['senha_hash'])) {
            return null;
        }

        unset($usuario['senha_hash']);

        return $usuario;
    }

    public static function create(string $nome, string $email, string $senha, string $perfil): int
    {
        $stmt = Database::getConnection()->prepare('
            INSERT INTO usuario (nome, email, senha_hash, perfil)
            VALUES (:nome, :email, :senha_hash, :perfil)
            RETURNING id
        ');

        $stmt->execute([
            'nome'       => $nome,
            'email'      => $email,
            'senha_hash' => password_hash($senha, PASSWORD_BCRYPT),
            'perfil'     => $perfil,
        ]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Atualiza os dados do usuário. A senha só é alterada quando um novo
     * valor (não vazio) é informado — permite editar nome/e-mail/perfil
     * sem obrigar a redefinição da senha a cada edição.
     */
    public static function update(int $id, string $nome, string $email, string $perfil, ?string $novaSenha = null): void
    {
        $pdo = Database::getConnection();

        if ($novaSenha !== null && $novaSenha !== '') {
            $stmt = $pdo->prepare('
                UPDATE usuario
                SET nome = :nome, email = :email, perfil = :perfil, senha_hash = :senha_hash
                WHERE id = :id
            ');
            $stmt->execute([
                'id'         => $id,
                'nome'       => $nome,
                'email'      => $email,
                'perfil'     => $perfil,
                'senha_hash' => password_hash($novaSenha, PASSWORD_BCRYPT),
            ]);
            return;
        }

        $stmt = $pdo->prepare('
            UPDATE usuario
            SET nome = :nome, email = :email, perfil = :perfil
            WHERE id = :id
        ');
        $stmt->execute([
            'id'     => $id,
            'nome'   => $nome,
            'email'  => $email,
            'perfil' => $perfil,
        ]);
    }

    public static function delete(int $id): void
    {
        $stmt = Database::getConnection()->prepare('DELETE FROM usuario WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function emailExists(string $email, ?int $excludingId = null): bool
    {
        if ($excludingId !== null) {
            $stmt = Database::getConnection()->prepare(
                'SELECT 1 FROM usuario WHERE email = :email AND id != :id'
            );
            $stmt->execute(['email' => $email, 'id' => $excludingId]);
        } else {
            $stmt = Database::getConnection()->prepare('SELECT 1 FROM usuario WHERE email = :email');
            $stmt->execute(['email' => $email]);
        }

        return $stmt->fetchColumn() !== false;
    }
}
