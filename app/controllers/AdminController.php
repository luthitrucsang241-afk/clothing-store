<?php
class AdminController extends Controller
{
    protected function admin(string $view, array $data = []): void
    {
        $this->view($view, $data, 'admin');
    }
}
