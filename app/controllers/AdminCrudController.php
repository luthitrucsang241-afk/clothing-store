<?php
// Lớp CRUD dùng chung cho: danh mục, voucher, khuyến mãi, banner, đánh giá, liên hệ
abstract class AdminCrudController extends AdminController
{
    protected string $table = '';
    protected string $title = '';
    protected string $route = '';
    protected array $fields = [];   // name => [label, type(text|textarea|number|select|datetime|image|products), required, null, options, help]
    protected array $columns = [];  // name => label
    protected string $order = 'id DESC';
    protected bool $canCreate = true, $canEdit = true, $canDelete = false, $canToggle = true;

    protected function listSql(): string { return "SELECT * FROM `{$this->table}` ORDER BY {$this->order}"; }
    protected function beforeSave(array $d, ?int $id): array { return $d; }
    protected function afterSave(int $id): void {}
    protected function extra(?int $id): array { return []; }

    public function meta(): array
    {
        return ['table' => $this->table, 'title' => $this->title, 'route' => $this->route, 'fields' => $this->fields, 'columns' => $this->columns,
                'create' => $this->canCreate, 'edit' => $this->canEdit, 'delete' => $this->canDelete, 'toggle' => $this->canToggle];
    }

    public function index(): void
    {
        $page = max(1, (int) ($_GET['page'] ?? 1)); $per = 20;
        $sql = $this->listSql();
        $total = (int) Model::val("SELECT COUNT(*) FROM ($sql) t");
        $rows = Model::all("SELECT * FROM ($sql) t LIMIT $per OFFSET " . (($page - 1) * $per));
        $this->admin('admin/crud/list', ['title' => $this->title, 'm' => $this->meta(), 'rows' => $rows, 'total' => $total, 'page' => $page, 'per' => $per]);
    }

    public function form(?string $id = null): void
    {
        $id = $id !== null ? (int) $id : null;
        $row = null;
        if ($id) {
            $row = Model::one("SELECT * FROM `{$this->table}` WHERE id=?", [$id]);
            if (!$row) { $this->notFound(); return; }
        }
        $this->admin('admin/crud/form', ['title' => $this->title, 'm' => $this->meta(), 'row' => $row, 'extra' => $this->extra($id)]);
    }

    public function save(?string $id = null): void
    {
        $id = $id !== null ? (int) $id : null;
        $data = []; $errors = [];
        foreach ($this->fields as $name => $f) {
            $type = $f['type'] ?? 'text';
            if ($type === 'products') { continue; }
            if ($type === 'image') {
                $up = upload_image($_FILES[$name] ?? ['error' => UPLOAD_ERR_NO_FILE]);
                if ($up && isset($up['error'])) { $errors[] = $f['label'] . ': ' . $up['error']; }
                elseif ($up) { $data[$name] = $up['path']; }
                continue;
            }
            $v = $this->input($name);
            if (!empty($f['required']) && $v === '') { $errors[] = $f['label'] . ' là bắt buộc.'; }
            if ($type === 'number' && $v !== '' && !is_numeric($v)) { $errors[] = $f['label'] . ' phải là số.'; }
            if ($type === 'select' && $v !== '' && !isset($f['options'][$v])) { $errors[] = $f['label'] . ' không hợp lệ.'; }
            if ($type === 'datetime' && $v !== '') { $v = str_replace('T', ' ', $v); if (strlen($v) === 16) { $v .= ':00'; } }
            if ($v === '' && !empty($f['null'])) { $v = null; }
            $data[$name] = $v;
        }
        if (!$errors) {
            try { $data = $this->beforeSave($data, $id); } catch (RuntimeException $e) { $errors[] = $e->getMessage(); }
        }
        if (!$errors) {
            try {
                if ($id) { Model::update($this->table, $data, $id); }
                else { $id = Model::insert($this->table, $data); }
                $this->afterSave($id);
            } catch (PDOException $e) {
                $errors[] = $e->getCode() === '23000' ? 'Dữ liệu bị trùng (mã/slug/SKU đã tồn tại).' : 'Lỗi cơ sở dữ liệu.';
                error_log($e->getMessage());
            }
        }
        if ($errors) {
            $_SESSION['old'] = $_POST;
            flash('error', implode(' ', $errors));
            $this->redirect("/admin/{$this->route}/" . ($id ? "edit/$id" : 'create'));
        }
        flash('success', 'Đã lưu thành công.');
        $this->redirect("/admin/{$this->route}");
    }

    public function toggle(string $id): void
    {
        if ($this->canToggle) { Model::exec("UPDATE `{$this->table}` SET status=1-status WHERE id=?", [(int) $id]); }
        $this->back("/admin/{$this->route}");
    }

    public function delete(string $id): void
    {
        if ($this->canDelete) {
            try { Model::exec("DELETE FROM `{$this->table}` WHERE id=?", [(int) $id]); flash('success', 'Đã xoá.'); }
            catch (PDOException $e) { flash('error', 'Không thể xoá vì dữ liệu đang được sử dụng. Hãy ẩn thay vì xoá.'); }
        }
        $this->redirect("/admin/{$this->route}");
    }
}
