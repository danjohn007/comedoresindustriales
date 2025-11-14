<?php
/**
 * Router Class
 * Handles URL routing with friendly URLs
 */

class Router {
    private $routes = [];
    private $baseUrl;
    
    public function __construct() {
        $this->baseUrl = BASE_URL;
    }
    
    /**
     * Add a route
     */
    public function add($method, $path, $controller, $action) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'controller' => $controller,
            'action' => $action
        ];
    }
    
    /**
     * Add GET route
     */
    public function get($path, $controller, $action) {
        $this->add('GET', $path, $controller, $action);
    }
    
    /**
     * Add POST route
     */
    public function post($path, $controller, $action) {
        $this->add('POST', $path, $controller, $action);
    }
    
    /**
     * Dispatch the request
     */
    public function dispatch() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = $_SERVER['REQUEST_URI'];
        
        // Remove query string
        $requestUri = strtok($requestUri, '?');
        
        // Remove base path from URI
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $basePath = str_replace('/index.php', '', $scriptName);
        $requestPath = str_replace($basePath, '', $requestUri);
        
        // Remove trailing slash except for root
        if ($requestPath !== '/' && substr($requestPath, -1) === '/') {
            $requestPath = substr($requestPath, 0, -1);
        }
        
        // Find matching route
        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod) {
                $pattern = $this->convertToRegex($route['path']);
                
                if (preg_match($pattern, $requestPath, $matches)) {
                    array_shift($matches); // Remove full match
                    
                    // Load controller
                    $controllerName = $route['controller'];
                    $controllerFile = APP_PATH . '/controllers/' . $controllerName . '.php';
                    
                    if (file_exists($controllerFile)) {
                        require_once $controllerFile;
                        
                        $controller = new $controllerName();
                        $action = $route['action'];
                        
                        if (method_exists($controller, $action)) {
                            call_user_func_array([$controller, $action], $matches);
                            return;
                        } else {
                            $this->error404("Method $action not found in $controllerName");
                            return;
                        }
                    } else {
                        $this->error404("Controller file not found: $controllerFile");
                        return;
                    }
                }
            }
        }
        
        // No route found
        $this->error404("Route not found: $requestMethod $requestPath");
    }
    
    /**
     * Convert route path to regex pattern
     */
    private function convertToRegex($path) {
        // Convert :param to regex capture group
        $pattern = preg_replace('/\/:([^\/]+)/', '/([^/]+)', $path);
        return '#^' . $pattern . '$#';
    }
    
    /**
     * 404 Error handler
     */
    private function error404($message = "Page not found") {
        http_response_code(404);
        require_once APP_PATH . '/views/errors/404.php';
        exit;
    }
    
    /**
     * Generate URL
     */
    public static function url($path = '') {
        return BASE_URL . $path;
    }
}
