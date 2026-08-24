<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
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
        $this->render('home/index', [
            'title' => 'Início',
            'totalLivros' => Livro::count(),
            'totalEmprestimosAtivos' => 0, // será implementado quando o módulo de empréstimos for adicionado
        ]);
    }
}
