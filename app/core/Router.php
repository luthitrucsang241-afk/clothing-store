<?php
class Router
{
    private array $routes = [];

    public function get(string $p, string $h, array $mw = []): void  { $this->add('GET', $p, $h, $mw); }
    public function post(string $p, string $h, array $mw = []): void { $this->add('POST', $p, $h, $mw); }

    private function add(string $method, string $path, string $handler, array $mw): void
    {
        $re = '#^' . preg_replace('#\{(\w+)\}#', '([^/]+)', $path) . '$#';
        $this->routes[] = [$method, $re, $handler, $mw];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = rawurldecode((string) parse_url($uri, PHP_URL_PATH));
        $base = base_url();
        foreach ([$base, dirname($base)] as $b) {
            if ($b !== '' && $b !== '.' && $b !== '/' && strpos($path, $b) === 0) { $path = substr($path, strlen($b)); break; }
        }
        $path = '/' . trim($path, '/');
        if ($path === '/index.php') { $path = '/'; }

        foreach ($this->routes as [$m, $re, $handler, $mw]) {
            if ($m !== $method || !preg_match($re, $path, $match)) { continue; }
            array_shift($match);

            foreach ($mw as $rule) {
                if ($rule === 'auth')      { AuthMiddleware::handle(); }
                elseif ($rule === 'admin') { RoleMiddleware::handle(['admin']); }
                elseif ($rule === 'dev')   { RoleMiddleware::handle(['developer', 'admin']); }
            }
            if ($method === 'POST' && !csrf_check()) {
                http_response_code(419);
                exit('Phiên làm việc hết hạn hoặc token không hợp lệ. Vui lòng quay lại và thử lại.');
            }
            [$class, $action] = explode('@', $handler);
            (new $class())->$action(...$match);
            return;
        }
        (new Controller())->notFound();
    }
}
