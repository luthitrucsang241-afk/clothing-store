<?php
class AdminProductController extends AdminController
{
    public function index(): void
    {
        $q = $this->input('q'); $cat = (int) ($_GET['category'] ?? 0); $st = $_GET['status'] ?? '';
        $page = max(1, (int) ($_GET['page'] ?? 1)); $per = 15;
        $w = ['1=1']; $p = [];
        if ($q !== '') { $like = '%' . addcslashes($q, '%_\\') . '%'; $w[] = '(p.name LIKE ? OR p.sku LIKE ?)'; array_push($p, $like, $like); }
        if ($cat) { $w[] = 'p.category_id=?'; $p[] = $cat; }
        if ($st === '0' || $st === '1') { $w[] = 'p.status=?'; $p[] = $st; }
        $where = implode(' AND ', $w);
        $total = (int) Model::val("SELECT COUNT(*) FROM products p WHERE $where", $p);
        $rows = Model::all("SELECT p.*, c.name AS category_name, (SELECT COALESCE(SUM(stock_quantity),0) FROM product_variants WHERE product_id=p.id) AS stock
            FROM products p JOIN categories c ON c.id=p.category_id WHERE $where ORDER BY p.id DESC LIMIT $per OFFSET " . (($page - 1) * $per), $p);
        $this->admin('admin/products', ['title' => 'Sản phẩm', 'rows' => $rows, 'total' => $total, 'page' => $page, 'per' => $per, 'q' => $q, 'cat' => $cat, 'st' => $st, 'categories' => Model::all("SELECT id, name FROM categories ORDER BY name")]);
    }

    public function form(?string $id = null): void
    {
        $id = $id !== null ? (int) $id : null;
        $p = null; $variants = []; $images = [];
        if ($id) {
            $p = Model::one("SELECT * FROM products WHERE id=?", [$id]);
            if (!$p) { $this->notFound(); return; }
            $variants = Model::all("SELECT * FROM product_variants WHERE product_id=? ORDER BY color, FIELD(size,'S','M','L','XL','XXL'), size", [$id]);
            $images = Model::all("SELECT * FROM product_images WHERE product_id=? ORDER BY sort_order, id", [$id]);
        }
        $this->admin('admin/product-form', ['title' => $id ? 'Sửa sản phẩm' : 'Thêm sản phẩm', 'p' => $p, 'variants' => $variants, 'images' => $images, 'categories' => Model::all("SELECT id, name FROM categories ORDER BY name")]);
    }

    public function save(?string $id = null): void
    {
        $id = $id !== null ? (int) $id : null;
        $name = $this->input('name'); $sku = strtoupper($this->input('sku')); $price = $this->input('price'); $sale = $this->input('sale_price');
        $e = [];
        if ($name === '' || str_len($name) > 200) { $e[] = 'Tên sản phẩm là bắt buộc (tối đa 200 ký tự).'; }
        if ($sku === '' || !preg_match('/^[A-Z0-9_-]{2,50}$/', $sku)) { $e[] = 'SKU chỉ gồm chữ, số, - và _.'; }
        if (!is_numeric($price) || (float) $price < 0) { $e[] = 'Giá bán phải là số không âm.'; }
        if ($sale !== '' && (!is_numeric($sale) || (float) $sale < 0 || (float) $sale >= (float) $price)) { $e[] = 'Giá khuyến mãi phải nhỏ hơn giá gốc và không âm.'; }
        if (!Model::val("SELECT COUNT(*) FROM categories WHERE id=?", [(int) $this->input('category_id')])) { $e[] = 'Vui lòng chọn danh mục.'; }
        $dup = Model::val("SELECT COUNT(*) FROM products WHERE sku=? AND id<>?", [$sku, (int) $id]);
        if ($dup) { $e[] = 'SKU đã tồn tại.'; }

        $data = [
            'category_id' => (int) $this->input('category_id'), 'name' => $name, 'sku' => $sku,
            'slug' => unique_slug('products', slugify($name), (int) $id),
            'description' => $this->input('description') ?: null, 'material' => $this->input('material') ?: null,
            'price' => $price, 'sale_price' => $sale === '' ? null : $sale,
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0, 'is_new' => isset($_POST['is_new']) ? 1 : 0,
            'status' => $this->input('status') === '0' ? 0 : 1,
        ];
        $main = upload_image($_FILES['main_image'] ?? ['error' => UPLOAD_ERR_NO_FILE]);
        if ($main && isset($main['error'])) { $e[] = 'Ảnh chính: ' . $main['error']; }
        elseif ($main) { $data['main_image'] = $main['path']; }
        $extra = [];
        foreach (files_list('images') as $f) {
            $u = upload_image($f);
            if ($u && isset($u['error'])) { $e[] = 'Ảnh phụ: ' . $u['error']; } elseif ($u) { $extra[] = $u['path']; }
        }
        if ($e) { $_SESSION['old'] = $_POST; flash('error', implode(' ', $e)); $this->redirect('/admin/products/' . ($id ? "edit/$id" : 'create')); }

        try {
            if ($id) { Model::update('products', $data, $id); }
            else { $id = Model::insert('products', $data); }
            foreach ($extra as $i => $path) { Model::insert('product_images', ['product_id' => $id, 'image_path' => $path, 'sort_order' => $i + 1]); }
        } catch (PDOException $ex) {
            error_log($ex->getMessage());
            flash('error', 'Không lưu được sản phẩm (SKU hoặc slug có thể bị trùng).');
            $this->redirect('/admin/products/' . ($id ? "edit/$id" : 'create'));
        }
        flash('success', 'Đã lưu sản phẩm. Thêm/sửa biến thể (size, màu, tồn kho) bên dưới.');
        $this->redirect("/admin/products/edit/$id");
    }

    public function toggle(string $id): void
    {
        Model::exec("UPDATE products SET status=1-status WHERE id=?", [(int) $id]);
        $this->back('/admin/products');
    }

    public function delete(string $id): void
    {
        $id = (int) $id;
        $p = Model::one("SELECT * FROM products WHERE id=?", [$id]);
        if (!$p) { $this->notFound(); return; }
        $files = array_column(Model::all("SELECT image_path FROM product_images WHERE product_id=?", [$id]), 'image_path');
        if ($p['main_image']) { $files[] = $p['main_image']; }
        try {
            // Biến thể, ảnh phụ, đánh giá xoá theo; đơn cũ vẫn giữ tên/giá/ảnh nhờ order_items lưu bản chụp (snapshot)
            Model::exec("DELETE FROM products WHERE id=?", [$id]);
        } catch (PDOException $ex) {
            error_log($ex->getMessage());
            flash('error', 'Không xoá được sản phẩm (đang được dùng ở nơi khác). Hãy dùng nút Ẩn.');
            $this->redirect('/admin/products');
        }
        foreach ($files as $f) {
            if (strpos($f, 'assets/uploads/') === 0 && !Model::val("SELECT COUNT(*) FROM order_items WHERE image=?", [$f])) { @unlink(ROOT . '/public/' . $f); }
        }
        flash('success', 'Đã xoá sản phẩm "' . $p['name'] . '".');
        $this->redirect('/admin/products');
    }

    public function addVariant(string $id): void
    {
        $pid = (int) $id;
        $size = strtoupper($this->input('size')); $color = $this->input('color'); $stock = $this->input('stock_quantity', '0'); $adj = $this->input('price_adjustment', '0');
        $sku = strtoupper($this->input('sku'));
        if ($size === '' || $color === '' || !ctype_digit($stock) || !is_numeric($adj)) { flash('error', 'Size, màu bắt buộc; tồn kho là số nguyên không âm.'); $this->redirect("/admin/products/edit/$pid"); }
        if ($sku === '') { $sku = Model::val("SELECT sku FROM products WHERE id=?", [$pid]) . '-' . $size . '-' . strtoupper(substr(slugify($color), 0, 3)); }
        $cc = preg_match('/^#[0-9A-Fa-f]{6}$/', $this->input('color_code')) ? $this->input('color_code') : null;
        try {
            Model::insert('product_variants', ['product_id' => $pid, 'size' => $size, 'color' => $color, 'color_code' => $cc, 'sku' => $sku, 'stock_quantity' => (int) $stock, 'price_adjustment' => $adj, 'status' => 1]);
            flash('success', 'Đã thêm biến thể.');
        } catch (PDOException $e) { flash('error', 'Biến thể (size + màu) hoặc SKU đã tồn tại.'); }
        $this->redirect("/admin/products/edit/$pid");
    }

    public function updateVariant(string $id): void
    {
        $v = Model::one("SELECT product_id FROM product_variants WHERE id=?", [(int) $id]);
        if (!$v) { $this->notFound(); return; }
        $stock = $this->input('stock_quantity', '0'); $adj = $this->input('price_adjustment', '0');
        if (!ctype_digit($stock) || !is_numeric($adj)) { flash('error', 'Giá trị không hợp lệ.'); }
        else {
            Model::exec("UPDATE product_variants SET stock_quantity=?, price_adjustment=?, status=? WHERE id=?", [(int) $stock, $adj, $this->input('status') === '0' ? 0 : 1, (int) $id]);
            flash('success', 'Đã cập nhật biến thể.');
        }
        $this->redirect('/admin/products/edit/' . $v['product_id']);
    }

    public function deleteVariant(string $id): void
    {
        $v = Model::one("SELECT product_id FROM product_variants WHERE id=?", [(int) $id]);
        if (!$v) { $this->notFound(); return; }
        Model::exec("DELETE FROM product_variants WHERE id=?", [(int) $id]); // đơn cũ vẫn giữ nguyên tên/giá nhờ order_items lưu snapshot
        flash('success', 'Đã xoá biến thể.');
        $this->redirect('/admin/products/edit/' . $v['product_id']);
    }

    public function deleteImage(string $id): void
    {
        $img = Model::one("SELECT * FROM product_images WHERE id=?", [(int) $id]);
        if (!$img) { $this->notFound(); return; }
        Model::exec("DELETE FROM product_images WHERE id=?", [(int) $id]);
        if (strpos($img['image_path'], 'assets/uploads/') === 0) { @unlink(ROOT . '/public/' . $img['image_path']); }
        $this->redirect('/admin/products/edit/' . $img['product_id']);
    }
}
