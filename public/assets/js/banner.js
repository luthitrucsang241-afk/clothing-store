// Slider banner trang chủ: tự chạy, nút trái/phải, chấm chỉ báo, vuốt trên điện thoại
(function () {
  var hero = document.querySelector('[data-slider]');
  if (!hero) return;
  var slides = hero.querySelectorAll('.slide'), dots = hero.querySelectorAll('.dots button');
  if (slides.length < 2) return;
  var i = 0, timer = null, reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  function show(n) {
    i = (n + slides.length) % slides.length;
    slides.forEach(function (s, k) { s.classList.toggle('active', k === i); });
    dots.forEach(function (d, k) { d.classList.toggle('active', k === i); });
  }
  function start() { if (!reduce) { stop(); timer = setInterval(function () { show(i + 1); }, 5000); } }
  function stop() { if (timer) clearInterval(timer); }
  hero.querySelector('.prev').addEventListener('click', function () { show(i - 1); start(); });
  hero.querySelector('.next').addEventListener('click', function () { show(i + 1); start(); });
  dots.forEach(function (d, k) { d.addEventListener('click', function () { show(k); start(); }); });
  hero.addEventListener('mouseenter', stop); hero.addEventListener('mouseleave', start);
  var x0 = null;
  hero.addEventListener('touchstart', function (e) { x0 = e.touches[0].clientX; }, { passive: true });
  hero.addEventListener('touchend', function (e) {
    if (x0 === null) return;
    var dx = e.changedTouches[0].clientX - x0;
    if (Math.abs(dx) > 40) { show(i + (dx < 0 ? 1 : -1)); start(); }
    x0 = null;
  });
  start();
})();
