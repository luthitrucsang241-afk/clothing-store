<?php
class AdminContactController extends AdminCrudController
{
    protected string $table = 'contacts', $title = 'Liên hệ từ khách', $route = 'contacts';
    protected bool $canCreate = false, $canEdit = false;
    public function __construct()
    {
        $this->columns = ['id' => 'ID', 'full_name' => 'Họ tên', 'email' => 'Email', 'subject' => 'Chủ đề', 'message' => 'Nội dung', 'created_at' => 'Ngày', 'status' => 'Trạng thái'];
    }
    public function toggle(string $id): void
    {
        Model::exec("UPDATE contacts SET status=IF(status='done','new','done') WHERE id=?", [(int) $id]);
        $this->back('/admin/contacts');
    }
}
