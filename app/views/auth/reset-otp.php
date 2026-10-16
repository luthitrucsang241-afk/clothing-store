<div class="container section auth-wrap"><div class="card auth-card"><div class="card-body">
  <h1><?= e(t('auth.reset')) ?></h1>
  <p class="muted"><?= e(t('auth.otp_text', $phone)) ?></p>
  <form method="post" action="<?= e(url('/reset-otp')) ?>" novalidate>
    <?= csrf_field() ?>
    <label><?= e(t('auth.otp')) ?><input class="form-control" type="text" name="otp" inputmode="numeric" maxlength="6" pattern="\d{6}" placeholder="••••••" required autocomplete="one-time-code"><?= field_error('otp') ?></label>
    <label><?= e(t('auth.new_password')) ?><input class="form-control" type="password" name="password" required autocomplete="new-password"><?= field_error('password') ?></label>
    <label><?= e(t('auth.password_confirm')) ?><input class="form-control" type="password" name="password_confirm" required autocomplete="new-password"><?= field_error('password_confirm') ?></label>
    <button class="btn btn-block" type="submit"><?= e(t('auth.reset')) ?></button>
  </form>
  <p class="center"><a href="<?= e(url('/forgot-password')) ?>">&larr; <?= e(t('auth.resend_code')) ?></a></p>
</div></div></div>
