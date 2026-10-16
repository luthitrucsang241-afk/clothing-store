<?php
class AdminBannerController extends AdminCrudController
{
    protected string $table = 'banners', $title = 'Banner', $route = 'banners', $order = 'position, sort_order, id';
    protected bool $canDelete = true;
    public function __construct()
    {
        $this->columns = ['id' => 'ID', 'image_desktop' => 'Ảnh', 'title' => 'Tiêu đề', 'position' => 'Vị trí', 'sort_order' => 'Thứ tự', 'status' => 'Trạng thái'];
        $this->fields = [
            'title'          => ['label' => 'Tiêu đề (VI)', 'required' => true],
            'title_en'       => ['label' => 'Tiêu đề (EN)', 'null' => true],
            'subtitle'       => ['label' => 'Mô tả ngắn (VI)', 'null' => true],
            'subtitle_en'    => ['label' => 'Mô tả ngắn (EN)', 'null' => true],
            'button_text'    => ['label' => 'Chữ nút (VI)', 'null' => true],
            'button_text_en' => ['label' => 'Chữ nút (EN)', 'null' => true],
            'image_desktop'  => ['label' => 'Ảnh desktop', 'type' => 'image'],
            'image_mobile'   => ['label' => 'Ảnh mobile', 'type' => 'image'],
            'link_url'       => ['label' => 'Đường dẫn (vd: /products?category=ao-polo)', 'null' => true],
            'position'       => ['label' => 'Vị trí', 'type' => 'select', 'options' => ['hero' => 'Slider chính', 'promo' => 'Banner phụ', 'middle' => 'Banner giữa trang'], 'required' => true],
            'sort_order'     => ['label' => 'Thứ tự', 'type' => 'number', 'required' => true],
            'start_at'       => ['label' => 'Bắt đầu hiển thị', 'type' => 'datetime', 'null' => true],
            'end_at'         => ['label' => 'Kết thúc hiển thị', 'type' => 'datetime', 'null' => true],
            'status'         => ['label' => 'Trạng thái', 'type' => 'select', 'options' => ['1' => 'Hiển thị', '0' => 'Ẩn'], 'required' => true],
        ];
    }
    protected function beforeSave(array $d, ?int $id): array
    {
        if (!$id && empty($d['image_desktop'])) { throw new RuntimeException('Vui lòng chọn ảnh desktop cho banner mới.'); }
        if (!empty($d['link_url']) && !preg_match('#^(/|https?://)#', $d['link_url'])) { throw new RuntimeException('Đường dẫn phải bắt đầu bằng / hoặc http(s)://'); }
        return $d;
    }
}
