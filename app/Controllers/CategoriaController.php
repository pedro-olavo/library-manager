<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Categoria;

class CategoriaController extends Controller
{
    /** GET /categorias */
    public function index(): void
    {
        $this->render('categorias/index', [
            'title'      => 'Categorias',
            'categorias' => Categoria::all(),
            'success'    => $_GET['success'] ?? null,
        ]);
    }

    /** GET /categorias/novo */
    public function create(): void
    {
        $this->render('categorias/form', [
            'title'      => 'Nova Categoria',
            'categoria'  => null,
            'errors'     => [],
            'old'        => [],
        ]);
    }

    /** POST /categorias */
    public function store(): void
    {
        $nome = trim($_POST['nome'] ?? '');

        if ($nome === '') {
            $this->render('categorias/form', [
                'title'     => 'Nova Categoria',
                'categoria' => null,
                'errors'    => ['O nome da categoria é obrigatório.'],
                'old'       => $_POST,
            ]);
            return;
        }

        Categoria::create($nome);
        $this->redirect('/categorias?success=criada');
    }

    /** GET /categorias/{id}/editar */
    public function edit(string $id): void
    {
        $categoria = Categoria::find((int) $id);

        if ($categoria === null) {
            http_response_code(404);
            $this->render('errors/404', ['title' => 'Não encontrado']);
            return;
        }

        $this->render('categorias/form', [
            'title'     => 'Editar Categoria',
            'categoria' => $categoria,
            'errors'    => [],
            'old'       => [],
        ]);
    }

    /** PUT /categorias/{id} */
    public function update(string $id): void
    {
        $nome = trim($_POST['nome'] ?? '');

        if ($nome === '') {
            $this->render('categorias/form', [
                'title'     => 'Editar Categoria',
                'categoria' => ['id' => $id, 'nome' => $nome],
                'errors'    => ['O nome da categoria é obrigatório.'],
                'old'       => $_POST,
            ]);
            return;
        }

        Categoria::update((int) $id, $nome);
        $this->redirect('/categorias?success=atualizada');
    }

    /** DELETE /categorias/{id} */
    public function destroy(string $id): void
    {
        Categoria::delete((int) $id);
        $this->redirect('/categorias?success=excluida');
    }
}
