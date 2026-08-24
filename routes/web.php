<?php

/**
 * Definição de todas as rotas da aplicação.
 * A variável $router já está disponível (instanciada em public/index.php).
 */

use App\Core\Router;

/** @var Router $router */

// Início
$router->get('/', 'HomeController', 'index');

// Autenticação
$router->get('/login', 'AuthController', 'showLogin');
$router->post('/login', 'AuthController', 'login');
$router->post('/logout', 'AuthController', 'logout');

// Usuários (gestão de perfis de acesso — apenas administrador)
$router->get('/usuarios', 'UsuarioController', 'index');
$router->get('/usuarios/novo', 'UsuarioController', 'create');
$router->post('/usuarios', 'UsuarioController', 'store');
$router->get('/usuarios/{id}/editar', 'UsuarioController', 'edit');
$router->put('/usuarios/{id}', 'UsuarioController', 'update');
$router->delete('/usuarios/{id}', 'UsuarioController', 'destroy');

// Livros
$router->get('/livros', 'LivroController', 'index');
$router->get('/livros/novo', 'LivroController', 'create');
$router->post('/livros', 'LivroController', 'store');
$router->get('/livros/{id}/editar', 'LivroController', 'edit');
$router->put('/livros/{id}', 'LivroController', 'update');
$router->delete('/livros/{id}', 'LivroController', 'destroy');
$router->get('/livros/{id}', 'LivroController', 'show');

// Autores
$router->get('/autores', 'AutorController', 'index');
$router->get('/autores/novo', 'AutorController', 'create');
$router->post('/autores', 'AutorController', 'store');
$router->get('/autores/{id}/editar', 'AutorController', 'edit');
$router->put('/autores/{id}', 'AutorController', 'update');
$router->delete('/autores/{id}', 'AutorController', 'destroy');

// Categorias
$router->get('/categorias', 'CategoriaController', 'index');
$router->get('/categorias/novo', 'CategoriaController', 'create');
$router->post('/categorias', 'CategoriaController', 'store');
$router->get('/categorias/{id}/editar', 'CategoriaController', 'edit');
$router->put('/categorias/{id}', 'CategoriaController', 'update');
$router->delete('/categorias/{id}', 'CategoriaController', 'destroy');

// Editoras
$router->get('/editoras', 'EditoraController', 'index');
$router->get('/editoras/novo', 'EditoraController', 'create');
$router->post('/editoras', 'EditoraController', 'store');
$router->get('/editoras/{id}/editar', 'EditoraController', 'edit');
$router->put('/editoras/{id}', 'EditoraController', 'update');
$router->delete('/editoras/{id}', 'EditoraController', 'destroy');
