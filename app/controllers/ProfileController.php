<?php
class ProfileController extends Controller
{
    public function index(): void
    {
        $uid = user()['id'];
        $this->view('profile/index', ['title' => t('profile.title'), 'u' => User::byId($uid), 'ms' => Membership::forUser($uid), 'levels' => Model::all("SELECT * FROM membership_levels WHERE status=1 ORDER BY minimum_spending")]);
    }

    public function update(): void
    {
        $name = $this->input('full_name'); $phone = $this->input('phone'); $addr = $this->input('address');
        $e = [];
        if (str_len($name) < 2 || str_len($name) > 120) { $e['full_name'] = t('val.name'); }
        if ($phone !== '' && !valid_phone($phone)) { $e['phone'] = t('val.phone'); }
        if (str_len($addr) > 255) { $e['address'] = t('val.too_long'); }
        if ($e) { $this->failWith($e, '/profile'); }
        Model::exec("UPDATE users SET full_name=?, phone=?, address=? WHERE id=?", [$name, $phone ?: null, $addr ?: null, user()['id']]);
        $_SESSION['user']['name'] = $name;
        flash('success', t('profile.saved'));
        $this->redirect('/profile');
    }

    public function password(): void
    {
        $cur = (string) ($_POST['current_password'] ?? ''); $new = (string) ($_POST['password'] ?? ''); $conf = (string) ($_POST['password_confirm'] ?? '');
        $u = User::byId(user()['id']);
        $e = [];
        if (!password_verify($cur, $u['password'])) { $e['current_password'] = t('profile.bad_current'); }
        if (!valid_password($new)) { $e['password'] = t('val.password'); }
        elseif ($new !== $conf) { $e['password_confirm'] = t('val.password_match'); }
        if ($e) { $this->failWith($e, '/profile'); }
        Model::exec("UPDATE users SET password=? WHERE id=?", [password_hash($new, PASSWORD_DEFAULT), $u['id']]);
        session_regenerate_id(true);
        flash('success', t('profile.pw_changed'));
        $this->redirect('/profile');
    }
}
