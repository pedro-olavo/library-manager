<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Livro;

class HomeController extends Controller
{
    /**
     * Página inicial do sistema (dashboard simplificado).
     */
    public function index(): void
    {
        $this->render('home/index', [
            'title' => 'Início',
            'totalLivros' => Livro::count(),
            'totalEmprestimosAtivos' => 0, // será implementado na Entrega Parcial 5 (empréstimos)
        ]);
    }
}
