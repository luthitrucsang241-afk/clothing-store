(function () {
  // Menu hamburger
  var nav = document.getElementById('main-nav'), tg = document.querySelector('[data-nav-toggle]');
  if (nav && tg) tg.addEventListener('click', function () {
    var open = nav.classList.toggle('open'); tg.setAttribute('aria-expanded', open ? 'true' : 'false');
  });

  // Bộ lọc dạng bảng trượt trên mobile/tablet
  var ft = document.querySelector('[data-filter-toggle]'), fl = document.getElementById('filters');
  if (ft && fl) ft.addEventListener('click', function () { fl.classList.toggle('open'); });

  // Thông báo (Bootstrap alert) tự ẩn sau 6 giây, riêng lỗi thì giữ lại
  document.querySelectorAll('.toasts .alert').forEach(function (a) {
    if (a.classList.contains('alert-danger')) return;
    setTimeout(function () {
      if (!a.isConnected) return;
      if (window.bootstrap) { bootstrap.Alert.getOrCreateInstance(a).close(); } else { a.remove(); }
    }, 6000);
  });

  // Hộp thoại xác nhận (Bootstrap modal) cho mọi form có data-confirm="..."
  var cm = document.getElementById('confirmModal'), pending = null, modal = null;
  if (cm && window.bootstrap) { modal = bootstrap.Modal.getOrCreateInstance(cm); }
  document.addEventListener('submit', function (e) {
    var f = e.target, msg = f.getAttribute && f.getAttribute('data-confirm');
    if (!msg || f.dataset.confirmed === '1') return;
    e.preventDefault();
    if (!modal) { if (window.confirm(msg)) { f.dataset.confirmed = '1'; f.submit(); } return; }
    pending = f; document.getElementById('confirmBody').textContent = msg; modal.show();
  });
  var ok = document.getElementById('confirmOk');
  if (ok) ok.addEventListener('click', function () {
    if (!pending) return;
    var f = pending; pending = null; f.dataset.confirmed = '1'; modal.hide(); f.submit();
  });

  // Thư viện ảnh sản phẩm
  var main = document.querySelector('.js-main-img');
  document.querySelectorAll('.gallery-thumbs button').forEach(function (b) {
    b.addEventListener('click', function () { if (main) main.src = b.getAttribute('data-src'); });
  });

  // Chọn biến thể (màu + size) ở trang chi tiết sản phẩm
  var dataEl = document.getElementById('variant-data');
  if (dataEl) {
    var V = JSON.parse(dataEl.textContent), color = null, size = null;
    var priceEl = document.getElementById('price'), base = parseFloat(priceEl.dataset.base);
    var colorBtns = document.querySelectorAll('#color-chips [data-color]'), sizeBtns = document.querySelectorAll('#size-chips [data-size]');
    var vid = document.getElementById('variant_id'), info = document.getElementById('stock-info'), qty = document.getElementById('qty');
    var addBtn = document.getElementById('add-btn'), buyBtn = document.getElementById('buy-btn'), cl = document.getElementById('color-label');
    var fmt = function (n) { return new Intl.NumberFormat('vi-VN').format(Math.round(n)) + ' ₫'; };
    var find = function (c, s) { return V.find(function (v) { return v.color === c && v.size === s; }); };
    function refresh() {
      sizeBtns.forEach(function (b) {
        var v = color ? find(color, b.dataset.size) : V.find(function (x) { return x.size === b.dataset.size && x.stock > 0; });
        b.disabled = !v || v.stock <= 0; if (b.disabled && size === b.dataset.size) { size = null; }
        b.classList.toggle('active', size === b.dataset.size);
      });
      colorBtns.forEach(function (b) {
        var v = size ? find(b.dataset.color, size) : V.find(function (x) { return x.color === b.dataset.color && x.stock > 0; });
        b.disabled = !v || v.stock <= 0; if (b.disabled && color === b.dataset.color) { color = null; }
        b.classList.toggle('active', color === b.dataset.color);
      });
      cl.textContent = color || '—';
      var v = color && size ? find(color, size) : null, ok = !!(v && v.stock > 0);
      vid.value = ok ? v.id : ''; addBtn.disabled = buyBtn.disabled = !ok;
      if (ok) {
        priceEl.textContent = fmt(base + v.adj); qty.max = v.stock; if (+qty.value > v.stock) qty.value = v.stock;
        info.textContent = (document.documentElement.lang === 'en' ? 'In stock: ' : 'Còn lại: ') + v.stock;
      } else { priceEl.textContent = fmt(base); qty.max = 1; info.textContent = document.documentElement.lang === 'en' ? 'Select color and size' : 'Vui lòng chọn màu và size'; }
    }
    colorBtns.forEach(function (b) { b.addEventListener('click', function () { color = b.dataset.color; refresh(); }); });
    sizeBtns.forEach(function (b) { b.addEventListener('click', function () { size = b.dataset.size; refresh(); }); });
    document.querySelectorAll('[data-qty]').forEach(function (b) {
      b.addEventListener('click', function () {
        var n = (+qty.value || 1) + (+b.dataset.qty); n = Math.max(1, Math.min(n, +qty.max || 1)); qty.value = n;
      });
    });
    if (colorBtns.length === 1) { color = colorBtns[0].dataset.color; }
    if (sizeBtns.length === 1) { size = sizeBtns[0].dataset.size; }
    // Tự chọn sẵn biến thể còn hàng đầu tiên để nút Thêm vào giỏ / Mua ngay dùng được ngay
    if (!color || !size) {
      var first = V.find(function (x) { return x.stock > 0 && (!color || x.color === color) && (!size || x.size === size); }) || V.find(function (x) { return x.stock > 0; });
      if (first) { color = first.color; size = first.size; }
    }
    refresh();
    var bf = document.getElementById('buy-form');
    if (bf) bf.addEventListener('submit', function (e) {
      if (!vid.value) { e.preventDefault(); alert(document.documentElement.lang === 'en' ? 'Please select color and size' : 'Vui lòng chọn màu và size'); }
    });
  }
})();
