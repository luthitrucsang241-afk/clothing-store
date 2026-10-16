<?php
class CheckoutController extends Controller
{
    public function index(): void
    {
        $s = Cart::summary();
        if (!$s['items']) { flash('warning', t('cart.empty')); $this->redirect('/cart'); }
        if (!$s['ok']) { flash('error', t('cart.fix_items')); $this->redirect('/cart'); }
        $this->view('checkout/index', ['title' => t('checkout.title'), 's' => $s, 'u' => User::byId(user()['id'])]);
    }

    public function place(): void
    {
        $f = ['name' => $this->input('name'), 'phone' => $this->input('phone'), 'email' => $this->input('email'),
              'province' => $this->input('province'), 'district' => $this->input('district'),
              'street' => $this->input('street'), 'note' => $this->input('note')];
        $e = [];
        if (str_len($f['name']) < 2 || str_len($f['name']) > 120) { $e['name'] = t('val.name'); }
        if (!valid_phone($f['phone'])) { $e['phone'] = t('val.phone'); }
        if ($f['email'] !== '' && !valid_email($f['email'])) { $e['email'] = t('msg.invalid_email'); }
        foreach (['province', 'district', 'street'] as $k) { if ($f[$k] === '') { $e[$k] = t('val.required'); } }
        if (str_len($f['note']) > 500) { $e['note'] = t('val.too_long'); }
        if (($_POST['payment_method'] ?? 'cod') !== 'cod') { $e['payment_method'] = t('checkout.cod_only'); }
        if ($e) { $this->failWith($e, '/checkout'); }

        $ship = ['name' => $f['name'], 'phone' => $f['phone'], 'email' => $f['email'], 'note' => $f['note'],
                 'address' => $f['street'] . ', ' . $f['district'] . ', ' . $f['province']];
        try {
            [$oid, $code] = Order::create(user(), $ship, $_SESSION['voucher'] ?? '');
        } catch (RuntimeException $ex) {
            flash('error', $ex->getMessage());
            $this->redirect('/cart');
        } catch (Throwable $ex) {
            error_log('Checkout error: ' . $ex->getMessage());
            flash('error', t('msg.server_error'));
            $this->redirect('/cart');
        }
        unset($_SESSION['cart'], $_SESSION['voucher']);
        notify_user((int) user()['id'], "Đặt hàng thành công – $code", "Cảm ơn bạn đã đặt hàng tại NOIRVA.\nMã đơn: $code\nChúng tôi sẽ sớm xác nhận và giao hàng cho bạn.\n\nNOIRVA");
        if ($f['email'] !== '' && strtolower($f['email']) !== strtolower((string) user()['email'])) { send_mail($f['email'], 'order_confirmation', "Xác nhận đơn $code", "Cảm ơn bạn đã đặt hàng tại NOIRVA. Mã đơn: $code"); }
        flash('success', t('checkout.success', $code));
        $this->redirect('/orders/detail?id=' . $oid);
    }
}
