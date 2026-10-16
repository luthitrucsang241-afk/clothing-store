<div class="container section">
  <div class="section-head"><h1><?= e(t('checkout.title')) ?></h1></div>
  <form method="post" action="<?= e(url('/checkout')) ?>" novalidate>
    <?= csrf_field() ?>
    <div class="grid">
      <div class="c-7 t-8 m-4">
        <div class="card"><div class="card-body">
          <h3><?= e(t('checkout.recipient')) ?></h3>
          <label><?= e(t('auth.full_name')) ?><input class="form-control" type="text" name="name" value="<?= e(old('name', $u['full_name'])) ?>" required><?= field_error('name') ?></label>
          <div class="row2">
            <label><?= e(t('auth.phone')) ?><input class="form-control" type="tel" name="phone" value="<?= e(old('phone', $u['phone'] ?? '')) ?>" required><?= field_error('phone') ?></label>
            <label>Email<input class="form-control" type="email" name="email" value="<?= e(old('email', $u['email'])) ?>"><?= field_error('email') ?></label>
          </div>
          <div class="row2">
            <label><?= e(t('checkout.province')) ?><input class="form-control" type="text" name="province" value="<?= e(old('province')) ?>" required><?= field_error('province') ?></label>
            <label><?= e(t('checkout.district')) ?><input class="form-control" type="text" name="district" value="<?= e(old('district')) ?>" required><?= field_error('district') ?></label>
          </div>
          <label><?= e(t('checkout.street')) ?><input class="form-control" type="text" name="street" value="<?= e(old('street', $u['address'] ?? '')) ?>" required><?= field_error('street') ?></label>
          <label><?= e(t('checkout.note')) ?><textarea class="form-control" name="note" rows="2" maxlength="500"><?= e(old('note')) ?></textarea></label>
          <h3><?= e(t('checkout.payment')) ?></h3>
          <label class="check"><input type="radio" name="payment_method" value="cod" checked> <?= e(t('checkout.cod')) ?></label>
          <small class="muted"><?= e(t('checkout.online_soon')) ?></small>
        </div></div>
      </div>
      <aside class="c-5 t-8 m-4">
        <div class="card"><div class="card-body">
          <h3><?= e(t('cart.summary')) ?></h3>
          <?php foreach ($s['items'] as $it): ?>
            <div class="mini-line"><span><?= e($it['name']) ?> <small class="muted">(<?= e($it['color']) ?>/<?= e($it['size']) ?>) × <?= (int) $it['qty'] ?></small></span><strong><?= e(money($it['line'])) ?></strong></div>
          <?php endforeach; ?>
          <dl class="sum">
            <dt><?= e(t('cart.subtotal')) ?></dt><dd><?= e(money($s['subtotal'])) ?></dd>
            <dt><?= e(t('cart.discount')) ?><?= $s['voucher'] ? ' (' . e($s['voucher']['code']) . ')' : '' ?></dt><dd>− <?= e(money($s['discount'])) ?></dd>
            <dt><?= e(t('cart.shipping')) ?></dt><dd><?= $s['shipping'] > 0 ? e(money($s['shipping'])) : e(t('cart.free')) ?></dd>
            <dt class="total"><?= e(t('cart.total')) ?></dt><dd class="total"><?= e(money($s['total'])) ?></dd>
          </dl>
          <button class="btn btn-block" type="submit"><?= e(t('checkout.place')) ?></button>
          <p class="center"><a href="<?= e(url('/cart')) ?>">&larr; <?= e(t('cart.title')) ?></a></p>
        </div></div>
      </aside>
    </div>
  </form>
</div>
