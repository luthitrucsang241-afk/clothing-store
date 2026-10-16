<div class="container section auth-wrap"><div class="card auth-card"><div class="card-body">
  <h1><?= e(t('auth.register')) ?></h1>
  <form method="post" action="<?= e(url('/register')) ?>" novalidate>
    <?= csrf_field() ?>
    <label><?= e(t('auth.full_name')) ?><input class="form-control" type="text" name="full_name" value="<?= e(old('full_name')) ?>" required><?= field_error('full_name') ?></label>
    <label><?= e(t('auth.email')) ?><input class="form-control" type="email" name="email" value="<?= e(old('email')) ?>" required><?= field_error('email') ?></label>
    <label><?= e(t('auth.phone')) ?><input class="form-control" type="tel" name="phone" value="<?= e(old('phone')) ?>"><?= field_error('phone') ?></label>
    <label><?= e(t('auth.password')) ?><input class="form-control" type="password" name="password" required autocomplete="new-password"><?= field_error('password') ?></label>
    <label><?= e(t('auth.password_confirm')) ?><input class="form-control" type="password" name="password_confirm" required autocomplete="new-password"><?= field_error('password_confirm') ?></label>
    <button class="btn btn-block" type="submit"><?= e(t('auth.register')) ?></button>
  </form>
  <p class="center muted"><?= e(t('auth.have_account')) ?> <a href="<?= e(url('/login')) ?>"><?= e(t('auth.login')) ?></a></p>
</div></div></div>
