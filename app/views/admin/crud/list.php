<div class="toolbar"><?php if ($m['create']): ?><a class="btn btn-sm btn-accent" href="<?= e(url('/admin/' . $m['route'] . '/create')) ?>">+ Thêm mới</a><?php endif; ?></div>
<div class="table-wrap table-responsive"><table class="table table-hover align-middle">
  <thead><tr><?php foreach ($m['columns'] as $label): ?><th><?= e($label) ?></th><?php endforeach; ?><th></th></tr></thead>
  <tbody>
  <?php foreach ($rows as $r): ?>
    <tr>
      <?php foreach ($m['columns'] as $k => $label): $val = $r[$k] ?? ''; ?>
        <td>
        <?php if (strpos($k, 'image') !== false): ?><span class="thumb-sm"><?= img($val ?: null, '') ?></span>
        <?php elseif ($k === 'status' && in_array((string) $val, ['0', '1'], true)): ?><span class="badge <?= $val ? 'ok' : 'bad' ?>"><?= $val ? 'Bật' : 'Ẩn' ?></span>
        <?php elseif ($k === 'status'): ?><span class="badge <?= $val === 'done' ? 'ok' : 'warn' ?>"><?= e(['new' => 'Mới', 'read' => 'Đã đọc', 'done' => 'Đã xử lý'][$val] ?? $val) ?></span>
        <?php elseif ($k === 'rating'): ?><?= str_repeat('★', (int) $val) ?>
        <?php elseif (in_array($k, ['discount_value', 'minimum_order_amount'], true) && is_numeric($val)): ?><?= e(number_format((float) $val, 0, ',', '.')) ?><?= ($r['discount_type'] ?? '') === 'percent' && $k === 'discount_value' ? '%' : '' ?>
        <?php else: ?><?= e(mb_strimwidth((string) $val, 0, 80, '…', 'UTF-8')) ?><?php endif; ?>
        </td>
      <?php endforeach; ?>
      <td class="actions">
        <?php if ($m['edit']): ?><a class="btn btn-sm btn-ghost" href="<?= e(url('/admin/' . $m['route'] . '/edit/' . $r['id'])) ?>">Sửa</a><?php endif; ?>
        <?php if ($m['toggle']): ?><form method="post" class="inline" action="<?= e(url('/admin/' . $m['route'] . '/toggle/' . $r['id'])) ?>"><?= csrf_field() ?><button class="btn btn-sm btn-ghost" type="submit"><?= $m['route'] === 'contacts' ? 'Đổi trạng thái' : 'Ẩn/Hiện' ?></button></form><?php endif; ?>
        <?php if ($m['delete']): ?><form method="post" class="inline" action="<?= e(url('/admin/' . $m['route'] . '/delete/' . $r['id'])) ?>" data-confirm="Xoá mục này?"><?= csrf_field() ?><button class="btn btn-sm btn-danger" type="submit">Xoá</button></form><?php endif; ?>
      </td>
    </tr>
  <?php endforeach; if (!$rows): ?><tr><td colspan="<?= count($m['columns']) + 1 ?>" class="muted">Chưa có dữ liệu.</td></tr><?php endif; ?>
  </tbody></table></div>
<?= pager($total, $page, $per) ?>
