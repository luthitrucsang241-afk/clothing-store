<header class="site-header">
  <div class="topbar"><div class="container"><?= e(t('topbar', money(Setting::get('free_shipping_threshold', 499000)))) ?></div></div>
  <div class="container header-main">
    <button class="icon-btn nav-toggle" type="button" data-nav-toggle aria-label="Menu" aria-expanded="false">&#9776;</button>
    <a class="logo" href="<?= e(url('/')) ?>">NOIRVA</a>
    <nav class="main-nav" id="main-nav" aria-label="Main">
      <a href="<?= e(url('/')) ?>"><?= e(t('nav.home')) ?></a>
      <a href="<?= e(url('/products')) ?>"><?= e(t('nav.products')) ?></a>
      <div class="has-sub">
        <a href="<?= e(url('/categories')) ?>"><?= e(t('nav.categories')) ?> &#9662;</a>
        <div class="sub">
          <?php foreach ($navCategories as $c): ?>
            <a href="<?= e(url('/products?category=' . $c['slug'])) ?>"><?= e(loc($c, 'name')) ?></a>
          <?php endforeach; ?>
        </div>
      </div>
      <a href="<?= e(url('/products?sort=price_asc#sale')) ?>"><?= e(t('nav.promotions')) ?></a>
      <a href="<?= e(url('/about')) ?>"><?= e(t('nav.about')) ?></a>
      <a href="<?= e(url('/contact')) ?>"><?= e(t('nav.contact')) ?></a>
    </nav>
    <form class="search" action="<?= e(url('/products')) ?>" method="get" role="search">
      <input class="form-control" type="search" name="q" value="<?= e($_GET['q'] ?? '') ?>" placeholder="<?= e(t('nav.search')) ?>" aria-label="<?= e(t('nav.search')) ?>">
      <button type="submit" class="icon-btn" aria-label="<?= e(t('nav.search')) ?>">&#128269;</button>
    </form>
    <div class="header-actions">
      <span class="lang-switch">
        <a href="<?= e(url('/lang/vi')) ?>" class="<?= lang() === 'vi' ? 'active' : '' ?>">VI</a>|<a href="<?= e(url('/lang/en')) ?>" class="<?= lang() === 'en' ? 'active' : '' ?>">EN</a>
      </span>
      <button class="icon-btn" type="button" data-theme-toggle aria-label="Theme">&#9680;</button>
      <?php if (user()): ?>
        <div class="has-sub account">
          <a href="<?= e(url('/profile')) ?>" class="icon-btn" aria-label="<?= e(t('nav.account')) ?>">&#128100;<?php if (inbox_unread() > 0): ?><span class="badge-count"><?= inbox_unread() ?></span><?php endif; ?></a>
          <div class="sub sub-right">
            <span class="sub-title"><?= e(user()['name']) ?></span>
            <a href="<?= e(url('/profile')) ?>"><?= e(t('nav.profile')) ?></a>
            <a href="<?= e(url('/orders')) ?>"><?= e(t('nav.orders')) ?></a>
            <a href="<?= e(url('/inbox')) ?>"><?= e(t('nav.inbox')) ?><?= inbox_unread() > 0 ? ' (' . inbox_unread() . ')' : '' ?></a>
            <a href="<?= e(url('/reviews')) ?>"><?= e(t('reviews.mine')) ?></a>
            <?php if (user()['role'] === 'admin'): ?><a href="<?= e(url('/admin')) ?>">Admin</a><?php endif; ?>
            <?php if (in_array(user()['role'], ['admin', 'developer'], true)): ?><a href="<?= e(url('/developer')) ?>">Developer</a><?php endif; ?>
            <form method="post" action="<?= e(url('/logout')) ?>"><?= csrf_field() ?><button type="submit" class="link-btn"><?= e(t('auth.logout')) ?></button></form>
          </div>
        </div>
      <?php else: ?>
        <a class="btn btn-sm btn-ghost hide-m" href="<?= e(url('/login')) ?>"><?= e(t('auth.login')) ?></a>
        <a class="btn btn-sm hide-m" href="<?= e(url('/register')) ?>"><?= e(t('auth.register')) ?></a>
        <a class="icon-btn show-m" href="<?= e(url('/login')) ?>" aria-label="<?= e(t('auth.login')) ?>">&#128100;</a>
      <?php endif; ?>
      <a class="icon-btn cart-link" href="<?= e(url('/cart')) ?>" aria-label="<?= e(t('cart.title')) ?>">&#128722;<?php if (Cart::count() > 0): ?><span class="badge-count"><?= Cart::count() ?></span><?php endif; ?></a>
    </div>
  </div>
</header>
