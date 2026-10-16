<!DOCTYPE html>
<html lang="<?= e(lang()) ?>" data-theme="light" data-bs-theme="light">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e(($title ?? '') === config('name') ? config('name') : (($title ?? '') . ' | ' . config('name'))) ?></title>
<script>try{var t=localStorage.getItem('noirva-theme');if(!t){t=matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light'}document.documentElement.setAttribute('data-theme',t);document.documentElement.setAttribute('data-bs-theme',t)}catch(e){}</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="<?= e(asset('css/theme.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('css/responsive.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('css/bootstrap-bridge.css')) ?>">
</head>
<body>
<?php require APP . '/views/layouts/header.php'; ?>
<div class="toasts" id="toasts">
<?php foreach (flashes() as $f): ?>
  <div class="alert alert-<?= e(['success' => 'success', 'error' => 'danger', 'warning' => 'warning'][$f['t']] ?? 'info') ?> alert-dismissible fade show" role="alert"><?= e($f['m']) ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
<?php endforeach; ?>
</div>
<main id="main"><?= $content ?></main>
<?php require APP . '/views/layouts/footer.php'; ?>
<?php require APP . '/views/partials/confirm_modal.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= e(asset('js/theme.js')) ?>"></script>
<script src="<?= e(asset('js/main.js')) ?>"></script>
<script src="<?= e(asset('js/banner.js')) ?>"></script>
</body>
</html>
