<?php

namespace App\Core;

/**
 * Classe responsável pela autenticação e controle de sessão do usuário.
 *
 * Centraliza o acesso a $_SESSION para que o restante da aplicação nunca
 * manipule a sessão diretamente — apenas através destes métodos estáticos.
 */
class Auth
{
    /**
     * Inicia a sessão PHP, caso ainda não tenha sido iniciada.
     * Deve ser chamado uma única vez, no início da requisição
     * (ver public/index.php), antes de qualquer saída ser enviada.
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Registra o usuário autenticado na sessão.
     */
    public static function login(array $usuario): void
    {
        session_regenerate_id(true);

        $_SESSION['usuario'] = [
            'id'     => $usuario['id'],
            'nome'   => $usuario['nome'],
            'email'  => $usuario['email'],
            'perfil' => $usuario['perfil'],
        ];
    }

    /**
     * Encerra a sessão do usuário atual.
     */
    public static function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie('PHPSESSID', '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }

        session_destroy();
    }

    /**
     * Retorna true se há um usuário autenticado na sessão atual.
     */
    public static function check(): bool
    {
        return isset($_SESSION['usuario']);
    }

    /**
     * Retorna os dados do usuário autenticado (ou null, se não houver).
     */
    public static function user(): ?array
    {
        return $_SESSION['usuario'] ?? null;
    }

    /**
     * Retorna o perfil (role) do usuário autenticado (ou null).
     */
    public static function role(): ?string
    {
        return $_SESSION['usuario']['perfil'] ?? null;
    }

    /**
     * Garante que exista um usuário autenticado; caso contrário,
     * redireciona para a tela de login e interrompe a execução.
     */
    public static function requireLogin(): void
    {
        if (!self::check()) {
            header('Location: /login');
            exit;
        }
    }

    /**
     * Garante que o usuário autenticado possua um dos perfis informados;
     * caso contrário, exibe a página 403 (acesso negado) e interrompe a execução.
     *
     * @param string[] $perfis Ex: ['administrador', 'bibliotecario']
     */
    public static function requireRole(array $perfis): void
    {
        self::requireLogin();

        if (!in_array(self::role(), $perfis, true)) {
            http_response_code(403);

            $title = 'Acesso negado';
            ob_start();
            require dirname(__DIR__) . '/Views/errors/403.php';
            $content = ob_get_clean();

            require dirname(__DIR__) . '/Views/layout.php';
            exit;
        }
    }
}
