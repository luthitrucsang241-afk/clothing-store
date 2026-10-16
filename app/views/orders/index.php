<div class="container section">
  <div class="section-head"><h1><?= e(t('orders.title')) ?></h1></div>
  <?php if (!$orders): ?>
    <div class="card empty"><div class="card-body center"><p><?= e(t('orders.none')) ?></p><a class="btn" href="<?= e(url('/products')) ?>"><?= e(t('cart.continue')) ?></a></div></div>
  <?php else: ?>
  <table class="table cart-table">
    <thead><tr><th><?= e(t('orders.code')) ?></th><th><?= e(t('orders.date')) ?></th><th><?= e(t('cart.total')) ?></th><th><?= e(t('orders.payment')) ?></th><th><?= e(t('orders.status')) ?></th><th></th></tr></thead>
    <tbody>
    <?php foreach ($orders as $o): ?>
      <tr>
        <td data-label="<?= e(t('orders.code')) ?>"><strong><?= e($o['order_code']) ?></strong></td>
        <td data-label="<?= e(t('orders.date')) ?>"><?= e(date('d/m/Y H:i', strtotime($o['created_at']))) ?></td>
        <td data-label="<?= e(t('cart.total')) ?>"><?= e(money($o['total_amount'])) ?></td>
        <td data-label="<?= e(t('orders.payment')) ?>"><?= e(strtoupper($o['payment_method'])) ?> · <?= e(t('pay.' . $o['payment_status'])) ?></td>
        <td data-label="<?= e(t('orders.status')) ?>"><span class="badge <?= e(status_class($o['order_status'])) ?>"><?= e(status_label($o['order_status'])) ?></span></td>
        <td><a class="btn btn-sm btn-ghost" href="<?= e(url('/orders/detail?id=' . $o['id'])) ?>"><?= e(t('orders.view')) ?></a></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>
