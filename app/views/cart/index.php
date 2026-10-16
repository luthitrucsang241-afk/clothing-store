<div class="container section">
  <div class="section-head"><h1><?= e(t('cart.title')) ?></h1></div>
  <?php if (!$s['items']): ?>
    <div class="card empty"><div class="card-body center"><p><?= e(t('cart.empty')) ?></p><a class="btn" href="<?= e(url('/products')) ?>"><?= e(t('cart.continue')) ?></a></div></div>
  <?php else: ?>
  <div class="grid">
    <div class="c-8 t-8 m-4">
      <form method="post" action="<?= e(url('/cart/update')) ?>">
        <?= csrf_field() ?>
        <table class="table cart-table">
          <thead><tr><th><?= e(t('cart.product')) ?></th><th><?= e(t('cart.price')) ?></th><th><?= e(t('cart.qty')) ?></th><th><?= e(t('cart.line')) ?></th><th></th></tr></thead>
          <tbody>
          <?php foreach ($s['items'] as $it): ?>
            <tr class="<?= $it['available'] ? '' : 'row-bad' ?>">
              <td data-label="<?= e(t('cart.product')) ?>">
                <div class="cart-prod"><?= img($it['main_image'], $it['name']) ?>
                  <div><a href="<?= e(url('/products/detail?id=' . $it['pid'])) ?>"><strong><?= e($it['name']) ?></strong></a>
                  <small class="muted"><?= e($it['color']) ?> / <?= e($it['size']) ?></small>
                  <?php if (!$it['available']): ?><small class="field-error"><?= e(t('cart.unavailable', (int) $it['stock_quantity'])) ?></small><?php endif; ?></div></div>
              </td>
              <td data-label="<?= e(t('cart.price')) ?>"><?= e(money($it['unit'])) ?></td>
              <td data-label="<?= e(t('cart.qty')) ?>"><input class="form-control qty-input" type="number" name="qty[<?= (int) $it['vid'] ?>]" value="<?= (int) $it['qty'] ?>" min="0" max="<?= max(1, (int) $it['stock_quantity']) ?>"></td>
              <td data-label="<?= e(t('cart.line')) ?>"><strong><?= e(money($it['line'])) ?></strong></td>
              <td><button class="link-btn danger" type="submit" form="rm-<?= (int) $it['vid'] ?>" aria-label="<?= e(t('cart.remove')) ?>">&times; <?= e(t('cart.remove')) ?></button></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        <div class="hrow"><button class="btn btn-ghost" type="submit"><?= e(t('cart.update')) ?></button><a class="btn btn-ghost" href="<?= e(url('/products')) ?>"><?= e(t('cart.continue')) ?></a></div>
      </form>
      <?php foreach ($s['items'] as $it): ?>
        <form id="rm-<?= (int) $it['vid'] ?>" method="post" action="<?= e(url('/cart/remove')) ?>"><?= csrf_field() ?><input type="hidden" name="variant_id" value="<?= (int) $it['vid'] ?>"></form>
      <?php endforeach; ?>
    </div>
    <aside class="c-4 t-8 m-4">
      <div class="card"><div class="card-body">
        <h3><?= e(t('cart.summary')) ?></h3>
        <?php if ($s['voucher']): ?>
          <p class="alert alert-ok"><?= e(t('cart.voucher_on')) ?>: <strong><?= e($s['voucher']['code']) ?></strong>
            <form method="post" action="<?= e(url('/cart/voucher-remove')) ?>" class="inline"><?= csrf_field() ?><button class="link-btn danger" type="submit">&times;</button></form></p>
        <?php else: ?>
          <form method="post" action="<?= e(url('/cart/voucher')) ?>" class="hrow"><?= csrf_field() ?>
            <input class="form-control" type="text" name="code" placeholder="<?= e(t('cart.voucher')) ?>" aria-label="<?= e(t('cart.voucher')) ?>"><button class="btn btn-ghost" type="submit"><?= e(t('cart.apply')) ?></button></form>
        <?php endif; ?>
        <dl class="sum">
          <dt><?= e(t('cart.subtotal')) ?></dt><dd><?= e(money($s['subtotal'])) ?></dd>
          <dt><?= e(t('cart.discount')) ?></dt><dd>− <?= e(money($s['discount'])) ?></dd>
          <dt><?= e(t('cart.shipping')) ?></dt><dd><?= $s['shipping'] > 0 ? e(money($s['shipping'])) : e(t('cart.free')) ?></dd>
          <dt class="total"><?= e(t('cart.total')) ?></dt><dd class="total"><?= e(money($s['total'])) ?></dd>
        </dl>
        <?php if ($s['ok']): ?><a class="btn btn-block" href="<?= e(url('/checkout')) ?>"><?= e(t('cart.checkout')) ?></a>
        <?php else: ?><p class="alert alert-error"><?= e(t('cart.fix_items')) ?></p><?php endif; ?>
      </div></div>
    </aside>
  </div>
  <?php endif; ?>
</div>
