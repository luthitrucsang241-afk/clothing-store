<div class="grid">
  <div class="c-4 t-8 m-4"><div class="card"><div class="card-body"><h3><?= e($u['full_name']) ?></h3>
    <p>Email: <?= e($u['email']) ?><br>SĐT: <?= e($u['phone']) ?><br>Địa chỉ: <?= e($u['address']) ?><br>Ngày tạo: <?= e(date('d/m/Y', strtotime($u['created_at']))) ?></p>
    <p>Hạng: <span class="badge ok"><?= e($ms['current']['name'] ?? '—') ?></span><br>Tổng chi tiêu (đã giao): <strong><?= e(money($ms['spent'])) ?></strong></p>
    <p>Trạng thái: <span class="badge <?= $u['status'] ? 'ok' : 'bad' ?>"><?= $u['status'] ? 'Hoạt động' : 'Đã khoá' ?></span></p></div></div></div>
  <div class="c-8 t-8 m-4"><div class="card"><div class="card-body"><h3>Lịch sử mua hàng</h3><div class="table-wrap table-responsive"><table class="table table-hover align-middle"><thead><tr><th>Mã</th><th>Ngày</th><th>Tổng</th><th>Trạng thái</th></tr></thead><tbody>
    <?php foreach ($orders as $o): ?><tr><td><a href="<?= e(url('/admin/orders/view/' . $o['id'])) ?>"><?= e($o['order_code']) ?></a></td><td><?= e(date('d/m/Y', strtotime($o['created_at']))) ?></td><td><?= e(money($o['total_amount'])) ?></td><td><span class="badge <?= e(status_class($o['order_status'])) ?>"><?= e(status_label($o['order_status'])) ?></span></td></tr><?php endforeach; if (!$orders): ?><tr><td colspan="4" class="muted">Chưa có đơn.</td></tr><?php endif; ?></tbody></table></div></div></div></div>
  <div class="c-12 t-8 m-4"><div class="card"><div class="card-body"><h3>Gửi thư cho khách hàng</h3>
    <p class="muted">Thư hiện trong mục "Hộp thư" của khách và (nếu đã cấu hình SMTP) được gửi tới email của họ.</p>
    <form method="post" action="<?= e(url('/admin/users/message/' . $u['id'])) ?>"><?= csrf_field() ?>
      <label>Tiêu đề<input class="form-control" type="text" name="subject" maxlength="200" required></label>
      <label>Nội dung<textarea class="form-control" name="body" rows="4" maxlength="3000" required></textarea></label>
      <button class="btn" type="submit">Gửi thư</button>
    </form></div></div></div>
</div>
