(function () {
  function ready(fn){ document.readyState !== 'loading' ? fn() : document.addEventListener('DOMContentLoaded', fn); }

  ready(function () {
    // Menú
    const btn = document.querySelector('.cfa-menu-btn');
    const menu = document.getElementById('cfa-menu');

    if (btn && menu) {
      btn.addEventListener('click', function () {
        const open = menu.classList.toggle('is-open');
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      });
    }

    // Idiomes (placeholder)
    const langBtn = document.querySelector('.cfa-lang__btn');
    const langMenu = document.getElementById('cfa-lang-menu');

    if (langBtn && langMenu) {
      langBtn.addEventListener('click', function () {
        const hidden = langMenu.hasAttribute('hidden');
        if (hidden) langMenu.removeAttribute('hidden');
        else langMenu.setAttribute('hidden', '');
        langBtn.setAttribute('aria-expanded', hidden ? 'true' : 'false');
      });

      document.addEventListener('click', function (e) {
        if (!e.target.closest('.cfa-lang')) {
          langMenu.setAttribute('hidden', '');
          langBtn.setAttribute('aria-expanded', 'false');
        }
      });
    }
  });
})();
