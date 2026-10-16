<p><a href="<?= e(url('/admin/orders')) ?>">&larr; Danh sách đơn</a></p>
<div class="grid">
  <div class="c-8 t-8 m-4">
    <div class="card"><div class="card-body"><h3>Sản phẩm</h3><div class="table-wrap table-responsive"><table class="table table-hover align-middle"><thead><tr><th>Sản phẩm</th><th>Biến thể</th><th>Đơn giá</th><th>SL</th><th>Thành tiền</th></tr></thead><tbody>
      <?php foreach ($items as $it): ?><tr><td><?= e($it['product_name']) ?><br><small class="muted"><?= e($it['sku']) ?></small></td><td><?= e($it['color']) ?>/<?= e($it['size']) ?></td><td><?= e(money($it['unit_price'])) ?></td><td><?= (int) $it['quantity'] ?></td><td><?= e(money($it['subtotal'])) ?></td></tr><?php endforeach; ?>
    </tbody></table></div>
    <dl class="sum"><dt>Tạm tính</dt><dd><?= e(money($o['subtotal'])) ?></dd><dt>Giảm giá<?= $o['voucher_code'] ? ' (' . e($o['voucher_code']) . ')' : '' ?></dt><dd>− <?= e(money($o['discount_amount'])) ?></dd><dt>Phí ship</dt><dd><?= e(money($o['shipping_fee'])) ?></dd><dt class="total">Tổng</dt><dd class="total"><?= e(money($o['total_amount'])) ?></dd></dl></div></div>
    <div class="card"><div class="card-body"><h3>Lịch sử trạng thái</h3><ul class="timeline"><?php foreach ($history as $h): ?><li><span class="badge <?= e(status_class($h['new_status'])) ?>"><?= e(status_label($h['new_status'])) ?></span> <small class="muted"><?= e(date('d/m/Y H:i', strtotime($h['created_at']))) ?> · <?= e($h['full_name'] ?? 'Hệ thống') ?><?= $h['note'] ? ' · ' . e($h['note']) : '' ?></small></li><?php endforeach; ?></ul></div></div>
  </div>
  <aside class="c-4 t-8 m-4">
    <div class="card"><div class="card-body"><h3><?= e($o['order_code']) ?></h3>
      <p>Trạng thái: <span class="badge <?= e(status_class($o['order_status'])) ?>"><?= e(status_label($o['order_status'])) ?></span><br>Thanh toán: <?= e(strtoupper($o['payment_method'])) ?> · <?= e($o['payment_status']) ?><br>Ngày đặt: <?= e(date('d/m/Y H:i', strtotime($o['created_at']))) ?></p>
      <p><strong><?= e($o['recipient_name']) ?></strong><br><?= e($o['recipient_phone']) ?><br><?= e($o['recipient_email']) ?><br><?= e($o['shipping_address']) ?></p>
      <?php if ($o['note']): ?><p class="muted">Ghi chú: <?= e($o['note']) ?></p><?php endif; ?>
      <?php if ($o['user_id']): ?><p>Tài khoản: <a href="<?= e(url('/admin/users/view/' . $o['user_id'])) ?>"><?= e($o['user_name']) ?></a></p><?php endif; ?>
    </div></div>
    <?php $next = Order::$flow[$o['order_status']]; if ($next): ?>
    <div class="card"><div class="card-body"><h3>Cập nhật trạng thái</h3>
      <form method="post" action="<?= e(url('/admin/orders/status/' . $o['id'])) ?>"><?= csrf_field() ?>
        <label>Chuyển sang<select class="form-select" name="new_status"><?php foreach ($next as $n): ?><option value="<?= $n ?>"><?= e(status_label($n)) ?></option><?php endforeach; ?></select></label>
        <label>Ghi chú<input class="form-control" type="text" name="note" maxlength="255"></label>
        <button class="btn btn-block" type="submit">Cập nhật</button></form>
      <p class="muted">Huỷ đơn sẽ hoàn lại tồn kho (nếu đã trừ) và lượt dùng voucher.</p></div></div>
    <?php else: ?><p class="muted">Đơn hàng đã ở trạng thái cuối, không thể đổi.</p><?php endif; ?>
  </aside>
</div>
