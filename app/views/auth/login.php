<div class="container section auth-wrap"><div class="card auth-card"><div class="card-body">
  <h1><?= e(t('auth.login')) ?></h1>
  <form method="post" action="<?= e(url('/login')) ?>" novalidate>
    <?= csrf_field() ?>
    <label><?= e(t('auth.email')) ?><input class="form-control" type="email" name="email" value="<?= e(old('email')) ?>" required autocomplete="email"><?= field_error('email') ?></label>
    <label><?= e(t('auth.password')) ?><input class="form-control" type="password" name="password" required autocomplete="current-password"></label>
    <label class="check-row"><input type="checkbox" name="remember" value="1"> <span><?= e(t('auth.remember')) ?></span></label>
    <button class="btn btn-block" type="submit"><?= e(t('auth.login')) ?></button>
  </form>
  <p class="center"><a href="<?= e(url('/forgot-password')) ?>"><?= e(t('auth.forgot')) ?></a></p>
  <p class="center muted"><?= e(t('auth.no_account')) ?> <a href="<?= e(url('/register')) ?>"><?= e(t('auth.register')) ?></a></p>
</div></div></div>
