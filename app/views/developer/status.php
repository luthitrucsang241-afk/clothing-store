<div class="card"><div class="card-body"><h3>Kiểm tra hệ thống</h3>
  <table class="table table-hover align-middle"><tbody><?php foreach ($checks as $name => $c): ?><tr><td><?= e($name) ?></td><td><span class="badge <?= $c['ok'] ? 'ok' : 'bad' ?>"><?= $c['ok'] ? 'OK' : 'Lỗi / Cần xem' ?></span></td><td><?= e($c['info']) ?></td></tr><?php endforeach; ?></tbody></table>
  <h4>Ứng dụng</h4><ul><?php foreach ($app as $k => $v): ?><li><?= e($k) ?>: <?= e($v) ?></li><?php endforeach; ?></ul>
  <p class="muted">Trang này không hiển thị mật khẩu hay cấu hình bí mật, và không cho chạy SQL tuỳ ý.</p>
</div></div>
