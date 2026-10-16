<?php
class Voucher extends Model
{
    public static function check(string $code, float $subtotal, ?int $uid = null, bool $lock = false): array
    {
        $fail = fn(string $msg) => ['ok' => false, 'error' => $msg];
        $code = strtoupper(trim($code));
        $v = self::one("SELECT * FROM vouchers WHERE code=?" . ($lock ? " FOR UPDATE" : ""), [$code]);
        if (!$v || !$v['status']) { return $fail(t('voucher.invalid')); }
        $now = date('Y-m-d H:i:s');
        if (($v['start_at'] && $v['start_at'] > $now) || ($v['end_at'] && $v['end_at'] < $now)) { return $fail(t('voucher.expired')); }
        if ($v['usage_limit'] !== null && (int) $v['used_count'] >= (int) $v['usage_limit']) { return $fail(t('voucher.used_up')); }
        if ($subtotal < (float) $v['minimum_order_amount']) { return $fail(t('voucher.min', money($v['minimum_order_amount']))); }
        if ($uid && self::val("SELECT COUNT(*) FROM voucher_usages WHERE voucher_id=? AND user_id=?", [$v['id'], $uid])) { return $fail(t('voucher.already')); }

        $d = $v['discount_type'] === 'percent' ? $subtotal * (float) $v['discount_value'] / 100 : (float) $v['discount_value'];
        if ($v['discount_type'] === 'percent' && $v['maximum_discount_amount'] !== null) { $d = min($d, (float) $v['maximum_discount_amount']); }
        return ['ok' => true, 'voucher' => $v, 'discount' => round(min($d, $subtotal))];
    }
}
