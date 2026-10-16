<form class="toolbar" method="get"><input class="form-control" type="search" name="q" value="<?= e($q) ?>" placeholder="Mã đơn, tên, SĐT">
  <select class="form-select" name="status"><option value="">Mọi trạng thái</option><?php foreach (array_keys(Order::$flow) as $k): ?><option value="<?= $k ?>" <?= $st === $k ? 'selected' : '' ?>><?= e(status_label($k)) ?></option><?php endforeach; ?></select>
  <button class="btn btn-sm" type="submit">Lọc</button></form>
<div class="table-wrap table-responsive"><table class="table table-hover align-middle"><thead><tr><th>Mã đơn</th><th>Ngày</th><th>Người nhận</th><th>SĐT</th><th>Tổng</th><th>Thanh toán</th><th>Trạng thái</th><th></th></tr></thead><tbody>
<?php foreach ($rows as $o): ?><tr><td><strong><?= e($o['order_code']) ?></strong></td><td><?= e(date('d/m/Y H:i', strtotime($o['created_at']))) ?></td><td><?= e($o['recipient_name']) ?></td><td><?= e($o['recipient_phone']) ?></td><td><?= e(money($o['total_amount'])) ?></td>
  <td><?= e(strtoupper($o['payment_method'])) ?> · <?= e($o['payment_status']) ?></td><td><span class="badge <?= e(status_class($o['order_status'])) ?>"><?= e(status_label($o['order_status'])) ?></span></td>
  <td><a class="btn btn-sm btn-ghost" href="<?= e(url('/admin/orders/view/' . $o['id'])) ?>">Chi tiết</a></td></tr>
<?php endforeach; if (!$rows): ?><tr><td colspan="8" class="muted">Không có đơn hàng.</td></tr><?php endif; ?></tbody></table></div>
<?= pager($total, $page, $per) ?>
