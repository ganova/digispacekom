// Menu HP dan tab segmen layanan di beranda — tanpa library.
(() => {
  const toggle = document.querySelector('.nav-tg');
  if (toggle) {
    const nav = document.getElementById(toggle.getAttribute('aria-controls'));
    toggle.addEventListener('click', () => {
      const open = nav.classList.toggle('open');
      toggle.setAttribute('aria-expanded', String(open));
    });
  }

  document.querySelectorAll('[role=tablist]').forEach((list) => {
    const tabs = [...list.querySelectorAll('[role=tab]')];
    tabs.forEach((tab) => tab.addEventListener('click', () => {
      tabs.forEach((t) => {
        const active = t === tab;
        t.classList.toggle('on', active);
        t.setAttribute('aria-selected', String(active));
        document.getElementById(t.getAttribute('aria-controls')).hidden = !active;
      });
    }));
  });
})();
