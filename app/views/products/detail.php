<?php
$onSale = $p['sale_price'] !== null && (float) $p['sale_price'] < (float) $p['price'];
$base = (float) $p['final_price'];
$gallery = [$p['main_image']];
foreach ($images as $im) { if ($im['image_path'] !== $p['main_image']) { $gallery[] = $im['image_path']; } }
$vdata = array_map(fn($v) => ['id' => (int) $v['id'], 'size' => $v['size'], 'color' => $v['color'], 'code' => $v['color_code'], 'stock' => (int) $v['stock_quantity'], 'adj' => (float) $v['price_adjustment']], $variants);
$colors = []; foreach ($variants as $v) { $colors[$v['color']] = $v['color_code']; }
$sizes = array_values(array_unique(array_column($variants, 'size')));
?>
<div class="container section">
  <div class="breadcrumb"><a href="<?= e(url('/')) ?>"><?= e(t('nav.home')) ?></a> / <a href="<?= e(url('/products?category=' . $p['category_slug'])) ?>"><?= e(loc(['name' => $p['category_name'], 'name_en' => $p['category_name_en']], 'name')) ?></a> / <?= e($p['name']) ?></div>
  <div class="grid">
    <div class="c-6 t-4 m-4 gallery">
      <div class="gallery-main"><?= img($gallery[0], $p['name'], 'js-main-img') ?></div>
      <?php if (count($gallery) > 1): ?>
        <div class="gallery-thumbs"><?php foreach ($gallery as $g): ?><button type="button" data-src="<?= e(media($g)) ?>"><?= img($g, '') ?></button><?php endforeach; ?></div>
      <?php endif; ?>
    </div>
    <div class="c-6 t-4 m-4 pinfo">
      <h1><?= e($p['name']) ?></h1>
      <p class="muted">SKU: <?= e($p['sku']) ?> &middot; <?= $rating['cnt'] ? '★ ' . $rating['avg'] . ' (' . $rating['cnt'] . ')' : e(t('product.no_reviews')) ?></p>
      <div class="price big"><strong id="price" data-base="<?= e($base) ?>"><?= e(money($base)) ?></strong><?php if ($onSale): ?> <s><?= e(money($p['price'])) ?></s><?php endif; ?></div>
      <?php if ($p['description']): ?><p><?= nl2br(e($p['description'])) ?></p><?php endif; ?>
      <?php if ($p['material']): ?><p><strong><?= e(t('product.material')) ?>:</strong> <?= e($p['material']) ?></p><?php endif; ?>

      <?php if (!$variants || (int) $p['stock'] <= 0): ?>
        <p class="alert alert-error"><?= e(t('product.out_msg')) ?></p>
      <?php else: ?>
      <form method="post" action="<?= e(url('/cart/add')) ?>" id="buy-form">
        <?= csrf_field() ?>
        <input type="hidden" name="variant_id" id="variant_id" value="">
        <div class="opt"><strong><?= e(t('product.color')) ?>:</strong> <span id="color-label">—</span>
          <div class="chips" id="color-chips"><?php foreach ($colors as $name => $code): ?><button type="button" class="swatch" data-color="<?= e($name) ?>" title="<?= e($name) ?>" style="--sw:<?= e($code ?: '#ccc') ?>"><span class="sr"><?= e($name) ?></span></button><?php endforeach; ?></div>
        </div>
        <div class="opt"><strong><?= e(t('product.size')) ?>:</strong> <a href="<?= e(url('/size-guide')) ?>" class="small"><?= e(t('footer.size_guide')) ?></a>
          <div class="chips" id="size-chips"><?php foreach ($sizes as $s): ?><button type="button" class="chip-btn" data-size="<?= e($s) ?>"><?= e($s) ?></button><?php endforeach; ?></div>
        </div>
        <p class="muted" id="stock-info"><?= e(t('product.pick')) ?></p>
        <div class="opt"><strong><?= e(t('product.qty')) ?>:</strong>
          <div class="qty"><button type="button" data-qty="-1">−</button><input class="form-control" type="number" name="qty" id="qty" value="1" min="1" max="1"><button type="button" data-qty="1">+</button></div>
        </div>
        <div class="hrow">
          <button class="btn" type="submit" id="add-btn" disabled><?= e(t('product.add')) ?></button>
          <button class="btn btn-accent" type="submit" name="buy_now" value="1" id="buy-btn" disabled><?= e(t('product.buy_now')) ?></button>
        </div>
      </form>
      <script type="application/json" id="variant-data"><?= json_encode($vdata, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
      <?php endif; ?>
      <ul class="policy-mini"><li>🚚 <?= e(t('product.ship_note')) ?></li><li>↩ <?= e(t('product.return_note')) ?></li></ul>
    </div>
  </div>

  <section class="section">
    <div class="section-head"><h2><?= e(t('product.reviews')) ?> (<?= $rating['cnt'] ?>)</h2></div>
    <?php if (user() && $reviewOrder): ?>
      <form class="card review-form" method="post" action="<?= e(url('/reviews/store')) ?>"><div class="card-body">
        <?= csrf_field() ?><input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>">
        <h3><?= e(t('reviews.write')) ?></h3>
        <label><?= e(t('reviews.rating')) ?>
          <select class="form-select" name="rating" required><?php for ($i = 5; $i >= 1; $i--): ?><option value="<?= $i ?>"><?= str_repeat('★', $i) ?></option><?php endfor; ?></select></label>
        <label><?= e(t('reviews.comment')) ?><textarea class="form-control" name="comment" rows="3" maxlength="1000"></textarea></label>
        <button class="btn" type="submit"><?= e(t('reviews.submit')) ?></button>
      </div></form>
    <?php endif; ?>
    <?php if (!$reviews): ?><p class="muted"><?= e(t('product.no_reviews')) ?></p><?php endif; ?>
    <?php foreach ($reviews as $r): ?>
      <div class="review"><strong><?= e($r['full_name']) ?></strong> <span class="stars"><?= str_repeat('★', (int) $r['rating']) . str_repeat('☆', 5 - (int) $r['rating']) ?></span>
        <small class="muted"><?= e(date('d/m/Y', strtotime($r['created_at']))) ?></small>
        <p><?= nl2br(e($r['comment'])) ?></p></div>
    <?php endforeach; ?>
  </section>

  <?php if ($related): ?>
  <section class="section">
    <div class="section-head"><h2><?= e(t('product.related')) ?></h2></div>
    <div class="grid"><?php foreach ($related as $p): ?><div class="c-3 t-2 m-2"><?php include APP . '/views/partials/product_card.php'; ?></div><?php endforeach; ?></div>
  </section>
  <?php endif; ?>
</div>
