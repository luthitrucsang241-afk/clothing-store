<?php
class AdminPromotionController extends AdminCrudController
{
    protected string $table = 'promotions', $title = 'Khuyến mãi', $route = 'promotions';
    protected bool $canDelete = true;
    public function __construct()
    {
        $this->columns = ['id' => 'ID', 'name' => 'Tên chương trình', 'discount_percent' => '% giảm', 'start_at' => 'Bắt đầu', 'end_at' => 'Kết thúc', 'status' => 'Trạng thái'];
        $this->fields = [
            'name'             => ['label' => 'Tên chương trình', 'required' => true],
            'description'      => ['label' => 'Mô tả', 'type' => 'textarea', 'null' => true],
            'discount_percent' => ['label' => '% giảm (để trống = chỉ hiển thị thông tin, KHÔNG tự giảm giá)', 'type' => 'number', 'null' => true],
            'start_at'         => ['label' => 'Bắt đầu', 'type' => 'datetime', 'null' => true],
            'end_at'           => ['label' => 'Kết thúc', 'type' => 'datetime', 'null' => true],
            'status'           => ['label' => 'Trạng thái', 'type' => 'select', 'options' => ['1' => 'Bật', '0' => 'Tắt'], 'required' => true],
            'product_ids'      => ['label' => 'Sản phẩm áp dụng', 'type' => 'products'],
        ];
    }
    protected function beforeSave(array $d, ?int $id): array
    {
        if ($d['discount_percent'] !== null && ((float) $d['discount_percent'] < 0 || (float) $d['discount_percent'] > 100)) { throw new RuntimeException('% giảm phải từ 0 đến 100.'); }
        return $d;
    }
    protected function extra(?int $id): array
    {
        return ['products' => Model::all("SELECT id, name FROM products ORDER BY name"),
                'selected' => $id ? array_column(Model::all("SELECT product_id FROM promotion_products WHERE promotion_id=?", [$id]), 'product_id') : []];
    }
    protected function afterSave(int $id): void
    {
        Model::exec("DELETE FROM promotion_products WHERE promotion_id=?", [$id]);
        foreach (array_unique(array_map('intval', (array) ($_POST['product_ids'] ?? []))) as $pid) {
            if ($pid > 0) { Model::exec("INSERT IGNORE INTO promotion_products (promotion_id, product_id) VALUES (?,?)", [$id, $pid]); }
        }
    }
}
