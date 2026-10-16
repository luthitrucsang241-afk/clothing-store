<?php
class Membership extends Model
{
    // Cập nhật hạng thành viên theo tổng chi tiêu của các đơn đã giao
    public static function refresh(?int $uid): void
    {
        if (!$uid) { return; }
        $spent = (float) self::val("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE user_id=? AND order_status='delivered'", [$uid]);
        $lvl = self::val("SELECT id FROM membership_levels WHERE status=1 AND minimum_spending<=? ORDER BY minimum_spending DESC LIMIT 1", [$spent]);
        if ($lvl) { self::exec("UPDATE users SET membership_level_id=? WHERE id=? AND role_id=1", [$lvl, $uid]); }
    }
    public static function forUser(int $uid): array
    {
        $spent = (float) self::val("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE user_id=? AND order_status='delivered'", [$uid]);
        $cur = self::one("SELECT m.* FROM users u LEFT JOIN membership_levels m ON m.id=u.membership_level_id WHERE u.id=?", [$uid]);
        $next = self::one("SELECT * FROM membership_levels WHERE status=1 AND minimum_spending>? ORDER BY minimum_spending LIMIT 1", [$spent]);
        return ['spent' => $spent, 'current' => $cur, 'next' => $next];
    }
}
