// Light / Dark mode – lưu lựa chọn trong localStorage
(function () {
  var root = document.documentElement;
  document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
      root.setAttribute('data-theme', next); root.setAttribute('data-bs-theme', next);
      try { localStorage.setItem('noirva-theme', next); } catch (e) {}
    });
  });
})();
