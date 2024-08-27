<?php

namespace App;
use App\Controllers\LoginController;

class Router
{
    private $routes = [];

    public function addRoute($method, $url, $login_required, $category, $permissions, $controller, $action)
    {
        // Transformar URL em regex e identificar parâmetros dinâmicos
        $url = rtrim($url, '/') . '/';
        $url = preg_replace('/\//', '\\/', $url);
        $url = preg_replace('/\{(\w+)\}/', '(?P<$1>[^\/]+)', $url);
        $url = '/^' . $url . '$/';

        $this->routes[] = [
            'method' => $method,
            'url' => $url,
            'login_required' => $login_required,
            'category' => $category,
            'permissions' => $permissions,
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function handleRequest()
    {
        $middleware = new Middleware();
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = rtrim($uri, '/') . '/';

        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            if ($route['method'] == $method and preg_match($route['url'], $uri, $matches)) {

                if (!$middleware->is_authenticated() and $route['login_required']):
                    header("Location: /");
                    return;
                elseif(isset($_SESSION['authenticated']) and $route['category'] === 'login'):
                    header("Location: /dashboard/");
                    return;
                elseif(isset($_SESSION['active']) and !$_SESSION['active']):
                    $loginController = new LoginController();
                    $loginController->logout();
                    return;
                elseif(is_array($route['permissions']) and !in_array($_SESSION['permission_id'], $route['permissions'])):
                    header("Location: /dashboard/");
                    return;
                endif;

                $controllerName = $route['controller'];
                $controller = new $controllerName();

                // Remover itens que não são parâmetros da URL
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                call_user_func_array([$controller, $route['action']], $params);

                return;
            }
        }

        header("Location: /error/404/");
    }
}
