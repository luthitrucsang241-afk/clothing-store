<div class="container section">
  <div class="section-head"><h1><?= e(t('nav.contact')) ?></h1></div>
  <div class="grid">
    <div class="c-7 t-8 m-4"><div class="card"><div class="card-body">
      <form method="post" action="<?= e(url('/contact')) ?>" novalidate><?= csrf_field() ?>
        <div class="row2">
          <label><?= e(t('auth.full_name')) ?><input class="form-control" type="text" name="full_name" value="<?= e(old('full_name', user()['name'] ?? '')) ?>" required><?= field_error('full_name') ?></label>
          <label>Email<input class="form-control" type="email" name="email" value="<?= e(old('email', user()['email'] ?? '')) ?>" required><?= field_error('email') ?></label>
        </div>
        <label><?= e(t('contact.subject')) ?><input class="form-control" type="text" name="subject" value="<?= e(old('subject')) ?>" required maxlength="200"><?= field_error('subject') ?></label>
        <label><?= e(t('contact.message')) ?><textarea class="form-control" name="message" rows="5" required maxlength="3000"><?= e(old('message')) ?></textarea><?= field_error('message') ?></label>
        <button class="btn" type="submit"><?= e(t('contact.send')) ?></button>
      </form>
    </div></div></div>
    <aside class="c-5 t-8 m-4"><div class="card"><div class="card-body">
      <h3><?= e(t('footer.contact')) ?></h3>
      <p>Email: <a href="mailto:<?= e(Setting::get('site_email')) ?>"><?= e(Setting::get('site_email')) ?></a></p>
      <p>Hotline: <?= e(Setting::get('site_phone')) ?></p>
      <p><?= e(Setting::get('site_address')) ?></p>
      <p><a class="btn btn-ghost" target="_blank" rel="noopener" href="https://www.google.com/maps/dir/?api=1&destination=<?= e(rawurlencode(Setting::get('map_query', 'Đại học Văn Hiến, 613 Âu Cơ, Tân Phú, Hồ Chí Minh'))) ?>"><?= e(t('contact.directions')) ?></a></p>
    </div></div></aside>
  </div>

  <?php $mapQ = Setting::get('map_query', 'Đại học Văn Hiến, 613 Âu Cơ, Tân Phú, Hồ Chí Minh'); ?>
  <div class="card map-card"><iframe
      title="<?= e(t('contact.map_title')) ?>"
      src="https://www.google.com/maps?q=<?= e(rawurlencode($mapQ)) ?>&hl=<?= e(lang()) ?>&z=17&output=embed"
      width="100%" height="380" style="border:0" allowfullscreen loading="lazy"
      referrerpolicy="no-referrer-when-downgrade"></iframe></div>
</div>
