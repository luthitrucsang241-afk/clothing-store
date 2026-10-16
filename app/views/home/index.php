<?php
function product_section(string $title, array $items, string $link = '', string $id = ''): void {
    if (!$items) { return; } ?>
<section class="section container" <?= $id ? 'id="' . e($id) . '"' : '' ?>>
  <div class="section-head"><h2><?= e($title) ?></h2><?php if ($link): ?><a href="<?= e(url($link)) ?>"><?= e(t('home.view_all')) ?> &rarr;</a><?php endif; ?></div>
  <div class="grid">
    <?php foreach ($items as $p): ?><div class="c-3 t-2 m-2"><?php include APP . '/views/partials/product_card.php'; ?></div><?php endforeach; ?>
  </div>
</section>
<?php } ?>

<?php if ($hero): ?>
<section class="hero" data-slider aria-roledescription="carousel">
  <div class="slides">
    <?php foreach ($hero as $i => $b): ?>
      <a class="slide g<?= ($i % 3) + 1 ?> <?= $i === 0 ? 'active' : '' ?>" href="<?= e(url($b['link_url'] ?: '/products')) ?>">
        <picture>
          <?php if ($b['image_mobile']): ?><source media="(max-width: 639px)" srcset="<?= e(media($b['image_mobile'])) ?>"><?php endif; ?>
          <img src="<?= e(media($b['image_desktop'])) ?>" alt="<?= e(loc($b, 'title')) ?>" <?= $i > 0 ? 'loading="lazy"' : '' ?> onerror="this.remove()">
        </picture>
        <div class="slide-text container">
          <h2><?= e(loc($b, 'title')) ?></h2>
          <p><?= e(loc($b, 'subtitle')) ?></p>
          <?php if (loc($b, 'button_text')): ?><span class="btn"><?= e(loc($b, 'button_text')) ?></span><?php endif; ?>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
  <?php if (count($hero) > 1): ?>
    <button type="button" class="slider-btn prev" aria-label="Prev">&#10094;</button>
    <button type="button" class="slider-btn next" aria-label="Next">&#10095;</button>
    <div class="dots"><?php foreach ($hero as $i => $b): ?><button type="button" class="<?= $i === 0 ? 'active' : '' ?>" aria-label="Slide <?= $i + 1 ?>"></button><?php endforeach; ?></div>
  <?php endif; ?>
</section>
<?php endif; ?>

<?php if ($promo): ?>
<section class="container section tight">
  <div class="grid">
    <?php foreach ($promo as $b): ?>
      <a class="c-4 t-8 m-4 promo-banner" href="<?= e(url($b['link_url'] ?: '/products')) ?>">
        <?= img($b['image_desktop'], loc($b, 'title')) ?>
        <span class="promo-text"><strong><?= e(loc($b, 'title')) ?></strong><small><?= e(loc($b, 'subtitle')) ?></small></span>
      </a>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<section class="section container">
  <div class="section-head"><h2><?= e(t('home.categories')) ?></h2></div>
  <div class="grid">
    <?php foreach ($categories as $c): ?>
      <a class="c-2 t-2 m-2 cat-tile" href="<?= e(url('/products?category=' . $c['slug'])) ?>">
        <span class="cat-img"><?= img($c['image'], loc($c, 'name')) ?></span>
        <strong><?= e(loc($c, 'name')) ?></strong><small class="muted"><?= (int) $c['total'] ?> <?= e(t('home.items')) ?></small>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<?php product_section(t('home.featured'), $featured, '/products'); ?>
<?php product_section(t('home.new'), $newest, '/products?sort=newest'); ?>

<?php if ($promotions): ?>
<section class="section container">
  <div class="section-head"><h2><?= e(t('home.promotions')) ?></h2></div>
  <div class="grid">
    <?php foreach ($promotions as $pr): ?>
      <div class="c-6 t-4 m-4 card promo-card"><div class="card-body"><h3><?= e($pr['name']) ?></h3><p class="muted"><?= e($pr['description']) ?></p>
        <?php if ($pr['end_at']): ?><small><?= e(t('home.until')) ?> <?= e(date('d/m/Y', strtotime($pr['end_at']))) ?></small><?php endif; ?></div></div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<?php product_section(t('home.sale'), $sale, '/products?sort=price_asc', 'sale'); ?>

<?php foreach ($middle as $b): ?>
<section class="container section tight">
  <a class="mid-banner g2" href="<?= e(url($b['link_url'] ?: '/products')) ?>">
    <picture>
      <?php if ($b['image_mobile']): ?><source media="(max-width: 639px)" srcset="<?= e(media($b['image_mobile'])) ?>"><?php endif; ?>
      <img src="<?= e(media($b['image_desktop'])) ?>" alt="" loading="lazy" onerror="this.remove()">
    </picture>
    <div class="slide-text"><h2><?= e(loc($b, 'title')) ?></h2><p><?= e(loc($b, 'subtitle')) ?></p><span class="btn"><?= e(loc($b, 'button_text')) ?></span></div>
  </a>
</section>
<?php endforeach; ?>

<?php product_section(t('home.best'), $best, '/products?sort=best'); ?>

<section class="section container brand-intro">
  <div class="grid">
    <div class="c-6 t-8 m-4"><h2><?= e(t('home.brand_title')) ?></h2><p><?= e(t('home.brand_text')) ?></p><a class="btn" href="<?= e(url('/about')) ?>"><?= e(t('home.learn_more')) ?></a></div>
    <div class="c-6 t-8 m-4">
      <form class="newsletter card" method="post" action="<?= e(url('/newsletter')) ?>">
        <?= csrf_field() ?><h3><?= e(t('home.newsletter')) ?></h3><p class="muted"><?= e(t('home.newsletter_text')) ?></p>
        <div class="hrow"><input class="form-control" type="email" name="email" required placeholder="email@example.com" aria-label="Email"><button class="btn" type="submit"><?= e(t('home.subscribe')) ?></button></div>
      </form>
    </div>
  </div>
</section>
