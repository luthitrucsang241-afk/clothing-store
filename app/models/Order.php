<?php
class Order extends Model
{
    // Quy tắc chuyển trạng thái hợp lệ
    public static array $flow = [
        'pending'    => ['confirmed', 'cancelled'],
        'confirmed'  => ['processing', 'cancelled'],
        'processing' => ['shipping', 'cancelled'],
        'shipping'   => ['delivered'],
        'delivered'  => [],
        'cancelled'  => [],
    ];

    public static function shippingFee(float $subtotal): float
    {
        if ($subtotal <= 0 || $subtotal >= (float) Setting::get('free_shipping_threshold', 499000)) { return 0.0; }
        return (float) Setting::get('default_shipping_fee', 30000);
    }

    // Tạo đơn trong 1 transaction: khoá tồn kho, tính lại giá + voucher ở máy chủ
    public static function create(array $user, array $ship, string $voucherCode): array
    {
        $pdo = Database::get();
        $pdo->beginTransaction();
        try {
            $cart = $_SESSION['cart'] ?? [];
            if (!$cart) { throw new RuntimeException(t('cart.empty')); }
            ksort($cart);
            $lines = []; $subtotal = 0.0;
            foreach ($cart as $vid => $qty) {
                $r = self::one("SELECT v.id AS vid, v.size, v.color, v.sku, v.stock_quantity, v.price_adjustment, v.status AS vstatus,
                    p.id AS pid, p.name, p.main_image, p.price, p.sale_price, p.status AS pstatus
                    FROM product_variants v JOIN products p ON p.id=v.product_id WHERE v.id=? FOR UPDATE", [(int) $vid]);
                if (!$r || !$r['vstatus'] || !$r['pstatus']) { throw new RuntimeException(t('order.err_unavailable')); }
                if ((int) $qty > (int) $r['stock_quantity'] || (int) $qty < 1) { throw new RuntimeException(t('order.err_stock', $r['name'])); }
                $unit = Product::unitPrice($r, $r['price_adjustment']);
                $lines[] = ['r' => $r, 'qty' => (int) $qty, 'unit' => $unit];
                $subtotal += $unit * $qty;
            }
            $discount = 0.0; $voucher = null;
            if ($voucherCode !== '') {
                $res = Voucher::check($voucherCode, $subtotal, $user['id'], true);
                if (!$res['ok']) { throw new RuntimeException($res['error']); }
                $voucher = $res['voucher']; $discount = $res['discount'];
            }
            $fee = self::shippingFee($subtotal);
            $total = max(0, $subtotal - $discount) + $fee;
            $code = 'NV' . date('ymd') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));

            $oid = self::insert('orders', [
                'order_code' => $code, 'user_id' => $user['id'],
                'recipient_name' => $ship['name'], 'recipient_phone' => $ship['phone'], 'recipient_email' => $ship['email'] ?: null,
                'shipping_address' => $ship['address'], 'subtotal' => $subtotal, 'discount_amount' => $discount,
                'shipping_fee' => $fee, 'total_amount' => $total, 'voucher_id' => $voucher['id'] ?? null,
                'payment_method' => 'cod', 'payment_status' => 'unpaid', 'order_status' => 'pending',
                'stock_deducted' => 1, 'note' => $ship['note'] ?: null,
            ]);
            foreach ($lines as $l) {
                $r = $l['r'];
                self::insert('order_items', [
                    'order_id' => $oid, 'product_id' => $r['pid'], 'product_variant_id' => $r['vid'],
                    'product_name' => $r['name'], 'sku' => $r['sku'], 'size' => $r['size'], 'color' => $r['color'],
                    'image' => $r['main_image'], 'unit_price' => $l['unit'], 'quantity' => $l['qty'], 'subtotal' => $l['unit'] * $l['qty'],
                ]);
                self::exec("UPDATE product_variants SET stock_quantity=stock_quantity-? WHERE id=?", [$l['qty'], $r['vid']]);
                self::exec("UPDATE products SET sold_count=sold_count+? WHERE id=?", [$l['qty'], $r['pid']]);
            }
            self::insert('payments', ['order_id' => $oid, 'payment_method' => 'cod', 'amount' => $total, 'payment_status' => 'unpaid']);
            self::insert('order_status_history', ['order_id' => $oid, 'old_status' => null, 'new_status' => 'pending', 'changed_by' => $user['id'], 'note' => 'Khách đặt hàng']);
            if ($voucher) {
                self::insert('voucher_usages', ['voucher_id' => $voucher['id'], 'user_id' => $user['id'], 'order_id' => $oid, 'discount_amount' => $discount]);
                self::exec("UPDATE vouchers SET used_count=used_count+1 WHERE id=?", [$voucher['id']]);
            }
            $pdo->commit();
            return [$oid, $code];
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) { $pdo->rollBack(); }
            throw $e;
        }
    }

    // Đổi trạng thái (admin hoặc huỷ bởi khách) – kiểm tra luồng hợp lệ, hoàn kho khi huỷ
    public static function changeStatus(int $id, string $new, ?int $by, string $note = ''): void
    {
        $pdo = Database::get();
        $pdo->beginTransaction();
        try {
            $o = self::one("SELECT * FROM orders WHERE id=? FOR UPDATE", [$id]);
            if (!$o) { throw new RuntimeException(t('order.not_found')); }
            if (!in_array($new, self::$flow[$o['order_status']] ?? [], true)) { throw new RuntimeException(t('order.err_transition')); }

            $upd = ['order_status' => $new];
            if ($new === 'cancelled') {
                if ($o['stock_deducted']) {
                    foreach (self::items($id) as $it) {
                        if ($it['product_variant_id']) { self::exec("UPDATE product_variants SET stock_quantity=stock_quantity+? WHERE id=?", [$it['quantity'], $it['product_variant_id']]); }
                        if ($it['product_id']) { self::exec("UPDATE products SET sold_count=GREATEST(sold_count-?,0) WHERE id=?", [$it['quantity'], $it['product_id']]); }
                    }
                    $upd['stock_deducted'] = 0;
                }
                if ($o['voucher_id']) {
                    self::exec("UPDATE vouchers SET used_count=GREATEST(used_count-1,0) WHERE id=?", [$o['voucher_id']]);
                    self::exec("DELETE FROM voucher_usages WHERE order_id=?", [$id]);
                }
            }
            if ($new === 'delivered') {
                $upd['payment_status'] = 'paid';
                self::exec("UPDATE payments SET payment_status='paid' WHERE order_id=?", [$id]);
            }
            self::update('orders', $upd, $id);
            self::insert('order_status_history', ['order_id' => $id, 'old_status' => $o['order_status'], 'new_status' => $new, 'changed_by' => $by, 'note' => $note ?: null]);
            $pdo->commit();
            if ($new === 'delivered') { Membership::refresh($o['user_id'] ? (int) $o['user_id'] : null); }
            if ($o['user_id']) {
                $txt = ['confirmed' => 'đã được xác nhận', 'processing' => 'đang được chuẩn bị', 'shipping' => 'đang được giao đến bạn', 'delivered' => 'đã giao thành công', 'cancelled' => 'đã bị huỷ'][$new] ?? $new;
                notify_user((int) $o['user_id'], "Đơn {$o['order_code']} $txt", "Xin chào,\n\nĐơn hàng {$o['order_code']} của bạn $txt." . ($note !== '' ? "\nGhi chú: $note" : '') . "\n\nBạn có thể theo dõi quá trình giao hàng trong mục \"Đơn hàng của tôi\".\n\nNOIRVA", $by !== (int) $o['user_id'] ? $by : null);
            }
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) { $pdo->rollBack(); }
            throw $e;
        }
    }

    public static function cancelByUser(int $id, int $uid): void
    {
        $o = self::one("SELECT order_status FROM orders WHERE id=? AND user_id=?", [$id, $uid]);
        if (!$o) { throw new RuntimeException(t('order.not_found')); }
        if (!in_array($o['order_status'], ['pending', 'confirmed'], true)) { throw new RuntimeException(t('order.err_cancel')); }
        self::changeStatus($id, 'cancelled', $uid, 'Khách huỷ đơn');
    }

    public static function forUser(int $uid): array
    {
        return self::all("SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC", [$uid]);
    }
    public static function items(int $id): array
    {
        return self::all("SELECT * FROM order_items WHERE order_id=? ORDER BY id", [$id]);
    }
    public static function history(int $id): array
    {
        return self::all("SELECT h.*, u.full_name FROM order_status_history h LEFT JOIN users u ON u.id=h.changed_by WHERE h.order_id=? ORDER BY h.id", [$id]);
    }
}
