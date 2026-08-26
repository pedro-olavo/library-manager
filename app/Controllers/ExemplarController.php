<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Exemplar;
use App\Models\Livro;
use PDOException;

/**
 * Gerencia os exemplares (cópias físicas) de um livro.
 * Acessado a partir da própria tela de detalhes do livro (livros/show.php).
 */
class ExemplarController extends Controller
{
    public function __construct()
    {
        Auth::requireRole(['administrador', 'bibliotecario']);
    }

    /**
     * Cadastra um novo exemplar para um livro.
     * POST /livros/{id}/exemplares
     */
    public function store(string $id): void
    {
        if (Livro::find((int) $id) === null) {
            http_response_code(404);
            $this->render('errors/404', ['title' => 'Não encontrado']);
            return;
        }

        $codigo = trim($_POST['codigo_patrimonio'] ?? '');

        Exemplar::create((int) $id, $codigo);
        $this->redirect("/livros/{$id}?success=exemplar_criado");
    }

    /**
     * Remove um exemplar.
     * DELETE /exemplares/{id}
     */
    public function destroy(string $id): void
    {
        $exemplar = Exemplar::find((int) $id);

        if ($exemplar === null) {
            http_response_code(404);
            $this->render('errors/404', ['title' => 'Não encontrado']);
            return;
        }

        try {
            Exemplar::delete((int) $id);
            $this->redirect("/livros/{$exemplar['livro_id']}?success=exemplar_excluido");
        } catch (PDOException $e) {
            // Exemplar possui empréstimos associados — exclusão bloqueada pela chave estrangeira.
            $this->redirect("/livros/{$exemplar['livro_id']}?error=exemplar_em_uso");
        }
    }
}
