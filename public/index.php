<?php

/**
 * Front Controller — ponto de entrada único da aplicação.
 * Todas as requisições HTTP são direcionadas para este arquivo
 * (ver public/.htaccess), que inicializa o autoload, o Router
 * e despacha a requisição para o Controller/Action correspondente.
 */

require dirname(__DIR__) . '/app/autoload.php';

use App\Core\Router;

$router = new Router();

require dirname(__DIR__) . '/routes/web.php';

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
