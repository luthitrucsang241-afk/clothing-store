<?php
class OrderController extends Controller
{
    public function index(): void
    {
        $this->view('orders/index', ['title' => t('orders.title'), 'orders' => Order::forUser(user()['id'])]);
    }

    public function detail(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $o = Model::one("SELECT * FROM orders WHERE id=? AND user_id=?", [$id, user()['id']]); // chỉ xem đơn của chính mình
        if (!$o) { $this->notFound(); return; }
        $this->view('orders/detail', ['title' => $o['order_code'], 'o' => $o, 'items' => Order::items($id), 'history' => Order::history($id)]);
    }

    public function cancel(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        try {
            Order::cancelByUser($id, user()['id']);
            flash('success', t('orders.cancelled'));
        } catch (RuntimeException $ex) {
            flash('error', $ex->getMessage());
        }
        $this->redirect('/orders/detail?id=' . $id);
    }
}
