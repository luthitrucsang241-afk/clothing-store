<form class="card" method="post" enctype="multipart/form-data" action="<?= e(url('/admin/' . $m['route'] . '/save' . ($row ? '/' . $row['id'] : ''))) ?>"><div class="card-body">
  <?= csrf_field() ?>
  <?php foreach ($m['fields'] as $name => $f): $type = $f['type'] ?? 'text'; $val = old($name, $row[$name] ?? ($type === 'select' ? array_key_first($f['options']) : '')); ?>
    <?php if ($type === 'products'): ?>
      <fieldset><legend><?= e($f['label']) ?></legend>
        <div class="check-grid"><?php foreach ($extra['products'] as $pr): ?><label class="check"><input type="checkbox" name="product_ids[]" value="<?= (int) $pr['id'] ?>" <?= in_array($pr['id'], $extra['selected']) ? 'checked' : '' ?>> <?= e($pr['name']) ?></label><?php endforeach; ?></div></fieldset>
    <?php elseif ($type === 'image'): ?>
      <label><?= e($f['label']) ?><input type="file" name="<?= e($name) ?>" accept="image/*"></label>
      <?php if (!empty($row[$name])): ?><span class="thumb-sm"><?= img($row[$name], '') ?></span><?php endif; ?>
    <?php elseif ($type === 'textarea'): ?>
      <label><?= e($f['label']) ?><textarea class="form-control" name="<?= e($name) ?>" rows="3"><?= e($val) ?></textarea></label>
    <?php elseif ($type === 'select'): ?>
      <label><?= e($f['label']) ?><select class="form-select" name="<?= e($name) ?>"><?php foreach ($f['options'] as $k => $lab): ?><option value="<?= e($k) ?>" <?= (string) $val === (string) $k ? 'selected' : '' ?>><?= e($lab) ?></option><?php endforeach; ?></select></label>
    <?php elseif ($type === 'datetime'): ?>
      <label><?= e($f['label']) ?><input class="form-control" type="datetime-local" name="<?= e($name) ?>" value="<?= e($val ? str_replace(' ', 'T', substr((string) $val, 0, 16)) : '') ?>"></label>
    <?php else: ?>
      <label><?= e($f['label']) ?><?= !empty($f['required']) ? ' *' : '' ?><input class="form-control" type="<?= $type === 'number' ? 'number' : 'text' ?>" <?= $type === 'number' ? 'step="any" min="0"' : '' ?> name="<?= e($name) ?>" value="<?= e($val) ?>"></label>
    <?php endif; ?>
  <?php endforeach; ?>
  <button class="btn" type="submit">Lưu</button> <a class="btn btn-ghost" href="<?= e(url('/admin/' . $m['route'])) ?>">Quay lại</a>
</div></form>
