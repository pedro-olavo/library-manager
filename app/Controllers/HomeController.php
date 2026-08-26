<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Emprestimo;
use App\Models\Livro;

class HomeController extends Controller
{
    public function __construct()
    {
        Auth::requireLogin();
    }

    /**
     * Página inicial do sistema (dashboard simplificado).
     */
    public function index(): void
    {
        Emprestimo::markOverdue();

        $this->render('home/index', [
            'title' => 'Início',
            'totalLivros' => Livro::count(),
            'totalEmprestimosAtivos' => Emprestimo::countAtivos(),
        ]);
    }
}
