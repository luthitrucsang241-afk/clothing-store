<?php $vi = $modalVi ?? (lang() === 'vi'); ?>
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title" id="confirmTitle"><?= $vi ? 'Xác nhận' : 'Confirm' ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
    <div class="modal-body" id="confirmBody"></div>
    <div class="modal-footer">
      <button type="button" class="btn btn-ghost" data-bs-dismiss="modal"><?= $vi ? 'Huỷ' : 'Cancel' ?></button>
      <button type="button" class="btn btn-danger" id="confirmOk"><?= $vi ? 'Đồng ý' : 'OK' ?></button>
    </div>
  </div></div>
</div>
