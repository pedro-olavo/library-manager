<?php

namespace App\Controllers;

use App\Core\Controller;

/**
 * Controller responsável pelo gerenciamento de Livros.
 *
 * Nesta etapa (Entrega Parcial 2), o objetivo é validar a estrutura MVC
 * e o sistema de rotas. Por isso, os dados exibidos ainda são estáticos
 * (mock). A partir da Entrega Parcial 3, os métodos index() e show()
 * passarão a consultar o banco de dados PostgreSQL via PDO (App\Core\Database),
 * e os métodos create()/store()/edit()/update()/delete() serão implementados
 * por completo (Entrega Parcial 4).
 */
class LivroController extends Controller
{
    /** Dados temporários utilizados apenas para validar as views. */
    private function mockLivros(): array
    {
        return [
            ['id' => 1, 'titulo' => 'Dom Casmurro', 'autor' => 'Machado de Assis', 'categoria' => 'Romance', 'ano' => 1899, 'status' => 'Disponível'],
            ['id' => 2, 'titulo' => '1984', 'autor' => 'George Orwell', 'categoria' => 'Ficção', 'ano' => 1949, 'status' => 'Emprestado'],
            ['id' => 3, 'titulo' => 'Clean Code', 'autor' => 'Robert C. Martin', 'categoria' => 'Tecnologia', 'ano' => 2008, 'status' => 'Disponível'],
            ['id' => 4, 'titulo' => 'O Cortiço', 'autor' => 'Aluísio Azevedo', 'categoria' => 'Romance', 'ano' => 1890, 'status' => 'Reservado'],
        ];
    }

    /**
     * Lista os livros cadastrados.
     * GET /livros
     */
    public function index(): void
    {
        $this->render('livros/index', [
            'title'  => 'Livros',
            'livros' => $this->mockLivros(),
        ]);
    }

    /**
     * Exibe os detalhes de um livro específico.
     * GET /livros/{id}
     */
    public function show(string $id): void
    {
        $livros = $this->mockLivros();
        $livro = null;

        foreach ($livros as $item) {
            if ((int) $item['id'] === (int) $id) {
                $livro = $item;
                break;
            }
        }

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
            'title' => 'Novo Livro',
        ]);
    }

    /**
     * Recebe os dados do formulário e cadastra um novo livro.
     * POST /livros
     *
     * Implementação completa prevista para a Entrega Parcial 3/4,
     * quando a conexão com o banco (App\Core\Database) for utilizada.
     */
    public function store(): void
    {
        // TODO (Entrega Parcial 3): validar dados e inserir via PDO.
        $this->redirect('/livros');
    }
}
