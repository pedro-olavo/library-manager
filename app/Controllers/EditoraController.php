<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Editora;

class EditoraController extends Controller
{
    public function __construct()
    {
        Auth::requireRole(['administrador', 'bibliotecario']);
    }

    /** GET /editoras */
    public function index(): void
    {
        $this->render('editoras/index', [
            'title'    => 'Editoras',
            'editoras' => Editora::all(),
            'success'  => $_GET['success'] ?? null,
        ]);
    }

    /** GET /editoras/novo */
    public function create(): void
    {
        $this->render('editoras/form', [
            'title'   => 'Nova Editora',
            'editora' => null,
            'errors'  => [],
            'old'     => [],
        ]);
    }

    /** POST /editoras */
    public function store(): void
    {
        $nome = trim($_POST['nome'] ?? '');

        if ($nome === '') {
            $this->render('editoras/form', [
                'title'   => 'Nova Editora',
                'editora' => null,
                'errors'  => ['O nome da editora é obrigatório.'],
                'old'     => $_POST,
            ]);
            return;
        }

        Editora::create($nome);
        $this->redirect('/editoras?success=criada');
    }

    /** GET /editoras/{id}/editar */
    public function edit(string $id): void
    {
        $editora = Editora::find((int) $id);

        if ($editora === null) {
            http_response_code(404);
            $this->render('errors/404', ['title' => 'Não encontrado']);
            return;
        }

        $this->render('editoras/form', [
            'title'   => 'Editar Editora',
            'editora' => $editora,
            'errors'  => [],
            'old'     => [],
        ]);
    }

    /** PUT /editoras/{id} */
    public function update(string $id): void
    {
        $nome = trim($_POST['nome'] ?? '');

        if ($nome === '') {
            $this->render('editoras/form', [
                'title'   => 'Editar Editora',
                'editora' => ['id' => $id, 'nome' => $nome],
                'errors'  => ['O nome da editora é obrigatório.'],
                'old'     => $_POST,
            ]);
            return;
        }

        Editora::update((int) $id, $nome);
        $this->redirect('/editoras?success=atualizada');
    }

    /** DELETE /editoras/{id} */
    public function destroy(string $id): void
    {
        Editora::delete((int) $id);
        $this->redirect('/editoras?success=excluida');
    }
}
