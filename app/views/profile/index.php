<div class="container section">
  <div class="section-head"><h1><?= e(t('profile.title')) ?></h1></div>
  <div class="grid">
    <div class="c-6 t-4 m-4"><div class="card"><div class="card-body">
      <h3><?= e(t('profile.info')) ?></h3>
      <form method="post" action="<?= e(url('/profile/update')) ?>" novalidate><?= csrf_field() ?>
        <label>Email<input class="form-control" type="email" value="<?= e($u['email']) ?>" disabled></label>
        <label><?= e(t('auth.full_name')) ?><input class="form-control" type="text" name="full_name" value="<?= e(old('full_name', $u['full_name'])) ?>" required><?= field_error('full_name') ?></label>
        <label><?= e(t('auth.phone')) ?><input class="form-control" type="tel" name="phone" value="<?= e(old('phone', $u['phone'] ?? '')) ?>"><?= field_error('phone') ?></label>
        <label><?= e(t('profile.address')) ?><input class="form-control" type="text" name="address" value="<?= e(old('address', $u['address'] ?? '')) ?>"><?= field_error('address') ?></label>
        <button class="btn" type="submit"><?= e(t('profile.save')) ?></button>
      </form>
    </div></div></div>
    <div class="c-6 t-4 m-4">
      <div class="card"><div class="card-body">
        <h3><?= e(t('profile.membership')) ?></h3>
        <p><span class="badge ok"><?= e($ms['current']['name'] ?? 'Silver') ?></span> · <?= e(t('profile.spent')) ?>: <strong><?= e(money($ms['spent'])) ?></strong></p>
        <?php if ($ms['next']): ?><p class="muted"><?= e(t('profile.next_level', $ms['next']['name'], money(max(0, $ms['next']['minimum_spending'] - $ms['spent'])))) ?></p><?php endif; ?>
        <ul><?php foreach ($levels as $l): ?><li><strong><?= e($l['name']) ?></strong>: ≥ <?= e(money($l['minimum_spending'])) ?> — <?= e($l['description']) ?></li><?php endforeach; ?></ul>
      </div></div>
      <div class="card"><div class="card-body">
        <h3><?= e(t('profile.change_pw')) ?></h3>
        <form method="post" action="<?= e(url('/profile/password')) ?>" novalidate><?= csrf_field() ?>
          <label><?= e(t('profile.current_pw')) ?><input class="form-control" type="password" name="current_password" required><?= field_error('current_password') ?></label>
          <label><?= e(t('auth.new_password')) ?><input class="form-control" type="password" name="password" required><?= field_error('password') ?></label>
          <label><?= e(t('auth.password_confirm')) ?><input class="form-control" type="password" name="password_confirm" required><?= field_error('password_confirm') ?></label>
          <button class="btn" type="submit"><?= e(t('profile.change_pw')) ?></button>
        </form>
      </div></div>
      <p><a class="btn btn-ghost" href="<?= e(url('/orders')) ?>"><?= e(t('orders.title')) ?></a></p>
    </div>
  </div>
</div>
