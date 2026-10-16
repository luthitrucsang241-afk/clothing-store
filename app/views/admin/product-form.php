<?php $v = fn($k, $d = '') => old($k, $p[$k] ?? $d); ?>
<form class="card" method="post" enctype="multipart/form-data" action="<?= e(url('/admin/products/save' . ($p ? '/' . $p['id'] : ''))) ?>"><div class="card-body">
  <?= csrf_field() ?>
  <div class="row2">
    <label>Tên sản phẩm *<input class="form-control" type="text" name="name" value="<?= e($v('name')) ?>" required maxlength="200"></label>
    <label>SKU *<input class="form-control" type="text" name="sku" value="<?= e($v('sku')) ?>" required maxlength="50"></label>
  </div>
  <div class="row2">
    <label>Danh mục *<select class="form-select" name="category_id" required><option value="">— Chọn —</option><?php foreach ($categories as $c): ?><option value="<?= (int) $c['id'] ?>" <?= (int) $v('category_id') === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></label>
    <label>Trạng thái<select class="form-select" name="status"><option value="1" <?= (string) $v('status', '1') === '1' ? 'selected' : '' ?>>Đang bán</option><option value="0" <?= (string) $v('status', '1') === '0' ? 'selected' : '' ?>>Ẩn / ngừng kinh doanh</option></select></label>
  </div>
  <div class="row2">
    <label>Giá gốc (₫) *<input class="form-control" type="number" name="price" min="0" step="1000" value="<?= e($v('price')) ?>" required></label>
    <label>Giá khuyến mãi (₫, để trống nếu không giảm)<input class="form-control" type="number" name="sale_price" min="0" step="1000" value="<?= e($v('sale_price')) ?>"></label>
  </div>
  <label>Chất liệu<input class="form-control" type="text" name="material" value="<?= e($v('material')) ?>"></label>
  <label>Mô tả<textarea class="form-control" name="description" rows="4"><?= e($v('description')) ?></textarea></label>
  <div class="row2">
    <label class="check"><input type="checkbox" name="is_featured" value="1" <?= $v('is_featured') ? 'checked' : '' ?>> Sản phẩm nổi bật</label>
    <label class="check"><input type="checkbox" name="is_new" value="1" <?= $v('is_new') ? 'checked' : '' ?>> Sản phẩm mới</label>
  </div>
  <div class="row2">
    <label>Ảnh chính (JPG/PNG/WEBP, ≤2MB)<input type="file" name="main_image" accept="image/*"></label>
    <label>Thêm ảnh phụ (chọn nhiều)<input type="file" name="images[]" accept="image/*" multiple></label>
  </div>
  <?php if ($p && $p['main_image']): ?><p class="thumb-sm"><?= img($p['main_image'], '') ?></p><?php endif; ?>
  <button class="btn" type="submit">Lưu sản phẩm</button> <a class="btn btn-ghost" href="<?= e(url('/admin/products')) ?>">Quay lại</a>
</div></form>

<?php if ($p): ?>
<div class="card"><div class="card-body"><h3>Ảnh phụ</h3>
  <div class="img-list"><?php foreach ($images as $im): ?>
    <div class="img-item"><?= img($im['image_path'], '') ?>
      <form method="post" action="<?= e(url('/admin/images/delete/' . $im['id'])) ?>" data-confirm="Xoá ảnh này?"><?= csrf_field() ?><button class="btn btn-sm btn-danger" type="submit">Xoá</button></form></div>
  <?php endforeach; if (!$images): ?><span class="muted">Chưa có ảnh phụ.</span><?php endif; ?></div>
</div></div>

<div class="card"><div class="card-body"><h3>Biến thể (size / màu / tồn kho)</h3>
  <div class="table-wrap table-responsive"><table class="table table-hover align-middle"><thead><tr><th>Size</th><th>Màu</th><th>SKU</th><th>Tồn kho</th><th>Điều chỉnh giá (₫)</th><th>Trạng thái</th><th></th></tr></thead><tbody>
  <?php foreach ($variants as $va): $fid = 'vf' . (int) $va['id']; ?>
    <tr>
      <td><?= e($va['size']) ?></td><td><?= e($va['color']) ?></td><td><small><?= e($va['sku']) ?></small></td>
      <td><input form="<?= $fid ?>" class="form-control w-sm" type="number" min="0" name="stock_quantity" value="<?= (int) $va['stock_quantity'] ?>"></td>
      <td><input form="<?= $fid ?>" class="form-control w-md" type="number" step="1000" name="price_adjustment" value="<?= e($va['price_adjustment']) ?>"></td>
      <td><select class="form-select" form="<?= $fid ?>" name="status"><option value="1" <?= $va['status'] ? 'selected' : '' ?>>Bán</option><option value="0" <?= !$va['status'] ? 'selected' : '' ?>>Ẩn</option></select></td>
      <td class="actions"><button form="<?= $fid ?>" class="btn btn-sm" type="submit">Lưu</button>
        <button form="vd<?= (int) $va['id'] ?>" class="btn btn-sm btn-danger" type="submit">Xoá</button></td>
    </tr>
  <?php endforeach; if (!$variants): ?><tr><td colspan="7" class="muted">Chưa có biến thể. Hãy thêm bên dưới để khách có thể đặt hàng.</td></tr><?php endif; ?>
  </tbody></table></div>
  <?php foreach ($variants as $va): ?>
    <form id="vf<?= (int) $va['id'] ?>" method="post" action="<?= e(url('/admin/variants/update/' . $va['id'])) ?>"><?= csrf_field() ?></form>
    <form id="vd<?= (int) $va['id'] ?>" method="post" action="<?= e(url('/admin/variants/delete/' . $va['id'])) ?>" data-confirm="Xoá biến thể này?"><?= csrf_field() ?></form>
  <?php endforeach; ?>
  <h4>Thêm biến thể</h4>
  <form class="toolbar" method="post" action="<?= e(url('/admin/products/' . $p['id'] . '/variants')) ?>"><?= csrf_field() ?>
    <input type="text" name="size" placeholder="Size (S, M, 30…)" required class="form-control w-sm">
    <input class="form-control" type="text" name="color" placeholder="Màu (Đen…)" required>
    <input type="text" name="color_code" placeholder="#111111" class="form-control w-sm">
    <input class="form-control" type="text" name="sku" placeholder="SKU (tự tạo nếu trống)">
    <input type="number" name="stock_quantity" min="0" value="0" class="form-control w-sm" title="Tồn kho">
    <input type="number" name="price_adjustment" step="1000" value="0" class="form-control w-md" title="Điều chỉnh giá">
    <button class="btn btn-sm" type="submit">+ Thêm</button>
  </form>
  <p class="muted">Giá cuối của biến thể = giá bán hiệu lực của sản phẩm (giá KM nếu có) + điều chỉnh giá.</p>
</div></div>
<?php endif; ?>
