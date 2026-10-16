<?php
class Review extends Model
{
    public static function forProduct(int $pid): array
    {
        return self::all("SELECT r.*, u.full_name FROM reviews r JOIN users u ON u.id=r.user_id WHERE r.product_id=? AND r.status=1 ORDER BY r.created_at DESC LIMIT 50", [$pid]);
    }
    // Trả về id đơn hàng đã giao chứa sản phẩm mà user chưa đánh giá
    public static function eligibleOrder(int $uid, int $pid): ?int
    {
        $id = self::val("SELECT o.id FROM orders o JOIN order_items i ON i.order_id=o.id
            WHERE o.user_id=? AND o.order_status='delivered' AND i.product_id=?
            AND NOT EXISTS (SELECT 1 FROM reviews r WHERE r.user_id=o.user_id AND r.product_id=i.product_id AND r.order_id=o.id) LIMIT 1", [$uid, $pid]);
        return $id ? (int) $id : null;
    }
    public static function mine(int $uid): array
    {
        return self::all("SELECT r.*, p.name AS product_name, p.id AS pid FROM reviews r JOIN products p ON p.id=r.product_id WHERE r.user_id=? ORDER BY r.created_at DESC", [$uid]);
    }
}
