<?php
class Controller
{
    protected function view(string $view, array $data = [], string $layout = 'main'): void
    {
        if ($layout === 'main') { $data['navCategories'] = Category::active(); }
        extract($data);
        ob_start();
        require APP . "/views/$view.php";
        $content = ob_get_clean();
        require APP . "/views/layouts/$layout.php";
        unset($_SESSION['old'], $_SESSION['errors']);
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . url($path));
        exit;
    }

    protected function back(string $fallback = '/'): void
    {
        $ref = $_SERVER['HTTP_REFERER'] ?? '';
        $host = parse_url($ref, PHP_URL_HOST);
        $cur = explode(':', $_SERVER['HTTP_HOST'] ?? '')[0];
        if ($ref && $host === $cur) { header('Location: ' . $ref); exit; }
        $this->redirect($fallback);
    }

    protected function input(string $key, string $default = ''): string
    {
        $v = $_POST[$key] ?? $_GET[$key] ?? $default;
        return is_array($v) ? $default : trim((string) $v);
    }

    protected function failWith(array $errors, string $to): void
    {
        $_SESSION['old'] = $_POST;
        $_SESSION['errors'] = $errors;
        flash('error', implode(' ', array_values($errors)));
        $this->redirect($to);
    }

    public function notFound(): void
    {
        http_response_code(404);
        $this->view('errors/404', ['title' => '404']);
    }

    public function forbidden(): void
    {
        http_response_code(403);
        $this->view('errors/403', ['title' => '403']);
    }
}
