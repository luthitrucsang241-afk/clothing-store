<div class="stats">
  <?php foreach ([['Sản phẩm', $s['products']], ['Khách hàng', $s['customers']], ['Tổng đơn hàng', $s['orders']], ['Chờ xác nhận', $s['pending']], ['Đã giao', $s['delivered']], ['Doanh thu (đã giao)', money($s['revenue'])], ['Doanh thu tháng này', money($s['rev_month'])]] as [$l, $v]): ?>
    <div class="stat"><small><?= e($l) ?></small><strong><?= e($v) ?></strong></div>
  <?php endforeach; ?>
</div>
<p class="muted">Doanh thu chỉ tính đơn đã giao thành công; đơn đã huỷ không được tính.</p>
<div class="grid">
  <div class="c-7 t-8 m-4"><div class="card"><div class="card-body"><h3>Đơn hàng mới nhất</h3>
    <div class="table-wrap table-responsive"><table class="table table-hover align-middle"><thead><tr><th>Mã</th><th>Khách</th><th>Tổng</th><th>Trạng thái</th></tr></thead><tbody>
      <?php foreach ($latest as $o): ?><tr><td><a href="<?= e(url('/admin/orders/view/' . $o['id'])) ?>"><?= e($o['order_code']) ?></a></td><td><?= e($o['recipient_name']) ?></td><td><?= e(money($o['total_amount'])) ?></td><td><span class="badge <?= e(status_class($o['order_status'])) ?>"><?= e(status_label($o['order_status'])) ?></span></td></tr><?php endforeach; ?>
    </tbody></table></div></div></div></div>
  <div class="c-5 t-8 m-4">
    <div class="card"><div class="card-body"><h3>Sản phẩm bán chạy</h3><ol><?php foreach ($top as $t): ?><li><?= e($t['product_name']) ?> — <strong><?= (int) $t['qty'] ?></strong></li><?php endforeach; ?><?php if (!$top): ?><li class="muted">Chưa có dữ liệu</li><?php endif; ?></ol></div></div>
    <div class="card"><div class="card-body"><h3>Tồn kho thấp (≤10)</h3><ul><?php foreach ($low as $l): ?><li><?= e($l['name']) ?> <small class="muted"><?= e($l['color']) ?>/<?= e($l['size']) ?></small> — <strong><?= (int) $l['stock_quantity'] ?></strong></li><?php endforeach; ?></ul></div></div>
  </div>
</div>
<?php $max = 1; foreach ($days as $d) { $max = max($max, (float) $d['revenue']); } ?>
<div class="card"><div class="card-body"><h3>Doanh thu 14 ngày gần nhất (theo ngày đặt)</h3>
  <?php if (!$days): ?><p class="muted">Chưa có đơn đã giao trong khoảng này.</p><?php endif; ?>
  <div class="bars"><?php foreach ($days as $d): ?><div class="bar" title="<?= e($d['d'] . ': ' . money($d['revenue'])) ?>"><span style="height:<?= max(4, round($d['revenue'] / $max * 100)) ?>%"></span><small><?= e(date('d/m', strtotime($d['d']))) ?></small></div><?php endforeach; ?></div>
</div></div>
