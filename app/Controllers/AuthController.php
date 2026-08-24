<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Usuario;

class AuthController extends Controller
{
    /**
     * Exibe o formulário de login.
     * GET /login
     */
    public function showLogin(): void
    {
        // Se já estiver autenticado, não faz sentido ver a tela de login de novo.
        if (Auth::check()) {
            $this->redirect('/');
            return;
        }

        $this->render('auth/login', [
            'title' => 'Login',
            'error' => null,
            'old'   => [],
        ]);
    }

    /**
     * Processa o login.
     * POST /login
     */
    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $senha = (string) ($_POST['senha'] ?? '');

        $usuario = ($email !== '' && $senha !== '') ? Usuario::authenticate($email, $senha) : null;

        if ($usuario === null) {
            $this->render('auth/login', [
                'title' => 'Login',
                'error' => 'E-mail ou senha inválidos.',
                'old'   => ['email' => $email],
            ]);
            return;
        }

        Auth::login($usuario);
        $this->redirect('/');
    }

    /**
     * Encerra a sessão do usuário.
     * POST /logout
     */
    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/login');
    }
}
