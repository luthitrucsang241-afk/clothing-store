<div class="container section">
  <div class="section-head"><h1><?= e(t('nav.categories')) ?></h1></div>
  <div class="grid">
    <?php foreach ($categories as $c): ?>
      <a class="c-3 t-4 m-2 cat-tile" href="<?= e(url('/products?category=' . $c['slug'])) ?>">
        <span class="cat-img"><?= img($c['image'], loc($c, 'name')) ?></span>
        <strong><?= e(loc($c, 'name')) ?></strong><small class="muted"><?= (int) $c['total'] ?> <?= e(t('home.items')) ?></small>
      </a>
    <?php endforeach; ?>
  </div>
</div>
