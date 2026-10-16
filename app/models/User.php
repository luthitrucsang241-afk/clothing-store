<?php
class User extends Model
{
    const REMEMBER_COOKIE = 'noirva_remember';
    const REMEMBER_DAYS = 30;

    public static function byEmail(string $email): ?array
    {
        return self::one("SELECT u.*, r.name AS role_name FROM users u JOIN roles r ON r.id=u.role_id WHERE u.email=?", [$email]);
    }
    public static function byId(int $id): ?array
    {
        return self::one("SELECT u.*, r.name AS role_name FROM users u JOIN roles r ON r.id=u.role_id WHERE u.id=?", [$id]);
    }
    /** Tìm tài khoản theo SĐT (chấp nhận 0xxxxxxxxx hoặc +84xxxxxxxxx). Chỉ trả về khi có đúng 1 tài khoản. */
    public static function byPhone(string $phone): ?array
    {
        $p = preg_replace('/[\s.\-]/', '', $phone);
        if (strpos($p, '+84') === 0) { $p = '0' . substr($p, 3); }
        if (!preg_match('/^0\d{9,10}$/', $p)) { return null; }
        $rows = self::all("SELECT u.*, r.name AS role_name FROM users u JOIN roles r ON r.id=u.role_id WHERE u.phone IN (?, ?)", [$p, '+84' . substr($p, 1)]);
        return count($rows) === 1 ? $rows[0] : null;
    }
    public static function login(array $u): void
    {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => (int) $u['id'], 'name' => $u['full_name'], 'email' => $u['email'], 'role' => $u['role_name']];
    }

    // ---------- Ghi nhớ đăng nhập (cookie selector:validator, chỉ lưu bản băm trong DB) ----------
    private static function cookieSecure(): bool { return !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'; }
    private static function setCookie(string $value, int $expires): void
    {
        setcookie(self::REMEMBER_COOKIE, $value, ['expires' => $expires, 'path' => '/', 'secure' => self::cookieSecure(), 'httponly' => true, 'samesite' => 'Lax']);
    }
    public static function remember(int $userId): void
    {
        try {
            $selector = bin2hex(random_bytes(12)); $validator = bin2hex(random_bytes(32));
            $exp = time() + self::REMEMBER_DAYS * 86400;
            self::exec("DELETE FROM remember_tokens WHERE expires_at < NOW()");
            self::insert('remember_tokens', ['user_id' => $userId, 'selector' => $selector, 'validator_hash' => hash('sha256', $validator), 'expires_at' => date('Y-m-d H:i:s', $exp)]);
            self::setCookie($selector . ':' . $validator, $exp);
        } catch (Throwable $e) { /* chưa chạy migration thì bỏ qua, không làm hỏng đăng nhập */ }
    }
    /** Gọi mỗi request: nếu chưa đăng nhập mà có cookie hợp lệ thì tự đăng nhập lại. */
    public static function restore(): void
    {
        if (!empty($_SESSION['user']) || empty($_COOKIE[self::REMEMBER_COOKIE])) { return; }
        $parts = explode(':', (string) $_COOKIE[self::REMEMBER_COOKIE]);
        if (count($parts) !== 2 || !preg_match('/^[a-f0-9]{24}$/', $parts[0]) || !preg_match('/^[a-f0-9]{64}$/', $parts[1])) { self::setCookie('', time() - 3600); return; }
        try {
            $row = self::one("SELECT * FROM remember_tokens WHERE selector=? AND expires_at>NOW()", [$parts[0]]);
            if ($row && hash_equals($row['validator_hash'], hash('sha256', $parts[1]))) {
                $u = self::byId((int) $row['user_id']);
                if ($u && $u['status']) {
                    $keep = $_SESSION['lang'] ?? null; $cart = $_SESSION['cart'] ?? null;
                    self::login($u);
                    if ($keep) { $_SESSION['lang'] = $keep; }
                    if ($cart) { $_SESSION['cart'] = $cart; }
                    // Đổi validator mỗi lần dùng (xoay vòng token)
                    self::exec("DELETE FROM remember_tokens WHERE id=?", [$row['id']]);
                    self::remember((int) $u['id']);
                    return;
                }
            }
            if ($row) { self::exec("DELETE FROM remember_tokens WHERE id=?", [$row['id']]); }
        } catch (Throwable $e) { /* bảng chưa tồn tại */ }
        self::setCookie('', time() - 3600);
    }
    public static function forget(): void
    {
        if (!empty($_COOKIE[self::REMEMBER_COOKIE])) {
            $parts = explode(':', (string) $_COOKIE[self::REMEMBER_COOKIE]);
            try { self::exec("DELETE FROM remember_tokens WHERE selector=?", [$parts[0]]); } catch (Throwable $e) {}
        }
        self::setCookie('', time() - 3600);
    }
}
