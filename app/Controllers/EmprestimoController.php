<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Emprestimo;
use App\Models\Exemplar;
use App\Models\Usuario;
use RuntimeException;

/**
 * Controller responsável pelo módulo de empréstimos.
 *
 * Qualquer usuário autenticado pode ver a listagem: leitores veem apenas
 * os próprios empréstimos, enquanto administrador/bibliotecário veem todos.
 * Registrar empréstimo e devolução é restrito a administrador/bibliotecário.
 */
class EmprestimoController extends Controller
{
    public function __construct()
    {
        Auth::requireLogin();
    }

    /**
     * Lista os empréstimos.
     * GET /emprestimos
     */
    public function index(): void
    {
        Emprestimo::markOverdue();

        $ehLeitor = Auth::role() === 'leitor';
        $somenteAtivos = ($_GET['filtro'] ?? 'ativos') !== 'todos';

        $emprestimos = Emprestimo::all(
            $ehLeitor ? Auth::user()['id'] : null,
            $somenteAtivos
        );

        $this->render('emprestimos/index', [
            'title'         => 'Empréstimos',
            'emprestimos'   => $emprestimos,
            'somenteAtivos' => $somenteAtivos,
            'success'       => $_GET['success'] ?? null,
            'error'         => $_GET['error'] ?? null,
        ]);
    }

    /**
     * Exibe o formulário de registro de um novo empréstimo.
     * GET /emprestimos/novo
     */
    public function create(): void
    {
        Auth::requireRole(['administrador', 'bibliotecario']);

        $this->render('emprestimos/form', [
            'title'              => 'Registrar Empréstimo',
            'usuarios'           => Usuario::all(),
            'exemplaresDisponiveis' => Exemplar::allDisponiveis(),
            'errors'             => [],
            'old'                => [],
        ]);
    }

    /**
     * Registra um novo empréstimo.
     * POST /emprestimos
     */
    public function store(): void
    {
        Auth::requireRole(['administrador', 'bibliotecario']);

        $usuarioId = $_POST['usuario_id'] ?? '';
        $exemplarId = $_POST['exemplar_id'] ?? '';
        $dataPrevista = trim($_POST['data_prevista_devolucao'] ?? '');

        $errors = [];

        if ($usuarioId === '') {
            $errors[] = 'Selecione o usuário.';
        }

        if ($exemplarId === '') {
            $errors[] = 'Selecione o exemplar a ser emprestado.';
        }

        $timestampPrevisto = $dataPrevista !== '' ? strtotime($dataPrevista) : false;

        if ($timestampPrevisto === false) {
            $errors[] = 'Informe uma data de devolução prevista válida.';
        } elseif ($timestampPrevisto < strtotime('today')) {
            $errors[] = 'A data de devolução prevista não pode estar no passado.';
        }

        if (!empty($errors)) {
            $this->render('emprestimos/form', [
                'title'                 => 'Registrar Empréstimo',
                'usuarios'              => Usuario::all(),
                'exemplaresDisponiveis' => Exemplar::allDisponiveis(),
                'errors'                => $errors,
                'old'                   => $_POST,
            ]);
            return;
        }

        try {
            Emprestimo::create((int) $usuarioId, (int) $exemplarId, $dataPrevista);
        } catch (RuntimeException $e) {
            $this->render('emprestimos/form', [
                'title'                 => 'Registrar Empréstimo',
                'usuarios'              => Usuario::all(),
                'exemplaresDisponiveis' => Exemplar::allDisponiveis(),
                'errors'                => [$e->getMessage()],
                'old'                   => $_POST,
            ]);
            return;
        }

        $this->redirect('/emprestimos?success=registrado');
    }

    /**
     * Registra a devolução de um empréstimo.
     * POST /emprestimos/{id}/devolver
     */
    public function devolver(string $id): void
    {
        Auth::requireRole(['administrador', 'bibliotecario']);

        Emprestimo::registrarDevolucao((int) $id);
        $this->redirect('/emprestimos?success=devolvido');
    }
}
