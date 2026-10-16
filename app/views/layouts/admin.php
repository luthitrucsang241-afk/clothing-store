<!DOCTYPE html>
<html lang="vi" data-theme="light" data-bs-theme="light">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e(($title ?? 'Admin') . ' | NOIRVA Admin') ?></title>
<script>try{var t=localStorage.getItem('noirva-theme');if(!t){t=matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light'}document.documentElement.setAttribute('data-theme',t);document.documentElement.setAttribute('data-bs-theme',t)}catch(e){}</script>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="<?= e(asset('css/theme.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('css/responsive.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('css/bootstrap-bridge.css')) ?>">
</head>
<body class="admin-body">
<?php
$cur = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$menu = [['/admin', 'Dashboard'], ['/admin/products', 'Sản phẩm'], ['/admin/categories', 'Danh mục'], ['/admin/orders', 'Đơn hàng'], ['/admin/users', 'Khách hàng'],
    ['/admin/employees', 'Nhân viên'], ['/admin/vouchers', 'Voucher'], ['/admin/promotions', 'Khuyến mãi'], ['/admin/banners', 'Banner'],
    ['/admin/reviews', 'Đánh giá'], ['/admin/contacts', 'Liên hệ'], ['/admin/reports', 'Báo cáo']];
if (!is_admin()) { $menu = []; }
$menu[] = ['/developer', 'Trạng thái hệ thống']; $menu[] = ['/developer/logs', 'Nhật ký lỗi'];
?>
<div class="admin-wrap">
  <aside class="admin-side" id="admin-side">
    <a class="logo" href="<?= e(url('/admin')) ?>">NOIRVA <small>Admin</small></a>
    <nav>
      <?php foreach ($menu as [$href, $label]): $active = rtrim($cur, '/') === rtrim(url($href), '/') || ($href !== '/admin' && strpos($cur, url($href) . '/') === 0); ?>
        <a href="<?= e(url($href)) ?>" class="<?= $active ? 'active' : '' ?>"><?= e($label) ?></a>
      <?php endforeach; ?>
    </nav>
    <div class="admin-side-foot">
      <a href="<?= e(url('/')) ?>" target="_blank">&rarr; Xem website</a>
      <form method="post" action="<?= e(url('/logout')) ?>"><?= csrf_field() ?><button class="link-btn" type="submit">Đăng xuất (<?= e(user()['name']) ?>)</button></form>
    </div>
  </aside>
  <section class="admin-main">
    <div class="admin-top">
      <button class="icon-btn nav-toggle" type="button" onclick="document.getElementById('admin-side').classList.toggle('open')" aria-label="Menu">&#9776;</button>
      <h1><?= e($title ?? 'Admin') ?></h1>
      <button class="icon-btn" type="button" data-theme-toggle aria-label="Theme">&#9680;</button>
    </div>
    <div class="toasts" id="toasts">
      <?php foreach (flashes() as $f): ?>
        <div class="alert alert-<?= e(['success' => 'success', 'error' => 'danger', 'warning' => 'warning'][$f['t']] ?? 'info') ?> alert-dismissible fade show" role="alert"><?= e($f['m']) ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
      <?php endforeach; ?>
    </div>
    <div class="admin-content"><?= $content ?></div>
  </section>
</div>
<?php $modalVi = true; require APP . '/views/partials/confirm_modal.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= e(asset('js/theme.js')) ?>"></script>
<script src="<?= e(asset('js/main.js')) ?>"></script>
</body>
</html>
