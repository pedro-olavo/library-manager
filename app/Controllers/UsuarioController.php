<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Usuario;

/**
 * Gerenciamento de usuários e perfis de acesso.
 * Todas as ações exigem perfil 'administrador'.
 */
class UsuarioController extends Controller
{
    public function __construct()
    {
        Auth::requireRole(['administrador']);
    }

    /** GET /usuarios */
    public function index(): void
    {
        $this->render('usuarios/index', [
            'title'    => 'Usuários',
            'usuarios' => Usuario::all(),
            'success'  => $_GET['success'] ?? null,
        ]);
    }

    /** GET /usuarios/novo */
    public function create(): void
    {
        $this->render('usuarios/form', [
            'title'   => 'Novo Usuário',
            'usuario' => null,
            'errors'  => [],
            'old'     => [],
        ]);
    }

    /** POST /usuarios */
    public function store(): void
    {
        [$data, $errors] = $this->validate($_POST, null);

        if (!empty($errors)) {
            $this->render('usuarios/form', [
                'title'   => 'Novo Usuário',
                'usuario' => null,
                'errors'  => $errors,
                'old'     => $_POST,
            ]);
            return;
        }

        Usuario::create($data['nome'], $data['email'], $data['senha'], $data['perfil']);
        $this->redirect('/usuarios?success=criado');
    }

    /** GET /usuarios/{id}/editar */
    public function edit(string $id): void
    {
        $usuario = Usuario::find((int) $id);

        if ($usuario === null) {
            http_response_code(404);
            $this->render('errors/404', ['title' => 'Não encontrado']);
            return;
        }

        $this->render('usuarios/form', [
            'title'   => 'Editar Usuário',
            'usuario' => $usuario,
            'errors'  => [],
            'old'     => [],
        ]);
    }

    /** PUT /usuarios/{id} */
    public function update(string $id): void
    {
        [$data, $errors] = $this->validate($_POST, (int) $id);

        if (!empty($errors)) {
            $this->render('usuarios/form', [
                'title'   => 'Editar Usuário',
                'usuario' => array_merge(['id' => $id], $data),
                'errors'  => $errors,
                'old'     => $_POST,
            ]);
            return;
        }

        Usuario::update((int) $id, $data['nome'], $data['email'], $data['perfil'], $data['senha'] ?: null);
        $this->redirect('/usuarios?success=atualizado');
    }

    /** DELETE /usuarios/{id} */
    public function destroy(string $id): void
    {
        // Impede que o administrador exclua a própria conta enquanto está logado.
        if (Auth::user()['id'] == $id) {
            $this->redirect('/usuarios?success=erro_autoexclusao');
            return;
        }

        Usuario::delete((int) $id);
        $this->redirect('/usuarios?success=excluido');
    }

    /**
     * @return array{0: array, 1: string[]}
     */
    private function validate(array $input, ?int $editingId): array
    {
        $nome   = trim($input['nome'] ?? '');
        $email  = trim($input['email'] ?? '');
        $senha  = (string) ($input['senha'] ?? '');
        $perfil = $input['perfil'] ?? '';

        $errors = [];
        $perfisValidos = ['administrador', 'bibliotecario', 'leitor'];

        if ($nome === '') {
            $errors[] = 'O nome é obrigatório.';
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Informe um e-mail válido.';
        } elseif (Usuario::emailExists($email, $editingId)) {
            $errors[] = 'Este e-mail já está cadastrado para outro usuário.';
        }

        if (!in_array($perfil, $perfisValidos, true)) {
            $errors[] = 'Selecione um perfil válido.';
        }

        // Na criação, a senha é obrigatória. Na edição, é opcional
        // (deixar em branco mantém a senha atual).
        if ($editingId === null && $senha === '') {
            $errors[] = 'A senha é obrigatória.';
        } elseif ($senha !== '' && strlen($senha) < 6) {
            $errors[] = 'A senha deve ter ao menos 6 caracteres.';
        }

        return [compact('nome', 'email', 'senha', 'perfil'), $errors];
    }
}
