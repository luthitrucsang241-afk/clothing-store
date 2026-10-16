<?php
class AdminReviewController extends AdminCrudController
{
    protected string $table = 'reviews', $title = 'Đánh giá sản phẩm', $route = 'reviews';
    protected bool $canCreate = false, $canEdit = false;
    public function __construct()
    {
        $this->columns = ['id' => 'ID', 'product_name' => 'Sản phẩm', 'user_name' => 'Khách hàng', 'rating' => 'Sao', 'comment' => 'Nội dung', 'created_at' => 'Ngày', 'status' => 'Hiển thị'];
    }
    protected function listSql(): string
    {
        return "SELECT r.id, r.rating, r.comment, r.status, r.created_at, u.full_name AS user_name, p.name AS product_name
                FROM reviews r JOIN users u ON u.id=r.user_id JOIN products p ON p.id=r.product_id ORDER BY r.id DESC";
    }
}
