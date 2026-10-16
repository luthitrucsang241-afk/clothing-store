<?php
class AdminCategoryController extends AdminCrudController
{
    protected string $table = 'categories', $title = 'Danh mục', $route = 'categories', $order = 'id ASC';
    protected bool $canDelete = true;
    public function __construct()
    {
        $this->columns = ['id' => 'ID', 'image' => 'Ảnh', 'name' => 'Tên', 'slug' => 'Slug', 'status' => 'Trạng thái'];
        $this->fields = [
            'name'        => ['label' => 'Tên danh mục (VI)', 'required' => true],
            'name_en'     => ['label' => 'Tên danh mục (EN)', 'null' => true],
            'description' => ['label' => 'Mô tả', 'type' => 'textarea', 'null' => true],
            'image'       => ['label' => 'Ảnh đại diện', 'type' => 'image'],
            'status'      => ['label' => 'Trạng thái', 'type' => 'select', 'options' => ['1' => 'Hiển thị', '0' => 'Ẩn'], 'required' => true],
        ];
    }
    protected function beforeSave(array $d, ?int $id): array
    {
        $d['slug'] = unique_slug('categories', slugify($d['name']), (int) $id);
        return $d;
    }
    public function delete(string $id): void
    {
        if (Model::val("SELECT COUNT(*) FROM products WHERE category_id=?", [(int) $id])) {
            flash('error', 'Danh mục đang có sản phẩm, không thể xoá. Hãy ẩn danh mục.');
            $this->redirect('/admin/categories');
        }
        parent::delete($id);
    }
}
