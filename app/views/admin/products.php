<form class="toolbar" method="get" action="<?= e(url('/admin/products')) ?>">
  <input class="form-control" type="search" name="q" value="<?= e($q) ?>" placeholder="Tìm tên hoặc SKU">
  <select class="form-select" name="category"><option value="">Tất cả danh mục</option><?php foreach ($categories as $c): ?><option value="<?= (int) $c['id'] ?>" <?= $cat === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select>
  <select class="form-select" name="status"><option value="">Mọi trạng thái</option><option value="1" <?= $st === '1' ? 'selected' : '' ?>>Đang bán</option><option value="0" <?= $st === '0' ? 'selected' : '' ?>>Đã ẩn</option></select>
  <button class="btn btn-sm" type="submit">Lọc</button>
  <a class="btn btn-sm btn-accent" href="<?= e(url('/admin/products/create')) ?>">+ Thêm sản phẩm</a>
</form>
<div class="table-wrap table-responsive"><table class="table table-hover align-middle">
  <thead><tr><th>ID</th><th>Ảnh</th><th>Tên</th><th>SKU</th><th>Danh mục</th><th>Giá</th><th>Tồn</th><th>Trạng thái</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($rows as $r): ?>
    <tr>
      <td><?= (int) $r['id'] ?></td><td class="thumb-sm"><?= img($r['main_image'], '') ?></td>
      <td><a href="<?= e(url('/admin/products/edit/' . $r['id'])) ?>"><strong><?= e($r['name']) ?></strong></a></td>
      <td><?= e($r['sku']) ?></td><td><?= e($r['category_name']) ?></td>
      <td><?= e(money($r['price'])) ?><?= $r['sale_price'] !== null ? '<br><small class="muted">KM: ' . e(money($r['sale_price'])) . '</small>' : '' ?></td>
      <td><?= (int) $r['stock'] ?></td>
      <td><span class="badge <?= $r['status'] ? 'ok' : 'bad' ?>"><?= $r['status'] ? 'Đang bán' : 'Đã ẩn' ?></span></td>
      <td class="actions"><a class="btn btn-sm btn-ghost" href="<?= e(url('/admin/products/edit/' . $r['id'])) ?>">Sửa</a>
        <form method="post" action="<?= e(url('/admin/products/toggle/' . $r['id'])) ?>" class="inline"><?= csrf_field() ?><button class="btn btn-sm btn-ghost" type="submit"><?= $r['status'] ? 'Ẩn' : 'Hiện' ?></button></form>
        <form method="post" action="<?= e(url('/admin/products/delete/' . $r['id'])) ?>" class="inline" data-confirm="Xoá vĩnh viễn sản phẩm này cùng biến thể và ảnh? Không thể hoàn tác. Nếu chỉ muốn ngừng bán, hãy dùng nút Ẩn."><?= csrf_field() ?><button class="btn btn-sm btn-ghost text-danger" type="submit">Xoá</button></form></td>
    </tr>
  <?php endforeach; if (!$rows): ?><tr><td colspan="9" class="muted">Không có sản phẩm.</td></tr><?php endif; ?>
  </tbody></table></div>
<?= pager($total, $page, $per) ?>
