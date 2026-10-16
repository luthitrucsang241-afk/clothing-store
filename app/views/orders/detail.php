<div class="container section">
  <div class="breadcrumb"><a href="<?= e(url('/orders')) ?>"><?= e(t('orders.title')) ?></a> / <?= e($o['order_code']) ?></div>
  <div class="section-head"><h1><?= e(t('orders.order')) ?> <?= e($o['order_code']) ?></h1><span class="badge <?= e(status_class($o['order_status'])) ?>"><?= e(status_label($o['order_status'])) ?></span></div>
  <div class="grid">
    <div class="c-8 t-8 m-4">
      <table class="table cart-table">
        <thead><tr><th><?= e(t('cart.product')) ?></th><th><?= e(t('cart.price')) ?></th><th><?= e(t('cart.qty')) ?></th><th><?= e(t('cart.line')) ?></th></tr></thead>
        <tbody><?php foreach ($items as $it): ?>
          <tr><td data-label="<?= e(t('cart.product')) ?>"><div class="cart-prod"><?= img($it['image'], $it['product_name']) ?><div><strong><?= e($it['product_name']) ?></strong><small class="muted"><?= e($it['color']) ?> / <?= e($it['size']) ?></small>
            <?php if ($it['product_id'] && $o['order_status'] === 'delivered'): ?><a class="small" href="<?= e(url('/products/detail?id=' . $it['product_id'])) ?>"><?= e(t('reviews.write')) ?></a><?php endif; ?></div></div></td>
          <td data-label="<?= e(t('cart.price')) ?>"><?= e(money($it['unit_price'])) ?></td><td data-label="<?= e(t('cart.qty')) ?>"><?= (int) $it['quantity'] ?></td><td data-label="<?= e(t('cart.line')) ?>"><?= e(money($it['subtotal'])) ?></td></tr>
        <?php endforeach; ?></tbody>
      </table>
      <h3><?= e(t('orders.history')) ?></h3>
      <ul class="timeline"><?php foreach ($history as $h): ?>
        <li><span class="badge <?= e(status_class($h['new_status'])) ?>"><?= e(status_label($h['new_status'])) ?></span> <small class="muted"><?= e(date('d/m/Y H:i', strtotime($h['created_at']))) ?></small></li>
      <?php endforeach; ?></ul>
    </div>
    <aside class="c-4 t-8 m-4"><div class="card"><div class="card-body">
      <h3><?= e(t('checkout.recipient')) ?></h3>
      <p><strong><?= e($o['recipient_name']) ?></strong><br><?= e($o['recipient_phone']) ?><br><?= e($o['shipping_address']) ?></p>
      <?php if ($o['note']): ?><p class="muted"><?= e($o['note']) ?></p><?php endif; ?>
      <dl class="sum">
        <dt><?= e(t('cart.subtotal')) ?></dt><dd><?= e(money($o['subtotal'])) ?></dd>
        <dt><?= e(t('cart.discount')) ?></dt><dd>− <?= e(money($o['discount_amount'])) ?></dd>
        <dt><?= e(t('cart.shipping')) ?></dt><dd><?= e(money($o['shipping_fee'])) ?></dd>
        <dt class="total"><?= e(t('cart.total')) ?></dt><dd class="total"><?= e(money($o['total_amount'])) ?></dd>
      </dl>
      <p class="muted"><?= e(t('orders.payment')) ?>: <?= e(strtoupper($o['payment_method'])) ?> · <?= e(t('pay.' . $o['payment_status'])) ?></p>
      <?php if (in_array($o['order_status'], ['pending', 'confirmed'], true)): ?>
        <form method="post" action="<?= e(url('/orders/cancel')) ?>" data-confirm="<?= e(t('orders.confirm_cancel')) ?>">
          <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $o['id'] ?>"><button class="btn btn-danger btn-block" type="submit"><?= e(t('orders.cancel')) ?></button></form>
      <?php endif; ?>
    </div></div></aside>
  </div>
</div>
