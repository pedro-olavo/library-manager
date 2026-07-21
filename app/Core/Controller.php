<?php

namespace App\Core;

/**
 * Controller base do qual todos os Controllers da aplicação herdam.
 *
 * Fornece o método render(), responsável por carregar uma view dentro
 * do layout principal, e o método redirect(), utilizado para
 * redirecionamentos após ações (ex: salvar um formulário).
 */
abstract class Controller
{
    /**
     * Renderiza uma view dentro do layout padrão.
     *
     * @param string $view Caminho da view relativo a app/Views, sem extensão.
     *                      Ex: 'livros/index' -> app/Views/livros/index.php
     * @param array  $data Dados a serem extraídos como variáveis na view.
     */
    protected function render(string $view, array $data = []): void
    {
        extract($data);

        $viewPath = dirname(__DIR__) . "/Views/{$view}.php";

        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View não encontrada: {$view}");
        }

        // Captura o conteúdo da view para injetar dentro do layout.
        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        require dirname(__DIR__) . '/Views/layout.php';
    }

    /**
     * Redireciona o usuário para outra rota da aplicação.
     */
    protected function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}
