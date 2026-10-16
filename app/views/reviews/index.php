<div class="container section">
  <div class="section-head"><h1><?= e(t('reviews.mine')) ?></h1></div>
  <?php if (!$reviews): ?><div class="card empty"><div class="card-body center"><?= e(t('reviews.none')) ?></div></div><?php endif; ?>
  <?php foreach ($reviews as $r): ?>
    <div class="review"><a href="<?= e(url('/products/detail?id=' . $r['pid'])) ?>"><strong><?= e($r['product_name']) ?></strong></a>
      <span class="stars"><?= str_repeat('★', (int) $r['rating']) . str_repeat('☆', 5 - (int) $r['rating']) ?></span>
      <small class="muted"><?= e(date('d/m/Y', strtotime($r['created_at']))) ?></small><?= $r['status'] ? '' : ' <span class="badge bad">' . e(t('reviews.hidden')) . '</span>' ?>
      <p><?= nl2br(e($r['comment'])) ?></p></div>
  <?php endforeach; ?>
</div>
