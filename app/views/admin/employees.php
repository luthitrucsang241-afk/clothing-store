<div class="table-wrap table-responsive"><table class="table table-hover align-middle"><thead><tr><th>Mã NV</th><th>Họ tên</th><th>Email</th><th>Vai trò</th><th>Chức vụ</th><th>Trạng thái</th><th></th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?= e($r['employee_code']) ?></td><td><?= e($r['full_name']) ?></td><td><?= e($r['email']) ?></td><td><?= e($r['role_name']) ?></td><td><?= e($r['position']) ?></td>
  <td><span class="badge <?= $r['status'] ? 'ok' : 'bad' ?>"><?= $r['status'] ? 'Hoạt động' : 'Đã khoá' ?></span></td>
  <td><form method="post" action="<?= e(url('/admin/employees/toggle/' . $r['id'])) ?>"><?= csrf_field() ?><button class="btn btn-sm btn-ghost" type="submit"><?= $r['status'] ? 'Khoá' : 'Mở khoá' ?></button></form></td></tr>
<?php endforeach; ?></tbody></table></div>
<form class="card" method="post" action="<?= e(url('/admin/employees/save')) ?>"><div class="card-body"><h3>Thêm nhân viên</h3><?= csrf_field() ?>
  <div class="row2"><label>Họ tên<input class="form-control" type="text" name="full_name" value="<?= e(old('full_name')) ?>" required></label><label>Email<input class="form-control" type="email" name="email" value="<?= e(old('email')) ?>" required></label></div>
  <div class="row2"><label>Chức vụ<input class="form-control" type="text" name="position" value="<?= e(old('position')) ?>"></label>
    <label>Vai trò<select class="form-select" name="role_id"><option value="2">Admin</option><option value="3">Developer</option></select></label></div>
  <label>Mật khẩu (≥8 ký tự, gồm chữ và số)<input class="form-control" type="password" name="password" required autocomplete="new-password"></label>
  <button class="btn" type="submit">Thêm nhân viên</button></div></form>
