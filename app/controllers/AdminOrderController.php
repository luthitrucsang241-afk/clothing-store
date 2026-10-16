<?php
class AdminOrderController extends AdminController
{
    public function index(): void
    {
        $q = $this->input('q'); $st = $this->input('status'); $page = max(1, (int) ($_GET['page'] ?? 1)); $per = 20;
        $w = ['1=1']; $p = [];
        if ($q !== '') { $like = '%' . addcslashes($q, '%_\\') . '%'; $w[] = '(order_code LIKE ? OR recipient_name LIKE ? OR recipient_phone LIKE ?)'; array_push($p, $like, $like, $like); }
        if (isset(Order::$flow[$st])) { $w[] = 'order_status=?'; $p[] = $st; }
        $where = implode(' AND ', $w);
        $total = (int) Model::val("SELECT COUNT(*) FROM orders WHERE $where", $p);
        $rows = Model::all("SELECT * FROM orders WHERE $where ORDER BY created_at DESC LIMIT $per OFFSET " . (($page - 1) * $per), $p);
        $this->admin('admin/orders', ['title' => 'Đơn hàng', 'rows' => $rows, 'total' => $total, 'page' => $page, 'per' => $per, 'q' => $q, 'st' => $st]);
    }

    public function show(string $id): void
    {
        $o = Model::one("SELECT o.*, u.full_name AS user_name, u.email AS user_email, v.code AS voucher_code FROM orders o LEFT JOIN users u ON u.id=o.user_id LEFT JOIN vouchers v ON v.id=o.voucher_id WHERE o.id=?", [(int) $id]);
        if (!$o) { $this->notFound(); return; }
        $this->admin('admin/order-detail', ['title' => 'Đơn ' . $o['order_code'], 'o' => $o, 'items' => Order::items((int) $id), 'history' => Order::history((int) $id)]);
    }

    public function status(string $id): void
    {
        try {
            Order::changeStatus((int) $id, $this->input('new_status'), user()['id'], $this->input('note'));
            flash('success', 'Đã cập nhật trạng thái đơn hàng.');
        } catch (RuntimeException $e) {
            flash('error', $e->getMessage());
        }
        $this->redirect("/admin/orders/view/$id");
    }
}
