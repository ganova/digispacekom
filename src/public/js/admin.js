(() => {
  const $ = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => [...r.querySelectorAll(s)];

  // Sidebar di layar kecil
  const side = $('#side');
  $('#burger')?.addEventListener('click', () => side.classList.toggle('open'));
  document.addEventListener('click', e => {
    if (side?.classList.contains('open') && !side.contains(e.target) && !e.target.closest('#burger')) side.classList.remove('open');
  });

  // Konfirmasi sebelum menghapus / mengembalikan
  document.addEventListener('submit', e => {
    const msg = e.target.dataset.confirm;
    if (msg && !confirm(msg)) e.preventDefault();
  });

  // Pesan sukses hilang sendiri
  const ok = $('.flash:not(.err)');
  if (ok) setTimeout(() => { ok.style.transition = 'opacity .4s'; ok.style.opacity = '0'; setTimeout(() => ok.remove(), 400); }, 4500);

  // Pratinjau harga: Rp150rb–450rb
  const short = n => n >= 1e6 ? (n / 1e6).toFixed(1).replace('.', ',').replace(/,0$/, '') + 'jt' : Math.floor(n / 1000) + 'rb';
  const pmin = $('#f-price_min'), pmax = $('#f-price_max'), psuf = $('#f-price_suffix'), chip = $('#price-chip');
  if (pmin && chip) {
    const draw = () => {
      const a = parseInt(pmin.value, 10), b = parseInt(pmax.value, 10);
      if (isNaN(a)) { chip.textContent = '—'; return; }
      let t = 'Rp' + short(a) + (b ? '–' + short(b) : '');
      if (psuf.value.trim()) t += ' ' + psuf.value.trim();
      chip.textContent = t;
    };
    [pmin, pmax, psuf].forEach(i => i.addEventListener('input', draw));
    draw();
  }

  // Editor artikel: tombol format + pratinjau
  const body = $('#f-body');
  if (body) {
    const wrap = (open, close = '') => {
      const { selectionStart: s, selectionEnd: e, value: v } = body;
      const sel = v.slice(s, e) || 'teks';
      body.setRangeText(open + sel + close, s, e, 'select');
      body.focus();
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

  // Tahapan tiket: tambah, hapus, urutkan
  const list = $('#events');
  if (list) {
    const tpl = $('#ev-tpl');
    const renumber = () => $$('.ev', list).forEach((row, i) => {
      $('.n', row).textContent = i + 1;
      $$('[name]', row).forEach(f => { f.name = f.name.replace(/events\[[^\]]*\]/, 'events[' + i + ']'); });
      row.dataset.state = $('select', row).value;
    });
    $('#add-ev')?.addEventListener('click', () => {
      list.append(tpl.content.cloneNode(true));
      renumber();
      $$('.ev', list).at(-1).querySelector('input')?.focus();
    });
    list.addEventListener('click', e => {
      const row = e.target.closest('.ev');
      if (!row) return;
      if (e.target.closest('[data-del]')) row.remove();
      else if (e.target.closest('[data-up]') && row.previousElementSibling) row.previousElementSibling.before(row);
      else if (e.target.closest('[data-down]') && row.nextElementSibling) row.nextElementSibling.after(row);
      else return;
      renumber();
    });
    list.addEventListener('change', e => { if (e.target.matches('select')) e.target.closest('.ev').dataset.state = e.target.value; });
    renumber();
  }
})();
