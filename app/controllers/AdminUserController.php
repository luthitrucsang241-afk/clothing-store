<?php
class AdminUserController extends AdminController
{
    public function index(): void
    {
        $q = $this->input('q'); $page = max(1, (int) ($_GET['page'] ?? 1)); $per = 20;
        $w = 'u.role_id=1'; $p = [];
        if ($q !== '') { $like = '%' . addcslashes($q, '%_\\') . '%'; $w .= ' AND (u.full_name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)'; array_push($p, $like, $like, $like); }
        $total = (int) Model::val("SELECT COUNT(*) FROM users u WHERE $w", $p);
        $rows = Model::all("SELECT u.*, m.name AS level_name, (SELECT COUNT(*) FROM orders WHERE user_id=u.id) AS orders FROM users u
            LEFT JOIN membership_levels m ON m.id=u.membership_level_id WHERE $w ORDER BY u.id DESC LIMIT $per OFFSET " . (($page - 1) * $per), $p);
        $this->admin('admin/users', ['title' => 'Khách hàng', 'rows' => $rows, 'total' => $total, 'page' => $page, 'per' => $per, 'q' => $q]);
    }

    public function show(string $id): void
    {
        $u = User::byId((int) $id);
        if (!$u) { $this->notFound(); return; }
        $this->admin('admin/user-detail', ['title' => $u['full_name'], 'u' => $u, 'orders' => Order::forUser((int) $id), 'ms' => Membership::forUser((int) $id)]);
    }

    public function toggle(string $id): void
    {
        $id = (int) $id;
        if ($id === user()['id']) { flash('error', 'Không thể tự khoá tài khoản của mình.'); $this->back('/admin/users'); }
        Model::exec("UPDATE users SET status=1-status WHERE id=?", [$id]);
        $this->back('/admin/users');
    }

    public function message(string $id): void
    {
        $u = User::byId((int) $id);
        if (!$u) { $this->notFound(); return; }
        $sub = $this->input('subject'); $body = $this->input('body');
        if ($sub === '' || str_len($sub) > 200 || str_len($body) < 5 || str_len($body) > 3000) {
            flash('error', 'Tiêu đề bắt buộc (≤200 ký tự), nội dung từ 5 đến 3000 ký tự.');
            $this->redirect('/admin/users/view/' . (int) $id);
        }
        notify_user((int) $u['id'], $sub, $body, user()['id']);
        flash('success', 'Đã gửi thư cho khách hàng.');
        $this->redirect('/admin/users/view/' . (int) $id);
    }
}
