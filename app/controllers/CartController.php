<?php
class CartController extends Controller
{
    public function index(): void
    {
        $this->view('cart/index', ['title' => t('cart.title'), 's' => Cart::summary()]);
    }

    public function add(): void
    {
        $vid = (int) ($_POST['variant_id'] ?? 0);
        $qty = max(1, (int) ($_POST['qty'] ?? 1));
        $v = Cart::variant($vid);
        if (!$v || !$v['vstatus'] || !$v['pstatus']) { flash('error', t('cart.pick_variant')); $this->back('/products'); }
        $have = (int) ($_SESSION['cart'][$vid] ?? 0);
        if ($have + $qty > (int) $v['stock_quantity']) {
            flash('error', t('cart.over_stock', (int) $v['stock_quantity']));
            $this->back('/products');
        }
        $_SESSION['cart'][$vid] = $have + $qty;
        if (!empty($_POST['buy_now'])) { $this->redirect('/checkout'); }
        flash('success', t('cart.added'));
        $this->back('/cart');
    }

    public function update(): void
    {
        foreach (($_POST['qty'] ?? []) as $vid => $q) {
            $vid = (int) $vid; $q = (int) $q;
            if (!isset($_SESSION['cart'][$vid])) { continue; }
            if ($q <= 0) { unset($_SESSION['cart'][$vid]); continue; }
            $v = Cart::variant($vid);
            $_SESSION['cart'][$vid] = $v ? min($q, max(1, (int) $v['stock_quantity'])) : $q;
        }
        flash('success', t('cart.updated'));
        $this->redirect('/cart');
    }

    public function remove(): void
    {
        unset($_SESSION['cart'][(int) ($_POST['variant_id'] ?? 0)]);
        $this->redirect('/cart');
    }

    public function applyVoucher(): void
    {
        $code = strtoupper($this->input('code'));
        $d = Cart::items();
        $r = Voucher::check($code, $d['subtotal'], user()['id'] ?? null);
        if ($r['ok']) { $_SESSION['voucher'] = $code; flash('success', t('voucher.applied')); }
        else { flash('error', $r['error']); }
        $this->redirect('/cart');
    }

    public function removeVoucher(): void
    {
        unset($_SESSION['voucher']);
        $this->redirect('/cart');
    }
}
