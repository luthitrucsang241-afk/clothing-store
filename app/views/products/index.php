<div class="container section">
  <div class="breadcrumb"><a href="<?= e(url('/')) ?>"><?= e(t('nav.home')) ?></a> / <?= e(t('nav.products')) ?></div>
  <div class="grid">
    <aside class="c-3 t-8 m-4">
      <button type="button" class="btn btn-ghost filter-toggle" data-filter-toggle><?= e(t('filter.title')) ?></button>
      <form class="filters card" id="filters" method="get" action="<?= e(url('/products')) ?>">
        <div class="card-body">
          <label><?= e(t('nav.search')) ?><input class="form-control" type="search" name="q" value="<?= e($f['q']) ?>"></label>
          <fieldset><legend><?= e(t('filter.category')) ?></legend>
            <label class="check"><input type="radio" name="category" value="" <?= $f['category'] === '' ? 'checked' : '' ?>> <?= e(t('filter.all')) ?></label>
            <?php foreach ($categories as $c): ?><label class="check"><input type="radio" name="category" value="<?= e($c['slug']) ?>" <?= $f['category'] === $c['slug'] ? 'checked' : '' ?>> <?= e(loc($c, 'name')) ?></label><?php endforeach; ?>
          </fieldset>
          <fieldset><legend><?= e(t('filter.price')) ?></legend>
            <div class="hrow"><input class="form-control" type="number" min="0" name="min" value="<?= e($f['min']) ?>" placeholder="<?= e(t('filter.from')) ?>"><input class="form-control" type="number" min="0" name="max" value="<?= e($f['max']) ?>" placeholder="<?= e(t('filter.to')) ?>"></div>
          </fieldset>
          <fieldset><legend><?= e(t('filter.size')) ?></legend>
            <div class="chips"><?php foreach ($opts['sizes'] as $s): ?><label class="chip"><input type="radio" name="size" value="<?= e($s) ?>" <?= $f['size'] === $s ? 'checked' : '' ?>><span><?= e($s) ?></span></label><?php endforeach; ?></div>
          </fieldset>
          <fieldset><legend><?= e(t('filter.color')) ?></legend>
            <div class="chips"><?php foreach ($opts['colors'] as $c): ?><label class="chip"><input type="radio" name="color" value="<?= e($c) ?>" <?= $f['color'] === $c ? 'checked' : '' ?>><span><?= e($c) ?></span></label><?php endforeach; ?></div>
          </fieldset>
          <label><?= e(t('filter.sort')) ?>
            <select class="form-select" name="sort">
              <?php foreach (['newest' => 'filter.newest', 'price_asc' => 'filter.price_asc', 'price_desc' => 'filter.price_desc', 'best' => 'filter.best'] as $k => $lab): ?>
                <option value="<?= $k ?>" <?= $f['sort'] === $k ? 'selected' : '' ?>><?= e(t($lab)) ?></option>
              <?php endforeach; ?>
            </select>
          </label>
          <div class="hrow"><button class="btn" type="submit"><?= e(t('filter.apply')) ?></button><a class="btn btn-ghost" href="<?= e(url('/products')) ?>"><?= e(t('filter.reset')) ?></a></div>
        </div>
      </form>
    </aside>
    <div class="c-9 t-8 m-4">
      <p class="muted"><?= e(t('filter.results', $total)) ?></p>
      <?php if (!$products): ?>
        <div class="empty card"><div class="card-body"><?= e(t('filter.none')) ?></div></div>
      <?php else: ?>
        <div class="grid">
          <?php foreach ($products as $p): ?><div class="c-4 t-2 m-2"><?php include APP . '/views/partials/product_card.php'; ?></div><?php endforeach; ?>
        </div>
        <?= pager($total, $page, $per) ?>
      <?php endif; ?>
    </div>
  </div>
</div>
