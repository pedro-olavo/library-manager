<?php

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller
{
    /**
     * Página inicial do sistema (dashboard simplificado).
     */
    public function index(): void
    {
        $this->render('home/index', [
            'title' => 'Início',
            'totalLivros' => 4,   // valor fixo por enquanto — virá do banco a partir da Entrega 3
            'totalEmprestimosAtivos' => 2,
        ]);
    }
}
