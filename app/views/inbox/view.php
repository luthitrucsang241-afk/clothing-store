<div class="container section">
  <p><a href="<?= e(url('/inbox')) ?>">&larr; <?= e(t('nav.inbox')) ?></a></p>
  <div class="card"><div class="card-body">
    <h1 class="h3"><?= e($m['subject']) ?></h1>
    <p class="muted"><?= e($m['sent_by'] ? t('inbox.from_admin') : t('inbox.from_system')) ?> · <?= e(date('d/m/Y H:i', strtotime($m['created_at']))) ?></p>
    <hr>
    <div class="inbox-body"><?= nl2br(e($m['body'])) ?></div>
    <form method="post" action="<?= e(url('/inbox/delete')) ?>" class="mt-3" data-confirm="<?= e(t('inbox.confirm_delete')) ?>">
      <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
      <button class="btn btn-sm btn-ghost" type="submit"><?= e(t('inbox.delete')) ?></button>
    </form>
  </div></div>
</div>
