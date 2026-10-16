<div class="card"><div class="card-body"><h3>200 dòng nhật ký lỗi gần nhất</h3>
  <?php if (!$lines): ?><p class="muted">Chưa có lỗi nào được ghi.</p><?php else: ?><pre class="log"><?= e(implode("\n", $lines)) ?></pre><?php endif; ?>
</div></div>
