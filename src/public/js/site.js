// Interaksi situs — tanpa library. Semua efek gerak menghormati prefers-reduced-motion.
(() => {
  const doc = document.documentElement;
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const fine = matchMedia('(hover: hover) and (pointer: fine)').matches;

  // Header kaca + progress bar saat menggulir
  const hd = document.querySelector('.hd');
  const onScroll = () => {
    const y = scrollY;
    hd?.classList.toggle('scrolled', y > 24);
    const max = doc.scrollHeight - innerHeight;
    doc.style.setProperty('--p', max > 0 ? (y / max).toFixed(4) : 0);
  };
  addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  // Menu HP layar penuh
  const tg = document.querySelector('.hd-tg');
  if (tg) {
    const close = () => { doc.classList.remove('menu-open'); tg.setAttribute('aria-expanded', 'false'); };
    tg.addEventListener('click', () => {
      const open = doc.classList.toggle('menu-open');
      tg.setAttribute('aria-expanded', String(open));
    });
    document.querySelectorAll('.hd-nav a').forEach((a) => a.addEventListener('click', close));
    addEventListener('keydown', (e) => e.key === 'Escape' && close());
  }

  // Pill yang mengikuti kursor di menu desktop
  const nav = document.querySelector('.hd-nav');
  const pill = nav?.querySelector('.hd-pill');
  if (nav && pill && fine) {
    nav.querySelectorAll('a:not(.hd-mcta)').forEach((a) => a.addEventListener('mouseenter', () => {
      pill.style.width = a.offsetWidth + 'px';
      pill.style.transform = `translateX(${a.offsetLeft}px)`;
      nav.classList.add('pill-on');
    }));
    nav.addEventListener('mouseleave', () => nav.classList.remove('pill-on'));
  }

  // Muncul saat digulir
  const io = new IntersectionObserver((entries) => entries.forEach((e) => {
    if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
  }), { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });
  document.querySelectorAll('.rv').forEach((el) => io.observe(el));

  // Angka yang menghitung naik
  const countIo = new IntersectionObserver((entries) => entries.forEach((e) => {
    if (!e.isIntersecting) return;
    countIo.unobserve(e.target);
    const el = e.target, to = +el.dataset.count, dur = reduce ? 0 : 1400, t0 = performance.now();
    const tick = (t) => {
      const k = Math.min(1, (t - t0) / (dur || 1));
      el.textContent = Math.round(to * (1 - Math.pow(1 - k, 4)));
      if (k < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
  }), { threshold: 0.6 });
  document.querySelectorAll('[data-count]').forEach((el) => countIo.observe(el));

  // Sorotan kursor pada kartu
  if (fine) {
    document.addEventListener('pointermove', (e) => {
      const card = e.target.closest?.('.spot');
      if (!card) return;
      const r = card.getBoundingClientRect();
      card.style.setProperty('--mx', `${e.clientX - r.left}px`);
      card.style.setProperty('--my', `${e.clientY - r.top}px`);
    }, { passive: true });
  }

  // Tombol magnetik
  if (fine && !reduce) {
    document.querySelectorAll('[data-magnetic]').forEach((b) => {
      b.addEventListener('pointermove', (e) => {
        const r = b.getBoundingClientRect();
        b.style.transform = `translate(${(e.clientX - r.left - r.width / 2) * 0.18}px, ${(e.clientY - r.top - r.height / 2) * 0.28}px)`;
      });
      b.addEventListener('pointerleave', () => { b.style.transform = ''; });
    });
  }

  // Kartu 3D yang miring mengikuti kursor (hero)
  if (fine && !reduce) {
    document.querySelectorAll('[data-tilt]').forEach((el) => {
      const host = el.parentElement;
      host.addEventListener('pointermove', (e) => {
        const r = host.getBoundingClientRect();
        const x = (e.clientX - r.left) / r.width - 0.5, y = (e.clientY - r.top) / r.height - 0.5;
        el.style.transform = `perspective(1200px) rotateY(${x * 7}deg) rotateX(${-y * 7}deg)`;
      });
      host.addEventListener('pointerleave', () => { el.style.transform = ''; });
    });
  }

  // Tab segmen dengan indikator bergeser
  document.querySelectorAll('[role=tablist]').forEach((list) => {
    const tabs = [...list.querySelectorAll('[role=tab]')];
    const ind = list.querySelector('.seg-ind');
    const place = (tab) => {
      if (!ind) return;
      ind.style.width = tab.offsetWidth + 'px';
      ind.style.transform = `translateX(${tab.offsetLeft}px)`;
    };
    const select = (tab) => tabs.forEach((t) => {
      const active = t === tab;
      t.classList.toggle('on', active);
      t.setAttribute('aria-selected', String(active));
      t.tabIndex = active ? 0 : -1;
      const panel = document.getElementById(t.getAttribute('aria-controls'));
      panel.hidden = !active;
      if (active) { panel.classList.remove('swap'); void panel.offsetWidth; panel.classList.add('swap'); place(t); }
    });
    tabs.forEach((tab, i) => {
      tab.addEventListener('click', () => select(tab));
      tab.addEventListener('keydown', (e) => {
        const d = { ArrowRight: 1, ArrowLeft: -1 }[e.key];
        if (d) { const n = tabs[(i + d + tabs.length) % tabs.length]; n.focus(); select(n); }
      });
    });
    const first = tabs.find((t) => t.classList.contains('on')) || tabs[0];
    requestAnimationFrame(() => place(first));
    addEventListener('resize', () => place(tabs.find((t) => t.classList.contains('on'))));
  });

  // Cek Harga: hasil langsung tanpa memuat ulang halaman
  const form = document.querySelector('[data-live-search]');
  if (form) {
    const list = document.getElementById(form.dataset.liveSearch);
    const input = form.querySelector('input[name=q]');
    let timer, ctrl;
    const load = async (url) => {
      ctrl?.abort(); ctrl = new AbortController();
      list.classList.add('loading');
      try {
        const res = await fetch(url, { headers: { 'X-Partial': '1' }, signal: ctrl.signal });
        list.innerHTML = await res.text();
        list.querySelectorAll('.rv').forEach((el) => el.classList.add('in'));
        history.replaceState(null, '', url);
      } catch (e) { if (e.name !== 'AbortError') location.href = url; }
      list.classList.remove('loading');
    };
    const urlFor = () => {
      const p = new URLSearchParams(new FormData(form));
      [...p.keys()].forEach((k) => !p.get(k) && p.delete(k));
      return form.action + (p.toString() ? '?' + p : '');
    };
    input.addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(() => load(urlFor()), 220); });
    form.addEventListener('submit', (e) => { e.preventDefault(); load(urlFor()); });
    document.querySelectorAll('[data-cat]').forEach((a) => a.addEventListener('click', (e) => {
      e.preventDefault();
      form.querySelector('input[name=kategori]').value = a.dataset.cat;
      document.querySelectorAll('[data-cat]').forEach((x) => x.classList.toggle('on', x === a));
      const ind = a.parentElement.querySelector('.seg-ind');
      if (ind) { ind.style.width = a.offsetWidth + 'px'; ind.style.transform = `translateX(${a.offsetLeft}px)`; }
      load(urlFor());
    }));
    const on = document.querySelector('[data-cat].on');
    const ind = on?.parentElement.querySelector('.seg-ind');
    if (on && ind) requestAnimationFrame(() => { ind.style.width = on.offsetWidth + 'px'; ind.style.transform = `translateX(${on.offsetLeft}px)`; });
  }

  // Landing: bar CTA di HP muncul selama form booking tidak terlihat di layar
  const booking = document.getElementById('booking');
  if (booking && document.querySelector('.a-sticky')) {
    new IntersectionObserver(([e]) => {
      doc.classList.toggle('scrolled-far', !e.isIntersecting);
    }, { threshold: 0.15 }).observe(booking);
  }

  // Petunjuk tombol WhatsApp muncul sekali setelah beberapa detik
  const wa = document.querySelector('.wa-float');
  if (wa) setTimeout(() => { wa.classList.add('hint'); setTimeout(() => wa.classList.remove('hint'), 4500); }, 3500);
})();
