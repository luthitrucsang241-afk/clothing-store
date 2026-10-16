<?php
class AdminEmployeeController extends AdminController
{
    public function index(): void
    {
        $rows = Model::all("SELECT u.*, r.name AS role_name, e.employee_code, e.position FROM users u JOIN roles r ON r.id=u.role_id
            LEFT JOIN employees e ON e.user_id=u.id WHERE u.role_id IN (2,3) ORDER BY u.id");
        $this->admin('admin/employees', ['title' => 'Nhân viên', 'rows' => $rows]);
    }

    public function save(): void
    {
        $name = $this->input('full_name'); $email = strtolower($this->input('email')); $pos = $this->input('position');
        $role = (int) $this->input('role_id'); $pass = (string) ($_POST['password'] ?? '');
        $e = [];
        if (str_len($name) < 2) { $e[] = 'Họ tên không hợp lệ.'; }
        if (!valid_email($email)) { $e[] = 'Email không hợp lệ.'; } elseif (User::byEmail($email)) { $e[] = 'Email đã tồn tại.'; }
        if (!in_array($role, [2, 3], true)) { $e[] = 'Vai trò không hợp lệ.'; }
        if (!valid_password($pass)) { $e[] = 'Mật khẩu tối thiểu 8 ký tự, gồm chữ và số.'; }
        if ($e) { $_SESSION['old'] = $_POST; flash('error', implode(' ', $e)); $this->redirect('/admin/employees'); }

        $pdo = Database::get(); $pdo->beginTransaction();
        try {
            $uid = Model::insert('users', ['full_name' => $name, 'email' => $email, 'password' => password_hash($pass, PASSWORD_DEFAULT), 'role_id' => $role, 'status' => 1]);
            $code = 'NV' . str_pad((string) $uid, 3, '0', STR_PAD_LEFT);
            Model::insert('employees', ['user_id' => $uid, 'employee_code' => $code, 'position' => $pos ?: null, 'status' => 1]);
            $pdo->commit();
            flash('success', "Đã thêm nhân viên $code.");
        } catch (Throwable $ex) {
            if ($pdo->inTransaction()) { $pdo->rollBack(); }
            error_log($ex->getMessage());
            flash('error', 'Không thêm được nhân viên.');
        }
        $this->redirect('/admin/employees');
    }

    public function toggle(string $id): void
    {
        $id = (int) $id;
        if ($id === user()['id']) { flash('error', 'Không thể tự khoá tài khoản của mình.'); $this->redirect('/admin/employees'); }
        Model::exec("UPDATE users SET status=1-status WHERE id=? AND role_id IN (2,3)", [$id]);
        Model::exec("UPDATE employees SET status=(SELECT status FROM users WHERE id=?) WHERE user_id=?", [$id, $id]);
        $this->redirect('/admin/employees');
    }
}
