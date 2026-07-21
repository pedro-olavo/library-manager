<?php

/**
 * Autoloader simples para o namespace App\.
 *
 * Mapeia App\Core\Router        -> app/Core/Router.php
 * Mapeia App\Controllers\HomeController -> app/Controllers/HomeController.php
 *
 * Caso o projeto passe a usar o Composer (recomendado para dependências
 * futuras), este arquivo pode ser substituído por vendor/autoload.php,
 * já que o composer.json na raiz do projeto já define o mesmo mapeamento
 * PSR-4 (App\ -> app/).
 */
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $relativePath = str_replace('\\', '/', $relativeClass) . '.php';
    $file = __DIR__ . '/' . $relativePath;

    if (file_exists($file)) {
        require $file;
    }
});
