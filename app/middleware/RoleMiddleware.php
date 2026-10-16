<?php
class RoleMiddleware
{
    public static function handle(array $roles): void
    {
        AuthMiddleware::handle();
        if (!in_array($_SESSION['user']['role'], $roles, true)) {
            (new Controller())->forbidden();
            exit;
        }
    }
}
