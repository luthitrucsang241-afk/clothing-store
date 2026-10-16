<footer class="site-footer">
  <div class="container grid">
    <div class="c-5 t-8 m-4 footer-brand">
      <div class="logo logo-footer">NOIRVA</div>
      <p class="muted footer-about"><?= e(t('footer.about')) ?></p>
      <p class="social">
        <a href="<?= e(Setting::get('facebook_url', '#')) ?>" target="_blank" rel="noopener">Facebook</a>
        <a href="<?= e(Setting::get('instagram_url', '#')) ?>" target="_blank" rel="noopener">Instagram</a>
        <a href="<?= e(Setting::get('tiktok_url', '#')) ?>" target="_blank" rel="noopener">TikTok</a>
      </p>
    </div>
    <div class="c-2 t-2 m-2">
      <h4><?= e(t('footer.shop')) ?></h4>
      <ul>
        <li><a href="<?= e(url('/products')) ?>"><?= e(t('nav.products')) ?></a></li>
        <li><a href="<?= e(url('/categories')) ?>"><?= e(t('nav.categories')) ?></a></li>
        <li><a href="<?= e(url('/about')) ?>"><?= e(t('nav.about')) ?></a></li>
      </ul>
    </div>
    <div class="c-2 t-3 m-2">
      <h4><?= e(t('footer.support')) ?></h4>
      <ul>
        <li><a href="<?= e(url('/policies#shipping')) ?>"><?= e(t('footer.shipping')) ?></a></li>
        <li><a href="<?= e(url('/policies#returns')) ?>"><?= e(t('footer.returns')) ?></a></li>
        <li><a href="<?= e(url('/policies#privacy')) ?>"><?= e(t('footer.privacy')) ?></a></li>
        <li><a href="<?= e(url('/policies#terms')) ?>"><?= e(t('footer.terms')) ?></a></li>
        <li><a href="<?= e(url('/size-guide')) ?>"><?= e(t('footer.size_guide')) ?></a></li>
        <li><a href="<?= e(url('/faq')) ?>"><?= e(t('footer.faq')) ?></a></li>
      </ul>
    </div>
    <div class="c-3 t-3 m-4">
      <h4><?= e(t('footer.contact')) ?></h4>
      <ul>
        <li>Email: <a href="mailto:<?= e(Setting::get('site_email')) ?>"><?= e(Setting::get('site_email')) ?></a></li>
        <li>Hotline: <?= e(Setting::get('site_phone')) ?></li>
        <li><?= e(Setting::get('site_address')) ?></li>
      </ul>
    </div>
  </div>
  <div class="copyright"><div class="container">&copy; <?= date('Y') ?> NOIRVA. <?= e(t('footer.rights')) ?></div></div>
</footer>
