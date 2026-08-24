<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Autor;

class AutorController extends Controller
{
    public function __construct()
    {
        Auth::requireRole(['administrador', 'bibliotecario']);
    }

    /** GET /autores */
    public function index(): void
    {
        $this->render('autores/index', [
            'title'   => 'Autores',
            'autores' => Autor::all(),
            'success' => $_GET['success'] ?? null,
        ]);
    }

    /** GET /autores/novo */
    public function create(): void
    {
        $this->render('autores/form', [
            'title'  => 'Novo Autor',
            'autor'  => null,
            'errors' => [],
            'old'    => [],
        ]);
    }

    /** POST /autores */
    public function store(): void
    {
        $nome          = trim($_POST['nome'] ?? '');
        $nacionalidade = trim($_POST['nacionalidade'] ?? '');

        if ($nome === '') {
            $this->render('autores/form', [
                'title'  => 'Novo Autor',
                'autor'  => null,
                'errors' => ['O nome do autor é obrigatório.'],
                'old'    => $_POST,
            ]);
            return;
        }

        Autor::create($nome, $nacionalidade);
        $this->redirect('/autores?success=criado');
    }

    /** GET /autores/{id}/editar */
    public function edit(string $id): void
    {
        $autor = Autor::find((int) $id);

        if ($autor === null) {
            http_response_code(404);
            $this->render('errors/404', ['title' => 'Não encontrado']);
            return;
        }

        $this->render('autores/form', [
            'title'  => 'Editar Autor',
            'autor'  => $autor,
            'errors' => [],
            'old'    => [],
        ]);
    }

    /** PUT /autores/{id} */
    public function update(string $id): void
    {
        $nome          = trim($_POST['nome'] ?? '');
        $nacionalidade = trim($_POST['nacionalidade'] ?? '');

        if ($nome === '') {
            $this->render('autores/form', [
                'title'  => 'Editar Autor',
                'autor'  => ['id' => $id, 'nome' => $nome, 'nacionalidade' => $nacionalidade],
                'errors' => ['O nome do autor é obrigatório.'],
                'old'    => $_POST,
            ]);
            return;
        }

        Autor::update((int) $id, $nome, $nacionalidade);
        $this->redirect('/autores?success=atualizado');
    }

    /** DELETE /autores/{id} */
    public function destroy(string $id): void
    {
        Autor::delete((int) $id);
        $this->redirect('/autores?success=excluido');
    }
}
