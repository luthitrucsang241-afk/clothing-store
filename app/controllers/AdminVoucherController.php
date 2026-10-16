<?php
class AdminVoucherController extends AdminCrudController
{
    protected string $table = 'vouchers', $title = 'Voucher', $route = 'vouchers';
    public function __construct()
    {
        $this->columns = ['id' => 'ID', 'code' => 'Mã', 'discount_type' => 'Kiểu', 'discount_value' => 'Mức giảm', 'minimum_order_amount' => 'Đơn tối thiểu', 'used_count' => 'Đã dùng', 'usage_limit' => 'Giới hạn', 'end_at' => 'Hết hạn', 'status' => 'Trạng thái'];
        $this->fields = [
            'code'                    => ['label' => 'Mã giảm giá', 'required' => true],
            'description'             => ['label' => 'Mô tả', 'null' => true],
            'discount_type'           => ['label' => 'Kiểu giảm', 'type' => 'select', 'options' => ['percent' => 'Phần trăm (%)', 'fixed' => 'Số tiền cố định (₫)'], 'required' => true],
            'discount_value'          => ['label' => 'Mức giảm', 'type' => 'number', 'required' => true],
            'minimum_order_amount'    => ['label' => 'Đơn hàng tối thiểu (₫)', 'type' => 'number', 'required' => true],
            'maximum_discount_amount' => ['label' => 'Giảm tối đa (₫, chỉ cho kiểu %)', 'type' => 'number', 'null' => true],
            'usage_limit'             => ['label' => 'Giới hạn lượt dùng (trống = không giới hạn)', 'type' => 'number', 'null' => true],
            'start_at'                => ['label' => 'Bắt đầu', 'type' => 'datetime', 'null' => true],
            'end_at'                  => ['label' => 'Kết thúc', 'type' => 'datetime', 'null' => true],
            'status'                  => ['label' => 'Trạng thái', 'type' => 'select', 'options' => ['1' => 'Bật', '0' => 'Tắt'], 'required' => true],
        ];
    }
    protected function beforeSave(array $d, ?int $id): array
    {
        $d['code'] = strtoupper(preg_replace('/[^A-Za-z0-9_-]/', '', $d['code']));
        if ($d['code'] === '') { throw new RuntimeException('Mã voucher không hợp lệ.'); }
        if ((float) $d['discount_value'] <= 0) { throw new RuntimeException('Mức giảm phải lớn hơn 0.'); }
        if ($d['discount_type'] === 'percent' && (float) $d['discount_value'] > 100) { throw new RuntimeException('Giảm phần trăm không vượt quá 100.'); }
        if ($d['start_at'] && $d['end_at'] && $d['end_at'] < $d['start_at']) { throw new RuntimeException('Thời gian kết thúc phải sau thời gian bắt đầu.'); }
        return $d;
    }
}
