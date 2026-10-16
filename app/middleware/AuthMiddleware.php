<?php
class AuthMiddleware
{
    public static function handle(): void
    {
        if (empty($_SESSION['user'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') { $_SESSION['intended'] = $_SERVER['REQUEST_URI']; }
            flash('warning', t('msg.login_required'));
            header('Location: ' . url('/login'));
            exit;
        }
    }
}
