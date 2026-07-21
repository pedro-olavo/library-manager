<?php

namespace App\Core;

/**
 * Router simples baseado em expressões regulares.
 *
 * Responsável por:
 *  - Registrar rotas (GET, POST, PUT, DELETE);
 *  - Casar a URL requisitada com uma rota cadastrada;
 *  - Extrair parâmetros dinâmicos (ex: /livros/{id});
 *  - Instanciar o Controller correspondente e chamar a Action.
 */
class Router
{
    /** @var array<int, array{method:string, pattern:string, regex:string, controller:string, action:string}> */
    private array $routes = [];

    public function get(string $path, string $controller, string $action): void
    {
        $this->addRoute('GET', $path, $controller, $action);
    }

    public function post(string $path, string $controller, string $action): void
    {
        $this->addRoute('POST', $path, $controller, $action);
    }

    public function put(string $path, string $controller, string $action): void
    {
        $this->addRoute('PUT', $path, $controller, $action);
    }

    public function delete(string $path, string $controller, string $action): void
    {
        $this->addRoute('DELETE', $path, $controller, $action);
    }

    private function addRoute(string $method, string $path, string $controller, string $action): void
    {
        // Converte {param} em grupo de captura nomeado no regex da rota.
        $regex = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', trim($path, '/'));
        $regex = '#^' . $regex . '$#';

        $this->routes[] = [
            'method'     => $method,
            'pattern'    => $path,
            'regex'      => $regex,
            'controller' => $controller,
            'action'     => $action,
        ];
    }

    /**
     * Resolve a rota atual a partir do método HTTP e da URI, e despacha
     * a requisição para o Controller/Action correspondente.
     */
    public function dispatch(string $method, string $uri): void
    {
        // Remove query string e barras extras da URI.
        $uri = parse_url($uri, PHP_URL_PATH) ?? '/';
        $uri = trim($uri, '/');

        // Permite que formulários HTML simulem PUT/DELETE via campo _method.
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['regex'], $uri, $matches)) {
                $params = array_filter(
                    $matches,
                    fn ($key) => is_string($key),
                    ARRAY_FILTER_USE_KEY
                );

                $this->callAction($route['controller'], $route['action'], $params);
                return;
            }
        }

        $this->notFound();
    }

    private function callAction(string $controller, string $action, array $params): void
    {
        $controllerClass = "App\\Controllers\\{$controller}";

        if (!class_exists($controllerClass)) {
            $this->notFound();
            return;
        }

        $instance = new $controllerClass();

        if (!method_exists($instance, $action)) {
            $this->notFound();
            return;
        }

        call_user_func_array([$instance, $action], $params);
    }

    private function notFound(): void
    {
        http_response_code(404);

        $title = 'Página não encontrada';
        ob_start();
        require dirname(__DIR__) . '/Views/errors/404.php';
        $content = ob_get_clean();

        require dirname(__DIR__) . '/Views/layout.php';
    }
}
