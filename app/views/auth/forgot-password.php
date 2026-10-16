<?php $method = old('method', 'email') === 'phone' ? 'phone' : 'email'; ?>
<div class="container section auth-wrap"><div class="card auth-card"><div class="card-body">
  <h1><?= e(t('auth.forgot')) ?></h1>
  <p class="muted"><?= e(t('auth.forgot_text')) ?></p>
  <form method="post" action="<?= e(url('/forgot-password')) ?>" id="forgot-form" novalidate>
    <?= csrf_field() ?>
    <div class="method-tabs" role="tablist">
      <label class="method-tab"><input type="radio" name="method" value="email" <?= $method === 'email' ? 'checked' : '' ?>><span><?= e(t('auth.by_email')) ?></span></label>
      <label class="method-tab"><input type="radio" name="method" value="phone" <?= $method === 'phone' ? 'checked' : '' ?>><span><?= e(t('auth.by_phone')) ?></span></label>
    </div>
    <label data-method="email"><?= e(t('auth.email')) ?><input class="form-control" type="email" name="email" value="<?= e(old('email')) ?>" autocomplete="email"><?= field_error('email') ?></label>
    <label data-method="phone"><?= e(t('auth.phone')) ?><input class="form-control" type="tel" name="phone" value="<?= e(old('phone')) ?>" placeholder="0901234567" autocomplete="tel"><?= field_error('phone') ?></label>
    <button class="btn btn-block" type="submit"><?= e(t('auth.send_code')) ?></button>
  </form>
  <p class="center"><a href="<?= e(url('/login')) ?>">&larr; <?= e(t('auth.login')) ?></a></p>
</div></div></div>
<script>
(function () {
  var f = document.getElementById('forgot-form');
  function sync() {
    var m = f.querySelector('input[name=method]:checked').value;
    f.querySelectorAll('[data-method]').forEach(function (el) { el.style.display = el.getAttribute('data-method') === m ? '' : 'none'; });
  }
  f.querySelectorAll('input[name=method]').forEach(function (r) { r.addEventListener('change', sync); });
  sync();
})();
</script>
