(() => {
  const $ = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => [...r.querySelectorAll(s)];
  const root = document.documentElement;
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const store = (k, v) => { try { v === undefined ? localStorage.removeItem(k) : localStorage.setItem(k, v); } catch (e) {} };

  /* ---------- Sidebar ---------- */
  const side = $('#side'), scrim = $('#scrim');
  const openSide = on => { side.classList.toggle('open', on); scrim.classList.toggle('on', on); };
  $('#burger')?.addEventListener('click', () => openSide(true));
  $$('[data-open-side]').forEach(b => b.addEventListener('click', () => openSide(true)));
  scrim?.addEventListener('click', () => openSide(false));
  $('#collapse')?.addEventListener('click', () => {
    const mini = root.classList.toggle('side-mini');
    store('adm-mini', mini ? '1' : undefined);
  });

  /* ---------- Tema ---------- */
  $('#theme')?.addEventListener('click', () => {
    const dark = root.dataset.theme ? root.dataset.theme === 'dark' : matchMedia('(prefers-color-scheme: dark)').matches;
    const next = dark ? 'light' : 'dark';
    const apply = () => { root.dataset.theme = next; store('adm-theme', next); };
    document.startViewTransition && !reduce ? document.startViewTransition(apply) : apply();
  });

  /* ---------- Bayangan topbar saat menggulir ---------- */
  const onScroll = () => document.body.classList.toggle('scrolled', scrollY > 4);
  addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  /* ---------- Animasi masuk berurutan ---------- */
  $$('#content > *').forEach((el, i) => el.style.setProperty('--i', Math.min(i, 8)));
  $$('tbody').forEach(tb => $$('tr', tb).forEach((tr, i) => tr.style.setProperty('--r', Math.min(i, 14))));
  $$('.grid-cards > *').forEach((el, i) => el.style.setProperty('--j', i));

  /* ---------- Angka berhitung naik ---------- */
  const countUp = el => {
    const to = parseInt(el.dataset.count, 10);
    if (!to || reduce) return;
    const t0 = performance.now(), dur = 900;
    const tick = now => {
      const p = Math.min(1, (now - t0) / dur), e = 1 - Math.pow(1 - p, 4);
      el.textContent = Math.round(to * e).toLocaleString('id-ID');
      if (p < 1) requestAnimationFrame(tick);
    };
    el.textContent = '0';
    requestAnimationFrame(tick);
  };
  $$('[data-count]').forEach(countUp);

  /* ---------- Baris tabel bisa diklik ---------- */
  $$('tbody tr').forEach(tr => {
    const link = $('a.t', tr);
    if (!link) return;
    tr.classList.add('clickable');
    tr.addEventListener('click', e => {
      if (e.target.closest('a, button, form, input, select, textarea, label')) return;
      if (getSelection().toString()) return;
      e.ctrlKey || e.metaKey ? open(link.href, '_blank') : link.click();
    });
  });

  /* ---------- Efek tombol ditekan ---------- */
  document.addEventListener('pointerdown', e => {
    const b = e.target.closest('.btn');
    if (!b || reduce) return;
    const r = b.getBoundingClientRect();
    b.style.setProperty('--x', (e.clientX - r.left) + 'px');
    b.style.setProperty('--y', (e.clientY - r.top) + 'px');
    b.classList.add('ripple');
    setTimeout(() => b.classList.remove('ripple'), 30);
  });

  /* ---------- Toast ---------- */
  const toasts = $('#toasts');
  const dismiss = t => { t.classList.add('out'); setTimeout(() => t.remove(), 350); };
  const arm = t => {
    $('button', t)?.addEventListener('click', () => dismiss(t));
    let timer = setTimeout(() => dismiss(t), 4500);
    t.addEventListener('mouseenter', () => { clearTimeout(timer); t.style.setProperty('--paused', 1); });
    t.addEventListener('mouseleave', () => { timer = setTimeout(() => dismiss(t), 2000); });
  };
  $$('.toast', toasts).forEach(arm);
  window.toast = msg => {
    const t = document.createElement('div');
    t.className = 'toast';
    t.setAttribute('role', 'status');
    t.innerHTML = '<span class="ok">✓</span><span></span><button type="button" aria-label="Tutup">×</button>';
    t.children[1].textContent = msg;
    toasts.append(t);
    arm(t);
  };

  /* ---------- Dialog konfirmasi ---------- */
  const dlg = $('#confirm');
  let pending = null;
  document.addEventListener('submit', e => {
    const f = e.target;
    if (f.dataset.confirm && !f.dataset.ok) {
      e.preventDefault();
      pending = f;
      $('[data-msg]', dlg).textContent = f.dataset.confirm;
      dlg.showModal();
      $('[data-no]', dlg).focus();
      return;
    }
    // tanda memuat pada tombol simpan
    const btn = e.submitter || $('button[type=submit]', f);
    if (btn && !e.defaultPrevented) setTimeout(() => btn.classList.add('is-loading'), 0);
    if (f === dirtyForm) leaving = true;
  });
  $('[data-no]', dlg)?.addEventListener('click', () => dlg.close());
  $('[data-yes]', dlg)?.addEventListener('click', () => {
    dlg.close();
    if (pending) { pending.dataset.ok = '1'; pending.requestSubmit(); }
  });
  dlg?.addEventListener('click', e => { if (e.target === dlg) dlg.close(); });

  /* ---------- Bilah simpan & peringatan perubahan ---------- */
  const savebar = $('#savebar');
  let dirtyForm = null, leaving = false;
  const tracked = $$('#content form[method=post]').filter(f => !f.dataset.confirm && !f.classList.contains('filters') && $('button[type=submit]', f) && $$('input:not([type=hidden]), textarea, select', f).length > 1);
  const snapshot = f => new URLSearchParams(new FormData(f)).toString();
  tracked.forEach(f => {
    f._initial = snapshot(f);
    const check = () => {
      const changed = snapshot(f) !== f._initial;
      if (changed) dirtyForm = f; else if (dirtyForm === f) dirtyForm = null;
      savebar.classList.toggle('on', !!dirtyForm);
    };
    f.addEventListener('input', check);
    f.addEventListener('change', check);
    f.addEventListener('ev-change', check);
  });
  $('[data-save]', savebar)?.addEventListener('click', () => dirtyForm?.requestSubmit());
  $('[data-discard]', savebar)?.addEventListener('click', () => { leaving = true; location.reload(); });
  addEventListener('beforeunload', e => { if (dirtyForm && !leaving) { e.preventDefault(); e.returnValue = ''; } });

  /* ---------- Pintasan keyboard ---------- */
  document.addEventListener('keydown', e => {
    const mod = e.ctrlKey || e.metaKey;
    if (mod && e.key.toLowerCase() === 's') {
      const f = dirtyForm || tracked[0];
      if (f) { e.preventDefault(); f.requestSubmit(); }
    }
    if (mod && e.key.toLowerCase() === 'k') { e.preventDefault(); openPalette(); }
    if (e.key === '/' && !e.target.closest('input, textarea, select, [contenteditable]')) { e.preventDefault(); openPalette(); }
    if (e.key === 'Escape' && side.classList.contains('open')) openSide(false);
  });

  /* ---------- Palet perintah (Ctrl+K) ---------- */
  const pal = $('#palette');
  const data = JSON.parse($('#commands')?.textContent || '{"items":[],"search":[]}');
  const iconTpl = $('#icons')?.content;
  const iconOf = name => iconTpl?.querySelector(`[data-ic="${name}"]`)?.innerHTML || '';
  const input = $('input', pal), list = $('ul', pal);
  let results = [], sel = 0;
  const esc = s => s.replace(/[&<>"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));
  const score = (label, q) => {
    const l = label.toLowerCase();
    if (!q) return 1;
    if (l.startsWith(q)) return 3;
    if (l.includes(q)) return 2;
    let i = 0;
    for (const ch of l) if (ch === q[i]) i++;
    return i === q.length ? 1 : 0;
  };
  const highlight = (label, q) => {
    if (!q) return esc(label);
    const i = label.toLowerCase().indexOf(q);
    return i < 0 ? esc(label) : esc(label.slice(0, i)) + '<mark>' + esc(label.slice(i, i + q.length)) + '</mark>' + esc(label.slice(i + q.length));
  };
  const render = () => {
    const q = input.value.trim().toLowerCase();
    results = data.items.map(c => ({ ...c, s: score(c.label, q) })).filter(c => c.s).sort((a, b) => b.s - a.s);
    if (q) results.push(...data.search.map(s => ({ label: `${s.label}: “${input.value.trim()}”`, url: s.url + '?q=' + encodeURIComponent(input.value.trim()), group: 'Cari', icon: 'search' })));
    sel = 0;
    let html = '', last = '';
    results.forEach((c, i) => {
      if (c.group !== last) { html += `<li class="grp">${esc(c.group)}</li>`; last = c.group; }
      html += `<li role="option"><a href="${esc(c.url)}" data-i="${i}" ${c.blank ? 'target="_blank" rel="noopener"' : ''}><span class="pi">${iconOf(c.icon)}</span><span>${highlight(c.label, q)}</span><span class="go">↵</span></a></li>`;
    });
    list.innerHTML = html || '<li class="none">Tidak ada hasil.</li>';
    mark();
  };
  const mark = () => $$('a', list).forEach(a => {
    const on = +a.dataset.i === sel;
    a.classList.toggle('sel', on);
    if (on) a.scrollIntoView({ block: 'nearest' });
  });
  function openPalette() {
    if (!pal || pal.open) return;
    input.value = '';
    render();
    pal.showModal();
    input.focus();
  }
  $('#open-palette')?.addEventListener('click', openPalette);
  input?.addEventListener('input', render);
  input?.addEventListener('keydown', e => {
    if (e.key === 'ArrowDown') { e.preventDefault(); sel = Math.min(sel + 1, results.length - 1); mark(); }
    if (e.key === 'ArrowUp') { e.preventDefault(); sel = Math.max(sel - 1, 0); mark(); }
    if (e.key === 'Enter') { e.preventDefault(); $(`a[data-i="${sel}"]`, list)?.click(); pal.close(); }
  });
  list?.addEventListener('mousemove', e => { const a = e.target.closest('a'); if (a && +a.dataset.i !== sel) { sel = +a.dataset.i; mark(); } });
  pal?.addEventListener('click', e => { if (e.target === pal) pal.close(); });

  /* ---------- Penghitung karakter ---------- */
  $$('.field input[maxlength], .field textarea[maxlength]').forEach(el => {
    const max = +el.getAttribute('maxlength');
    const c = document.createElement('span');
    c.className = 'count';
    el.closest('.field').append(c);
    const upd = () => {
      const n = el.value.length;
      c.textContent = `${n}/${max}`;
      c.classList.toggle('warn', n > max * .85 && n < max);
      c.classList.toggle('over', n >= max);
    };
    el.addEventListener('input', upd);
    upd();
  });

  /* ---------- Pratinjau harga: Rp150rb–450rb ---------- */
  const short = n => n >= 1e6 ? (n / 1e6).toFixed(1).replace('.', ',').replace(/,0$/, '') + 'jt' : Math.floor(n / 1000) + 'rb';
  const pmin = $('#f-price_min'), pmax = $('#f-price_max'), psuf = $('#f-price_suffix'), chip = $('#price-chip');
  if (pmin && chip) {
    const draw = () => {
      const a = parseInt(pmin.value, 10), b = parseInt(pmax.value, 10);
      let t = '—';
      if (!isNaN(a)) {
        t = 'Rp' + short(a) + (b ? '–' + short(b) : '');
        if (psuf.value.trim()) t += ' ' + psuf.value.trim();
      }
      if (chip.textContent !== t) {
        chip.textContent = t;
        chip.classList.add('bump');
        setTimeout(() => chip.classList.remove('bump'), 160);
      }
    };
    [pmin, pmax, psuf].forEach(i => i.addEventListener('input', draw));
    draw();
  }

  /* ---------- Editor artikel ---------- */
  const body = $('#f-body');
  if (body) {
    const wrap = (open, close = '') => {
      const { selectionStart: s, selectionEnd: e, value: v } = body;
      const sel = v.slice(s, e) || 'teks';
      body.focus();
      body.setRangeText(open + sel + close, s, e, 'select');
      body.dispatchEvent(new Event('input', { bubbles: true }));
    };
    const acts = {
      h2: () => wrap('<h2>', '</h2>\n'),
      h3: () => wrap('<h3>', '</h3>\n'),
      p: () => wrap('<p>', '</p>\n'),
      b: () => wrap('<strong>', '</strong>'),
      i: () => wrap('<em>', '</em>'),
      ul: () => wrap('<ul>\n<li>', '</li>\n<li>butir lain</li>\n</ul>\n'),
      ol: () => wrap('<ol>\n<li>', '</li>\n<li>butir lain</li>\n</ol>\n'),
      a: () => { const u = prompt('Alamat tautan (https://…)'); if (u) wrap('<a href="' + u.replace(/"/g, '') + '">', '</a>'); },
    };
    $$('.tools [data-fmt]').forEach(b => b.addEventListener('click', () => acts[b.dataset.fmt]?.()));
    body.addEventListener('keydown', e => {
      if (!(e.ctrlKey || e.metaKey)) return;
      const k = e.key.toLowerCase();
      if (k === 'b') { e.preventDefault(); acts.b(); }
      if (k === 'i') { e.preventDefault(); acts.i(); }
    });

    const prev = $('#preview'), toggle = $('#toggle-preview');
    toggle?.addEventListener('click', () => {
      const showing = !prev.hidden;
      prev.hidden = showing;
      body.hidden = !showing;
      toggle.textContent = showing ? 'Pratinjau' : 'Kembali mengedit';
      if (!showing) prev.innerHTML = body.value;
    });

    // Judul → alamat otomatis selama alamat belum diisi manual
    const title = $('#f-title'), slug = $('#f-slug');
    if (title && slug && !slug.value) {
      title.addEventListener('input', () => {
        if (slug.dataset.touched) return;
        slug.value = title.value.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '').replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
      });
      slug.addEventListener('input', () => { slug.dataset.touched = '1'; });
    }

    const mode = $('#f-publish'), when = $('#when');
    const sync = () => { if (when && mode) when.hidden = mode.value !== 'scheduled'; };
    mode?.addEventListener('change', sync);
    sync();
  }

  /* ---------- Tahapan tiket: tambah, hapus, urutkan, seret ---------- */
  const list2 = $('#events');
  if (list2) {
    const form = list2.closest('form');
    const changed = () => form.dispatchEvent(new Event('ev-change'));
    const tpl = $('#ev-tpl');
    const renumber = () => $$('.ev', list2).forEach((row, i) => {
      $('.n', row).textContent = i + 1;
      $$('[name]', row).forEach(f => { f.name = f.name.replace(/events\[[^\]]*\]/, 'events[' + i + ']'); });
      row.dataset.state = $('select', row).value;
    });
    // animasi FLIP saat baris berpindah
    const flip = fn => {
      const rows = $$('.ev', list2), first = new Map(rows.map(r => [r, r.getBoundingClientRect().top]));
      fn();
      if (reduce) return;
      rows.forEach(r => {
        const d = first.get(r) - r.getBoundingClientRect().top;
        if (d) r.animate([{ transform: `translateY(${d}px)` }, { transform: 'none' }], { duration: 320, easing: 'cubic-bezier(.22,1,.36,1)' });
      });
    };
    $('#add-ev')?.addEventListener('click', () => {
      list2.append(tpl.content.cloneNode(true));
      renumber();
      $$('.ev', list2).at(-1).querySelector('input')?.focus();
      changed();
    });
    list2.addEventListener('click', e => {
      const row = e.target.closest('.ev');
      if (!row) return;
      if (e.target.closest('[data-del]')) {
        row.classList.add('removing');
        setTimeout(() => { flip(() => row.remove()); renumber(); changed(); }, reduce ? 0 : 220);
        return;
      }
      if (e.target.closest('[data-up]') && row.previousElementSibling) flip(() => row.previousElementSibling.before(row));
      else if (e.target.closest('[data-down]') && row.nextElementSibling) flip(() => row.nextElementSibling.after(row));
      else return;
      renumber();
      changed();
    });
    list2.addEventListener('change', e => { if (e.target.matches('select')) e.target.closest('.ev').dataset.state = e.target.value; });

    // seret nomor tahap untuk mengurutkan
    let drag = null;
    list2.addEventListener('pointerdown', e => { const n = e.target.closest('.n'); if (n) n.closest('.ev').draggable = true; });
    list2.addEventListener('dragstart', e => { drag = e.target.closest('.ev'); drag?.classList.add('dragging'); e.dataTransfer.effectAllowed = 'move'; });
    list2.addEventListener('dragover', e => {
      if (!drag) return;
      e.preventDefault();
      const over = e.target.closest('.ev');
      $$('.ev.over', list2).forEach(r => r.classList.remove('over'));
      if (over && over !== drag) over.classList.add('over');
    });
    list2.addEventListener('drop', e => {
      e.preventDefault();
      const over = e.target.closest('.ev');
      if (drag && over && over !== drag) {
        const after = [...list2.children].indexOf(drag) < [...list2.children].indexOf(over);
        flip(() => after ? over.after(drag) : over.before(drag));
        renumber();
        changed();
      }
    });
    list2.addEventListener('dragend', () => {
      $$('.ev', list2).forEach(r => { r.classList.remove('dragging', 'over'); r.draggable = false; });
      drag = null;
    });
    renumber();
  }
})();
