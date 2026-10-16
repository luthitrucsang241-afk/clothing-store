<?php
class AuthController extends Controller
{
    public function loginForm(): void
    {
        if (user()) { $this->redirect('/'); }
        $this->view('auth/login', ['title' => t('auth.login')]);
    }

    public function login(): void
    {
        $email = strtolower($this->input('email'));
        $pass = (string) ($_POST['password'] ?? '');

        // Giới hạn thử sai: 5 lần / 5 phút
        $lock = $_SESSION['login_lock'] ?? ['n' => 0, 'until' => 0];
        if ($lock['until'] > time()) { $this->failWith(['email' => t('auth.locked')], '/login'); }

        $u = User::byEmail($email);
        if (!$u || !password_verify($pass, $u['password'])) {
            $lock['n']++;
            if ($lock['n'] >= 5) { $lock = ['n' => 0, 'until' => time() + 300]; }
            $_SESSION['login_lock'] = $lock;
            $this->failWith(['email' => t('auth.bad_credentials')], '/login');
        }
        if (!$u['status']) { $this->failWith(['email' => t('auth.account_locked')], '/login'); }

        unset($_SESSION['login_lock']);
        $keepCart = $_SESSION['cart'] ?? []; $lang = $_SESSION['lang'] ?? null;
        User::login($u);
        if (!empty($_POST['remember'])) { User::remember((int) $u['id']); } else { User::forget(); }
        $_SESSION['cart'] = $keepCart;
        if ($lang) { $_SESSION['lang'] = $lang; }
        flash('success', t('auth.welcome', $u['full_name']));

        $go = $_SESSION['intended'] ?? null; unset($_SESSION['intended']);
        if ($u['role_name'] === 'user' && $go && $go[0] === '/' && substr($go, 0, 2) !== '//') { header('Location: ' . $go); exit; }
        $this->redirect(['admin' => '/admin', 'developer' => '/developer'][$u['role_name']] ?? '/');
    }

    public function registerForm(): void
    {
        if (user()) { $this->redirect('/'); }
        $this->view('auth/register', ['title' => t('auth.register')]);
    }

    public function register(): void
    {
        $name = $this->input('full_name'); $email = strtolower($this->input('email')); $phone = $this->input('phone');
        $pass = (string) ($_POST['password'] ?? ''); $conf = (string) ($_POST['password_confirm'] ?? '');
        $e = [];
        if (str_len($name) < 2 || str_len($name) > 120) { $e['full_name'] = t('val.name'); }
        if (!valid_email($email)) { $e['email'] = t('msg.invalid_email'); }
        elseif (User::byEmail($email)) { $e['email'] = t('val.email_taken'); }
        if ($phone !== '' && !valid_phone($phone)) { $e['phone'] = t('val.phone'); }
        if (!valid_password($pass)) { $e['password'] = t('val.password'); }
        elseif ($pass !== $conf) { $e['password_confirm'] = t('val.password_match'); }
        if ($e) { $this->failWith($e, '/register'); }

        Model::insert('users', ['full_name' => $name, 'email' => $email, 'phone' => $phone ?: null,
            'password' => password_hash($pass, PASSWORD_DEFAULT), 'role_id' => 1, 'membership_level_id' => 1, 'status' => 1]);
        flash('success', t('auth.registered'));
        $this->redirect('/login');
    }

    public function logout(): void
    {
        User::forget();
        $lang = $_SESSION['lang'] ?? null;
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        session_start();
        if ($lang) { $_SESSION['lang'] = $lang; }
        flash('success', t('auth.logged_out'));
        $this->redirect('/');
    }

    public function forgotForm(): void { $this->view('auth/forgot-password', ['title' => t('auth.forgot')]); }

    /** Chặn gửi liên tục: tối thiểu 60 giây giữa 2 lần yêu cầu trên cùng một phiên. */
    private function throttleReset(): void
    {
        $last = $_SESSION['reset_last'] ?? 0;
        if (time() - $last < 60) { $this->failWith(['identifier' => t('auth.reset_wait')], '/forgot-password'); }
        $_SESSION['reset_last'] = time();
    }

    public function forgot(): void
    {
        $method = $this->input('method') === 'phone' ? 'phone' : 'email';
        $ident = $method === 'phone' ? $this->input('phone') : strtolower($this->input('email'));
        $_SESSION['old'] = ['method' => $method, 'email' => $this->input('email'), 'phone' => $this->input('phone')];

        if ($method === 'email' && !valid_email($ident)) { $this->failWith(['email' => t('msg.invalid_email')], '/forgot-password'); }
        if ($method === 'phone' && !valid_phone(preg_replace('/[\s.\-]/', '', $ident))) { $this->failWith(['phone' => t('val.phone')], '/forgot-password'); }
        $this->throttleReset();

        $u = $method === 'phone' ? User::byPhone($ident) : User::byEmail($ident);
        if ($u && $u['status']) {
            // Vô hiệu các yêu cầu cũ chưa dùng của tài khoản này
            Model::exec("UPDATE password_resets SET used_at=NOW() WHERE user_id=? AND used_at IS NULL", [$u['id']]);
            if ($method === 'email') {
                $token = bin2hex(random_bytes(32));
                Model::insert('password_resets', ['user_id' => $u['id'], 'token_hash' => hash('sha256', $token), 'channel' => 'email', 'expires_at' => date('Y-m-d H:i:s', time() + 3600)]);
                $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . url('/reset-password?token=' . $token);
                send_mail($u['email'], 'password_reset', 'Đặt lại mật khẩu NOIRVA', "Xin chào {$u['full_name']},\n\nBạn vừa yêu cầu đặt lại mật khẩu NOIRVA. Mở liên kết sau (hiệu lực 1 giờ):\n$link\n\nNếu không phải bạn, hãy bỏ qua email này.");
                if (config('debug')) { flash('info', 'DEBUG: ' . $link); }
            } else {
                $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                Model::insert('password_resets', ['user_id' => $u['id'], 'token_hash' => hash('sha256', $otp . ':' . $u['id']), 'channel' => 'phone', 'expires_at' => date('Y-m-d H:i:s', time() + 600)]);
                send_sms($u['phone'], "NOIRVA: Ma xac nhan dat lai mat khau cua ban la $otp (hieu luc 10 phut). Khong chia se ma nay cho bat ky ai.");
                if (config('debug')) { flash('info', 'DEBUG OTP: ' . $otp); }
            }
        }
        // Luôn trả lời giống nhau để không lộ email/SĐT nào đã đăng ký
        if ($method === 'phone') {
            $_SESSION['reset_phone'] = $ident;
            unset($_SESSION['old']);
            flash('success', t('auth.otp_sent'));
            $this->redirect('/reset-otp');
        }
        unset($_SESSION['old']);
        flash('success', t('auth.reset_sent'));
        $this->redirect('/forgot-password');
    }

    // ----- Đặt lại bằng liên kết email -----
    private function resetRow(string $token): ?array
    {
        if (!preg_match('/^[a-f0-9]{64}$/', $token)) { return null; }
        return Model::one("SELECT * FROM password_resets WHERE token_hash=? AND channel='email' AND used_at IS NULL AND expires_at>NOW()", [hash('sha256', $token)]);
    }

    public function resetForm(): void
    {
        $token = $this->input('token');
        if (!$this->resetRow($token)) { flash('error', t('auth.token_invalid')); $this->redirect('/forgot-password'); }
        $this->view('auth/reset-password', ['title' => t('auth.reset'), 'token' => $token]);
    }

    public function reset(): void
    {
        $token = $this->input('token');
        $row = $this->resetRow($token);
        if (!$row) { flash('error', t('auth.token_invalid')); $this->redirect('/forgot-password'); }
        $pass = (string) ($_POST['password'] ?? ''); $conf = (string) ($_POST['password_confirm'] ?? '');
        if (!valid_password($pass)) { $this->failWith(['password' => t('val.password')], '/reset-password?token=' . $token); }
        if ($pass !== $conf) { $this->failWith(['password_confirm' => t('val.password_match')], '/reset-password?token=' . $token); }
        $this->finishReset($row, $pass);
    }

    // ----- Đặt lại bằng mã OTP gửi về số điện thoại -----
    public function otpForm(): void
    {
        if (empty($_SESSION['reset_phone'])) { $this->redirect('/forgot-password'); }
        $this->view('auth/reset-otp', ['title' => t('auth.reset'), 'phone' => $_SESSION['reset_phone']]);
    }

    public function otpReset(): void
    {
        $phone = (string) ($_SESSION['reset_phone'] ?? '');
        if ($phone === '') { $this->redirect('/forgot-password'); }
        $otp = preg_replace('/\D/', '', $this->input('otp'));
        $pass = (string) ($_POST['password'] ?? ''); $conf = (string) ($_POST['password_confirm'] ?? '');
        if (strlen($otp) !== 6) { $this->failWith(['otp' => t('auth.otp_invalid')], '/reset-otp'); }
        if (!valid_password($pass)) { $this->failWith(['password' => t('val.password')], '/reset-otp'); }
        if ($pass !== $conf) { $this->failWith(['password_confirm' => t('val.password_match')], '/reset-otp'); }

        $u = User::byPhone($phone);
        $row = $u ? Model::one("SELECT * FROM password_resets WHERE user_id=? AND channel='phone' AND used_at IS NULL AND expires_at>NOW() ORDER BY id DESC LIMIT 1", [$u['id']]) : null;
        if (!$row || (int) $row['attempts'] >= 5) { $this->failWith(['otp' => t('auth.otp_invalid')], '/reset-otp'); }
        if (!hash_equals($row['token_hash'], hash('sha256', $otp . ':' . $u['id']))) {
            Model::exec("UPDATE password_resets SET attempts=attempts+1 WHERE id=?", [$row['id']]);
            $this->failWith(['otp' => t('auth.otp_invalid')], '/reset-otp');
        }
        unset($_SESSION['reset_phone']);
        $this->finishReset($row, $pass);
    }

    private function finishReset(array $row, string $pass): void
    {
        Model::exec("UPDATE users SET password=? WHERE id=?", [password_hash($pass, PASSWORD_DEFAULT), $row['user_id']]);
        Model::exec("UPDATE password_resets SET used_at=NOW() WHERE id=?", [$row['id']]);
        // Đăng xuất mọi thiết bị đã "ghi nhớ"
        try { Model::exec("DELETE FROM remember_tokens WHERE user_id=?", [$row['user_id']]); } catch (Throwable $e) {}
        flash('success', t('auth.reset_done'));
        $this->redirect('/login');
    }
}
