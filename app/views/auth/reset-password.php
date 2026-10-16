<div class="container section auth-wrap"><div class="card auth-card"><div class="card-body">
  <h1><?= e(t('auth.reset')) ?></h1>
  <form method="post" action="<?= e(url('/reset-password')) ?>" novalidate>
    <?= csrf_field() ?><input type="hidden" name="token" value="<?= e($token) ?>">
    <label><?= e(t('auth.new_password')) ?><input class="form-control" type="password" name="password" required autocomplete="new-password"><?= field_error('password') ?></label>
    <label><?= e(t('auth.password_confirm')) ?><input class="form-control" type="password" name="password_confirm" required autocomplete="new-password"><?= field_error('password_confirm') ?></label>
    <button class="btn btn-block" type="submit"><?= e(t('auth.reset')) ?></button>
  </form>
</div></div></div>
