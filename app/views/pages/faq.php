<?php $faq = lang() === 'en' ? [
  ['How long does delivery take?', 'Usually 2–5 business days depending on your location.'],
  ['Which payment methods are supported?', 'Currently Cash on Delivery (COD). Online payment is coming soon.'],
  ['Can I cancel my order?', 'Yes, while it is still Pending or Confirmed, from the Orders page.'],
  ['How do I choose my size?', 'See the Size guide page and compare with a garment you own.'],
] : [
  ['Giao hàng mất bao lâu?', 'Thông thường 2–5 ngày làm việc tuỳ khu vực.'],
  ['Hỗ trợ những phương thức thanh toán nào?', 'Hiện tại là thanh toán khi nhận hàng (COD). Thanh toán online sẽ sớm có.'],
  ['Tôi có thể huỷ đơn không?', 'Có, khi đơn còn ở trạng thái Chờ xác nhận hoặc Đã xác nhận, tại trang Đơn hàng.'],
  ['Làm sao chọn đúng size?', 'Xem trang Hướng dẫn chọn size và so với một món đồ bạn đang mặc vừa.'],
]; ?>
<div class="container section prose"><h1><?= e(t('footer.faq')) ?></h1>
  <?php foreach ($faq as [$q, $a]): ?><details class="card"><summary><strong><?= e($q) ?></strong></summary><p><?= e($a) ?></p></details><?php endforeach; ?>
</div>
