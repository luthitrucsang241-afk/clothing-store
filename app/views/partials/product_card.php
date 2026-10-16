<?php /** @var array $p */
$onSale = $p['sale_price'] !== null && (float) $p['sale_price'] < (float) $p['price'];
$pct = $onSale ? round(100 - (float) $p['sale_price'] / (float) $p['price'] * 100) : 0; ?>
<article class="card product-card">
  <a class="thumb" href="<?= e(url('/products/detail?id=' . $p['id'])) ?>">
    <?= img($p['main_image'], $p['name']) ?>
    <?php if ($onSale): ?><span class="tag tag-sale">-<?= $pct ?>%</span><?php elseif (!empty($p['is_new'])): ?><span class="tag"><?= e(t('product.new')) ?></span><?php endif; ?>
    <?php if ((int) $p['stock'] <= 0): ?><span class="tag tag-out"><?= e(t('product.out')) ?></span><?php endif; ?>
  </a>
  <div class="card-body">
    <small class="muted"><?= e(loc(['name' => $p['category_name'], 'name_en' => $p['category_name_en']], 'name')) ?></small>
    <h3><a href="<?= e(url('/products/detail?id=' . $p['id'])) ?>"><?= e($p['name']) ?></a></h3>
    <div class="price"><strong><?= e(money($p['final_price'])) ?></strong><?php if ($onSale): ?> <s><?= e(money($p['price'])) ?></s><?php endif; ?></div>
    <?php if ((int) $p['stock'] > 0): ?><a class="btn btn-sm btn-block mt-2" href="<?= e(url('/products/detail?id=' . $p['id'])) ?>">&#128722; <?= e(lang() === 'en' ? 'Add to cart' : 'Chọn mua') ?></a><?php endif; ?>
  </div>
</article>
