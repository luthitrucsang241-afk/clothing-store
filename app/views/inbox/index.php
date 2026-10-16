<div class="container section">
  <div class="section-head"><h1><?= e(t('nav.inbox')) ?></h1></div>
  <p class="muted"><?= e(t('inbox.intro')) ?></p>
  <?php if (!$rows): ?>
    <div class="empty card"><div class="card-body"><?= e(t('inbox.empty')) ?></div></div>
  <?php else: ?>
    <div class="list-group inbox-list">
      <?php foreach ($rows as $r): ?>
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?= $r['is_read'] ? '' : 'inbox-unread' ?>" href="<?= e(url('/inbox/view?id=' . $r['id'])) ?>">
          <span>
            <?php if (!$r['is_read']): ?><span class="badge text-bg-dark me-2"><?= e(t('inbox.new')) ?></span><?php endif; ?>
            <strong><?= e($r['subject']) ?></strong><br>
            <small class="muted"><?= e($r['sent_by'] ? t('inbox.from_admin') : t('inbox.from_system')) ?></small>
          </span>
          <small class="muted"><?= e(date('d/m/Y H:i', strtotime($r['created_at']))) ?></small>
        </a>
      <?php endforeach; ?>
    </div>
    <?= pager($total, $page, $per) ?>
  <?php endif; ?>
</div>
