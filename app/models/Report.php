<?php
// Doanh thu chỉ tính các đơn đã giao (delivered). Đơn đã huỷ không được tính.
class Report extends Model
{
    public static function summary(): array
    {
        return [
            'products'  => (int) self::val("SELECT COUNT(*) FROM products"),
            'customers' => (int) self::val("SELECT COUNT(*) FROM users WHERE role_id=1"),
            'orders'    => (int) self::val("SELECT COUNT(*) FROM orders"),
            'pending'   => (int) self::val("SELECT COUNT(*) FROM orders WHERE order_status='pending'"),
            'delivered' => (int) self::val("SELECT COUNT(*) FROM orders WHERE order_status='delivered'"),
            'cancelled' => (int) self::val("SELECT COUNT(*) FROM orders WHERE order_status='cancelled'"),
            'revenue'   => (float) self::val("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE order_status='delivered'"),
            'rev_month' => (float) self::val("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE order_status='delivered' AND DATE_FORMAT(created_at,'%Y-%m')=DATE_FORMAT(CURDATE(),'%Y-%m')"),
            'gross'     => (float) self::val("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE order_status<>'cancelled'"),
        ];
    }
    public static function byDay(int $days = 30): array
    {
        return self::all("SELECT DATE(created_at) AS d, SUM(total_amount) AS revenue, COUNT(*) AS orders FROM orders
            WHERE order_status='delivered' AND created_at>=DATE_SUB(CURDATE(), INTERVAL " . ($days - 1) . " DAY) GROUP BY DATE(created_at) ORDER BY d");
    }
    public static function byMonth(int $months = 12): array
    {
        return self::all("SELECT DATE_FORMAT(created_at,'%Y-%m') AS m, SUM(total_amount) AS revenue, COUNT(*) AS orders FROM orders
            WHERE order_status='delivered' AND created_at>=DATE_SUB(CURDATE(), INTERVAL " . (int) $months . " MONTH) GROUP BY m ORDER BY m");
    }
    public static function byWeek(int $weeks = 12): array
    {
        return self::all("SELECT DATE_SUB(DATE(created_at), INTERVAL WEEKDAY(created_at) DAY) AS w, SUM(total_amount) AS revenue, COUNT(*) AS orders FROM orders
            WHERE order_status='delivered' AND created_at>=DATE_SUB(CURDATE(), INTERVAL " . (int) $weeks . " WEEK) GROUP BY w ORDER BY w");
    }
    public static function byYear(): array
    {
        return self::all("SELECT YEAR(created_at) AS y, SUM(total_amount) AS revenue, COUNT(*) AS orders FROM orders
            WHERE order_status='delivered' GROUP BY y ORDER BY y");
    }
    // Sản phẩm bán ế: đang bán nhưng số lượng đã giao thấp nhất (kể cả 0)
    public static function worstProducts(int $n = 10): array
    {
        return self::all("SELECT p.id, p.name, COALESCE(SUM(CASE WHEN o.order_status='delivered' THEN i.quantity ELSE 0 END),0) AS qty,
            COALESCE(SUM(CASE WHEN o.order_status='delivered' THEN i.subtotal ELSE 0 END),0) AS revenue
            FROM products p LEFT JOIN order_items i ON i.product_id=p.id LEFT JOIN orders o ON o.id=i.order_id
            WHERE p.status=1 GROUP BY p.id, p.name ORDER BY qty ASC, p.id DESC LIMIT " . (int) $n);
    }
    public static function topProducts(int $n = 10): array
    {
        return self::all("SELECT i.product_name, SUM(i.quantity) AS qty, SUM(i.subtotal) AS revenue FROM order_items i JOIN orders o ON o.id=i.order_id
            WHERE o.order_status='delivered' GROUP BY i.product_name ORDER BY qty DESC LIMIT " . (int) $n);
    }
    public static function topCustomers(int $n = 10): array
    {
        return self::all("SELECT u.id, u.full_name, u.email, COUNT(o.id) AS orders, SUM(o.total_amount) AS spent FROM orders o JOIN users u ON u.id=o.user_id
            WHERE o.order_status='delivered' GROUP BY u.id ORDER BY spent DESC LIMIT " . (int) $n);
    }
    public static function lowStock(int $n = 10, int $threshold = 10): array
    {
        return self::all("SELECT p.name, v.size, v.color, v.sku, v.stock_quantity FROM product_variants v JOIN products p ON p.id=v.product_id
            WHERE v.status=1 AND v.stock_quantity<=? ORDER BY v.stock_quantity, p.name LIMIT " . (int) $n, [$threshold]);
    }
    public static function voucherUsage(): array
    {
        return self::all("SELECT v.code, v.used_count, v.usage_limit, COALESCE(SUM(vu.discount_amount),0) AS total_discount
            FROM vouchers v LEFT JOIN voucher_usages vu ON vu.voucher_id=v.id GROUP BY v.id ORDER BY v.used_count DESC");
    }
    public static function latestOrders(int $n = 8): array
    {
        return self::all("SELECT * FROM orders ORDER BY created_at DESC LIMIT " . (int) $n);
    }
}
