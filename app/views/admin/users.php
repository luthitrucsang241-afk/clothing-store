<form class="toolbar" method="get"><input class="form-control" type="search" name="q" value="<?= e($q) ?>" placeholder="Tìm tên, email, SĐT"><button class="btn btn-sm" type="submit">Tìm</button></form>
<div class="table-wrap table-responsive"><table class="table table-hover align-middle"><thead><tr><th>ID</th><th>Họ tên</th><th>Email</th><th>SĐT</th><th>Hạng</th><th>Đơn</th><th>Trạng thái</th><th></th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?= (int) $r['id'] ?></td><td><?= e($r['full_name']) ?></td><td><?= e($r['email']) ?></td><td><?= e($r['phone']) ?></td><td><?= e($r['level_name']) ?></td><td><?= (int) $r['orders'] ?></td>
  <td><span class="badge <?= $r['status'] ? 'ok' : 'bad' ?>"><?= $r['status'] ? 'Hoạt động' : 'Đã khoá' ?></span></td>
  <td class="actions"><a class="btn btn-sm btn-ghost" href="<?= e(url('/admin/users/view/' . $r['id'])) ?>">Xem</a>
    <form method="post" class="inline" action="<?= e(url('/admin/users/toggle/' . $r['id'])) ?>"><?= csrf_field() ?><button class="btn btn-sm btn-ghost" type="submit"><?= $r['status'] ? 'Khoá' : 'Mở khoá' ?></button></form></td></tr>
<?php endforeach; if (!$rows): ?><tr><td colspan="8" class="muted">Không có khách hàng.</td></tr><?php endif; ?></tbody></table></div>
<?= pager($total, $page, $per) ?>
