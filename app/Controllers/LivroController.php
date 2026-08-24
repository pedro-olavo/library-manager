<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Autor;
use App\Models\Categoria;
use App\Models\Editora;
use App\Models\Livro;

/**
 * Controller responsável pelo gerenciamento de Livros.
 *
 * Entrega Parcial 3: Create (cadastro) e Read (listagem) via PDO.
 * Entrega Parcial 4: Update (edição) e Delete (exclusão) via PDO,
 * completando o CRUD da entidade principal do sistema.
 */
class LivroController extends Controller
{
    /**
     * Lista os livros cadastrados.
     * GET /livros
     */
    public function index(): void
    {
        $livros = Livro::all();

        $this->render('livros/index', [
            'title'   => 'Livros',
            'livros'  => $livros,
            'success' => $_GET['success'] ?? null,
        ]);
    }

    /**
     * Exibe os detalhes de um livro específico.
     * GET /livros/{id}
     */
    public function show(string $id): void
    {
        $livro = Livro::find((int) $id);

        if ($livro === null) {
            http_response_code(404);
            $this->render('errors/404', ['title' => 'Não encontrado']);
            return;
        }

        $this->render('livros/show', [
            'title' => $livro['titulo'],
            'livro' => $livro,
        ]);
    }

    /**
     * Exibe o formulário de cadastro de um novo livro.
     * GET /livros/novo
     */
    public function create(): void
    {
        $this->render('livros/form', array_merge($this->formLookups(), [
            'title'  => 'Novo Livro',
            'livro'  => null,
            'errors' => [],
            'old'    => [],
        ]));
    }

    /**
     * Recebe os dados do formulário e cadastra um novo livro no banco.
     * POST /livros
     */
    public function store(): void
    {
        [$data, $errors] = $this->validate($_POST);

        if (!empty($errors)) {
            $this->render('livros/form', array_merge($this->formLookups(), [
                'title'  => 'Novo Livro',
                'livro'  => null,
                'errors' => $errors,
                'old'    => $_POST,
            ]));
            return;
        }

        try {
            Livro::create($data);
        } catch (\Throwable $e) {
            $this->render('livros/form', array_merge($this->formLookups(), [
                'title'  => 'Novo Livro',
                'livro'  => null,
                'errors' => ['Não foi possível salvar o livro: ' . $e->getMessage()],
                'old'    => $_POST,
            ]));
            return;
        }

        $this->redirect('/livros?success=criado');
    }

    /**
     * Exibe o formulário de edição de um livro existente.
     * GET /livros/{id}/editar
     */
    public function edit(string $id): void
    {
        $livro = Livro::find((int) $id);

        if ($livro === null) {
            http_response_code(404);
            $this->render('errors/404', ['title' => 'Não encontrado']);
            return;
        }

        $this->render('livros/form', array_merge($this->formLookups(), [
            'title'  => 'Editar Livro',
            'livro'  => $livro,
            'errors' => [],
            'old'    => [],
        ]));
    }

    /**
     * Atualiza um livro existente no banco.
     * PUT /livros/{id}
     */
    public function update(string $id): void
    {
        $livroAtual = Livro::find((int) $id);

        if ($livroAtual === null) {
            http_response_code(404);
            $this->render('errors/404', ['title' => 'Não encontrado']);
            return;
        }

        [$data, $errors] = $this->validate($_POST);

        if (!empty($errors)) {
            $this->render('livros/form', array_merge($this->formLookups(), [
                'title'  => 'Editar Livro',
                'livro'  => array_merge($livroAtual, ['id' => $id]),
                'errors' => $errors,
                'old'    => $_POST,
            ]));
            return;
        }

        try {
            Livro::update((int) $id, $data);
        } catch (\Throwable $e) {
            $this->render('livros/form', array_merge($this->formLookups(), [
                'title'  => 'Editar Livro',
                'livro'  => array_merge($livroAtual, ['id' => $id]),
                'errors' => ['Não foi possível atualizar o livro: ' . $e->getMessage()],
                'old'    => $_POST,
            ]));
            return;
        }

        $this->redirect('/livros?success=atualizado');
    }

    /**
     * Remove um livro do banco.
     * DELETE /livros/{id}
     */
    public function destroy(string $id): void
    {
        Livro::delete((int) $id);
        $this->redirect('/livros?success=excluido');
    }

    /**
     * Valida os dados enviados pelo formulário de livro (usado por store e update).
     *
     * @return array{0: array, 1: string[]} Tupla [dados normalizados, lista de erros]
     */
    private function validate(array $input): array
    {
        $titulo        = trim($input['titulo'] ?? '');
        $isbn          = trim($input['isbn'] ?? '');
        $anoPublicacao = trim($input['ano_publicacao'] ?? '');
        $categoriaId   = $input['categoria_id'] ?? '';
        $editoraId     = $input['editora_id'] ?? '';
        $autorId       = $input['autor_id'] ?? '';

        $errors = [];

        if ($titulo === '') {
            $errors[] = 'O título é obrigatório.';
        }

        if ($anoPublicacao !== '' && !preg_match('/^\d{1,4}$/', $anoPublicacao)) {
            $errors[] = 'Informe um ano de publicação válido.';
        }

        if ($autorId === '') {
            $errors[] = 'Selecione o autor do livro.';
        }

        $data = [
            'titulo'         => $titulo,
            'isbn'           => $isbn,
            'ano_publicacao' => $anoPublicacao,
            'categoria_id'   => $categoriaId,
            'editora_id'     => $editoraId,
            'autor_id'       => $autorId,
        ];

        return [$data, $errors];
    }

    /**
     * Listas usadas para popular os dropdowns do formulário (autor, categoria, editora).
     */
    private function formLookups(): array
    {
        return [
            'categorias' => Categoria::all(),
            'editoras'   => Editora::all(),
            'autores'    => Autor::all(),
        ];
    }
}
