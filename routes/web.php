<?php

/**
 * Definição de todas as rotas da aplicação.
 * A variável $router já está disponível (instanciada em public/index.php).
 */

use App\Core\Router;

/** @var Router $router */

// Início
$router->get('/', 'HomeController', 'index');

// Livros
$router->get('/livros', 'LivroController', 'index');
$router->get('/livros/novo', 'LivroController', 'create');
$router->post('/livros', 'LivroController', 'store');
$router->get('/livros/{id}', 'LivroController', 'show');

// As rotas de edição/exclusão (PUT /livros/{id} e DELETE /livros/{id})
// serão adicionadas na Entrega Parcial 4, junto da implementação do
// CRUD completo (update e delete).
