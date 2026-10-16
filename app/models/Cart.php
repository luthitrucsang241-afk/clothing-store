<?php
// Giỏ hàng lưu theo Session: [product_variant_id => quantity]. Giá/tồn kho luôn đọc lại từ DB.
class Cart extends Model
{
    public static function count(): int { return (int) array_sum($_SESSION['cart'] ?? []); }

    public static function variant(int $vid): ?array
    {
        return self::one("SELECT v.id AS vid, v.size, v.color, v.sku, v.stock_quantity, v.price_adjustment, v.status AS vstatus,
            p.id AS pid, p.name, p.main_image, p.price, p.sale_price, p.status AS pstatus
            FROM product_variants v JOIN products p ON p.id=v.product_id WHERE v.id=?", [$vid]);
    }

    public static function items(): array
    {
        $cart = $_SESSION['cart'] ?? [];
        $out = ['items' => [], 'subtotal' => 0.0, 'count' => 0, 'ok' => true];
        if (!$cart) { return $out; }
        $ids = array_map('intval', array_keys($cart));
        $in = implode(',', array_fill(0, count($ids), '?'));
        $rows = self::all("SELECT v.id AS vid, v.size, v.color, v.sku, v.stock_quantity, v.price_adjustment, v.status AS vstatus,
            p.id AS pid, p.name, p.main_image, p.price, p.sale_price, p.status AS pstatus
            FROM product_variants v JOIN products p ON p.id=v.product_id WHERE v.id IN ($in)", $ids);
        $by = [];
        foreach ($rows as $r) { $by[(int) $r['vid']] = $r; }
        foreach ($cart as $vid => $qty) {
            if (!isset($by[(int) $vid])) { unset($_SESSION['cart'][$vid]); continue; }
            $r = $by[(int) $vid];
            $r['unit'] = Product::unitPrice($r, $r['price_adjustment']);
            $r['qty'] = (int) $qty;
            $r['line'] = $r['unit'] * $qty;
            $r['available'] = $r['vstatus'] == 1 && $r['pstatus'] == 1 && $qty <= (int) $r['stock_quantity'];
            if (!$r['available']) { $out['ok'] = false; }
            $out['items'][] = $r;
            $out['subtotal'] += $r['line'];
            $out['count'] += $qty;
        }
        return $out;
    }

    public static function summary(): array
    {
        $d = self::items();
        $disc = 0.0; $v = null; $verr = null;
        if (!empty($_SESSION['voucher']) && $d['items']) {
            $r = Voucher::check($_SESSION['voucher'], $d['subtotal'], $_SESSION['user']['id'] ?? null);
            if ($r['ok']) { $v = $r['voucher']; $disc = $r['discount']; }
            else { $verr = $r['error']; unset($_SESSION['voucher']); }
        }
        $ship = $d['items'] ? Order::shippingFee($d['subtotal']) : 0.0;
        return $d + ['discount' => $disc, 'voucher' => $v, 'voucher_error' => $verr, 'shipping' => $ship,
            'total' => max(0, $d['subtotal'] - $disc) + $ship];
    }
}
