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
 * Entrega Parcial 3: Create (cadastro) e Read (listagem) já consultam
 * o banco de dados PostgreSQL via PDO, através dos Models em App\Models.
 * Update e Delete serão implementados na Entrega Parcial 4.
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
        $this->render('livros/create', [
            'title'      => 'Novo Livro',
            'categorias' => Categoria::all(),
            'editoras'   => Editora::all(),
            'autores'    => Autor::all(),
            'errors'     => [],
            'old'        => [],
        ]);
    }

    /**
     * Recebe os dados do formulário e cadastra um novo livro no banco.
     * POST /livros
     */
    public function store(): void
    {
        $titulo        = trim($_POST['titulo'] ?? '');
        $isbn          = trim($_POST['isbn'] ?? '');
        $anoPublicacao = trim($_POST['ano_publicacao'] ?? '');
        $categoriaId   = $_POST['categoria_id'] ?? '';
        $editoraId     = $_POST['editora_id'] ?? '';
        $autorId       = $_POST['autor_id'] ?? '';

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

        if (!empty($errors)) {
            $this->render('livros/create', [
                'title'      => 'Novo Livro',
                'categorias' => Categoria::all(),
                'editoras'   => Editora::all(),
                'autores'    => Autor::all(),
                'errors'     => $errors,
                'old'        => $_POST,
            ]);
            return;
        }

        try {
            Livro::create([
                'titulo'         => $titulo,
                'isbn'           => $isbn,
                'ano_publicacao' => $anoPublicacao,
                'categoria_id'   => $categoriaId,
                'editora_id'     => $editoraId,
                'autor_id'       => $autorId,
            ]);
        } catch (\Throwable $e) {
            $this->render('livros/create', [
                'title'      => 'Novo Livro',
                'categorias' => Categoria::all(),
                'editoras'   => Editora::all(),
                'autores'    => Autor::all(),
                'errors'     => ['Não foi possível salvar o livro: ' . $e->getMessage()],
                'old'        => $_POST,
            ]);
            return;
        }

        $this->redirect('/livros?success=1');
    }
}
