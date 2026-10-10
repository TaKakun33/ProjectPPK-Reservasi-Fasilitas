{{--
    Sistem popup global (pengganti alert()/confirm() bawaan browser dan kotak notifikasi di halaman).
    Mandiri: gaya + skrip ada di sini, tidak butuh build Vite.

    Dipasang sekali di setiap layout. Fungsinya:
      1. Menampilkan flash session otomatis: success, error, warning, info, status
         (dan, bila validasi=true, ringkasan kesalahan validasi formulir).
      2. Konfirmasi untuk form:
            <form ... data-confirm="Pesan..." data-confirm-type="danger|warning|success|info"
                      data-confirm-title="Judul" data-confirm-ok="Teks tombol">
      3. API JavaScript:
            Popup.alert({ type, title, message, items })      -> Promise
            Popup.confirm({ type, title, message, okText })   -> Promise<boolean>
--}}
@props(['validasi' => false])

@php
    $antrian = [];

    // Pesan status bawaan Laravel Breeze (kode singkat) dijadikan kalimat Indonesia.
    $pesanStatus = [
        'profile-updated'        => 'Profil Anda berhasil diperbarui.',
        'password-updated'       => 'Kata sandi Anda berhasil diperbarui.',
        'verification-link-sent' => 'Tautan verifikasi baru telah dikirim ke alamat email Anda.',
    ];

    if (session('success')) {
        $antrian[] = ['type' => 'success', 'title' => 'Berhasil', 'message' => session('success')];
    }
    if (session('status')) {
        $antrian[] = ['type' => 'success', 'title' => 'Berhasil', 'message' => $pesanStatus[session('status')] ?? session('status')];
    }
    if (session('info')) {
        $antrian[] = ['type' => 'info', 'title' => 'Informasi', 'message' => session('info')];
    }
    if (session('warning')) {
        $antrian[] = ['type' => 'warning', 'title' => 'Perhatian', 'message' => session('warning')];
    }
    if (session('error')) {
        $antrian[] = ['type' => 'error', 'title' => 'Terjadi Kesalahan', 'message' => session('error')];
    }
    if ($validasi && $errors->any()) {
        $antrian[] = [
            'type'    => 'error',
            'title'   => 'Data Belum Valid',
            'message' => 'Mohon periksa kembali isian berikut:',
            'items'   => array_values(array_unique($errors->all())),
        ];
    }
@endphp

<style>
    .pp-overlay{position:fixed;inset:0;z-index:100;display:flex;align-items:center;justify-content:center;padding:1rem;background:rgba(37,43,43,.6);-webkit-backdrop-filter:blur(2px);backdrop-filter:blur(2px);opacity:0;transition:opacity 150ms ease-in;font-family:'Plus Jakarta Sans',system-ui,sans-serif}
    .pp-overlay.pp-show{opacity:1;transition:opacity 200ms ease-out}
    .pp-box{position:relative;width:100%;max-width:26rem;max-height:90vh;overflow:auto;background:#fff;border-radius:1rem;padding:1.75rem 1.5rem 1.25rem;text-align:center;box-shadow:0 25px 50px -12px rgba(0,0,0,.25);border:1px solid #EAE0D3;border-top:4px solid #5A121D;opacity:0;transform:translateY(1rem);transition:opacity 150ms ease-in,transform 150ms ease-in}
    @media (min-width:640px){.pp-box{transform:translateY(0) scale(.95)}}
    .pp-show .pp-box{opacity:1;transform:none;transition:opacity 200ms ease-out,transform 200ms ease-out}
    .pp-icon{width:3.5rem;height:3.5rem;border-radius:9999px;margin:0 auto .9rem;display:flex;align-items:center;justify-content:center}
    .pp-icon svg{width:1.75rem;height:1.75rem}
    .pp-success .pp-icon{background:#D1FAE5;color:#047857}
    .pp-error .pp-icon,.pp-danger .pp-icon{background:#FEE2E2;color:#B91C1C}
    .pp-warning .pp-icon{background:#FEF3C7;color:#B45309}
    .pp-info .pp-icon{background:#DBEAFE;color:#1D4ED8}
    .pp-title{margin:0;font-size:1.125rem;font-weight:800;color:#5A121D}
    .pp-msg{margin:.5rem 0 0;font-size:.875rem;line-height:1.55;color:#4C4F54;white-space:pre-line}
    .pp-list{margin:.6rem 0 0;padding:.6rem .75rem .6rem 1.6rem;text-align:left;font-size:.8125rem;line-height:1.5;color:#991B1B;background:#FEF2F2;border:1px solid #FECACA;border-radius:.6rem}
    .pp-list li{margin:.15rem 0}
    .pp-actions{display:flex;gap:.6rem;justify-content:center;margin-top:1.4rem}
    .pp-btn{flex:1;min-width:0;padding:.6rem 1rem;border-radius:.6rem;font:inherit;font-size:.875rem;font-weight:700;cursor:pointer;border:1px solid transparent;transition:filter .15s,background .15s}
    .pp-btn:focus-visible{outline:2px solid #8F0B13;outline-offset:2px}
    .pp-btn-cancel{background:#fff;color:#5A121D;border-color:#EAE0D3}
    .pp-btn-cancel:hover{background:#FAF6F0}
    .pp-btn-ok{background:#8F0B13;color:#fff}
    .pp-btn-ok:hover{filter:brightness(1.12)}
    .pp-success .pp-btn-ok{background:#059669}
    .pp-danger .pp-btn-ok,.pp-error .pp-btn-ok{background:#B91C1C}
    .pp-warning .pp-btn-ok{background:#D97706}
    .pp-info .pp-btn-ok{background:#2563EB}
    .pp-bar{position:absolute;left:0;bottom:0;height:3px;width:100%;background:#059669;border-radius:0 0 .9rem .9rem;transform-origin:left}
    @keyframes pp-bar{from{transform:scaleX(1)}to{transform:scaleX(0)}}
</style>

<script>
(function () {
    if (window.Popup) { return; }

    var ICONS = {
        success: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>',
        error:   '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>',
        warning: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v3.5m0 3.5h.01M10.3 4.6L3.4 16.5A2 2 0 005.1 19.5h13.8a2 2 0 001.7-3L13.7 4.6a2 2 0 00-3.4 0z"/>',
        info:    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M12 8h.01M11 12h1v4h1m8-4a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        question:'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M8.2 9a3.8 3.8 0 117.2 1.6c-.7 1.2-2.4 1.6-2.4 3.2M12 17.5h.01"/>'
    };
    var ICON_FOR = { success: 'success', error: 'error', danger: 'warning', warning: 'warning', info: 'info', question: 'question' };
    var TITLE_FOR = { success: 'Berhasil', error: 'Terjadi Kesalahan', danger: 'Konfirmasi', warning: 'Konfirmasi', info: 'Informasi', question: 'Konfirmasi' };

    var queue = [];
    var active = false;

    function el(tag, cls, text) {
        var n = document.createElement(tag);
        if (cls) { n.className = cls; }
        if (text != null) { n.textContent = text; }
        return n;
    }

    function run() {
        if (active || !queue.length) { return; }
        active = true;
        var o = queue.shift();
        var type = o.type || 'info';

        var overlay = el('div', 'pp-overlay pp-' + type);
        var box = el('div', 'pp-box');
        var titleId = 'pp-title-' + Date.now();
        overlay.setAttribute('role', o.confirm ? 'alertdialog' : 'dialog');
        overlay.setAttribute('aria-modal', 'true');
        overlay.setAttribute('aria-labelledby', titleId);

        var icon = el('div', 'pp-icon');
        icon.innerHTML = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">' + (ICONS[ICON_FOR[type]] || ICONS.info) + '</svg>';
        box.appendChild(icon);

        var h = el('h2', 'pp-title', o.title || TITLE_FOR[type] || 'Informasi');
        h.id = titleId;
        box.appendChild(h);
        if (o.message) { box.appendChild(el('p', 'pp-msg', o.message)); }
        if (o.items && o.items.length) {
            var ul = el('ul', 'pp-list');
            o.items.forEach(function (t) { ul.appendChild(el('li', null, t)); });
            box.appendChild(ul);
        }

        var actions = el('div', 'pp-actions');
        var cancelBtn = null;
        if (o.confirm) {
            cancelBtn = el('button', 'pp-btn pp-btn-cancel', o.cancelText || 'Batal');
            cancelBtn.type = 'button';
            actions.appendChild(cancelBtn);
        }
        var okBtn = el('button', 'pp-btn pp-btn-ok', o.okText || (o.confirm ? 'Ya, Lanjutkan' : 'OK'));
        okBtn.type = 'button';
        actions.appendChild(okBtn);
        box.appendChild(actions);

        var timer = null;
        if (o.autoClose) {
            var bar = el('div', 'pp-bar');
            bar.style.animation = 'pp-bar ' + o.autoClose + 'ms linear forwards';
            box.appendChild(bar);
            timer = setTimeout(function () { close(true); }, o.autoClose);
            box.addEventListener('mouseenter', function () { clearTimeout(timer); bar.style.display = 'none'; });
        }

        overlay.appendChild(box);
        var prevFocus = document.activeElement;
        var done = false;

        function close(result) {
            if (done) { return; }
            done = true;
            clearTimeout(timer);
            document.removeEventListener('keydown', onKey, true);
            overlay.classList.remove('pp-show');
            setTimeout(function () {
                overlay.remove();
                if (!document.querySelector('.pp-overlay')) { document.documentElement.style.overflow = overflowBefore; }
                if (prevFocus && prevFocus.focus) { try { prevFocus.focus(); } catch (e) {} }
                active = false;
                o.resolve(result);
                run();
            }, 150);
        }

        function onKey(e) {
            if (e.key === 'Escape') {
                e.preventDefault();
                e.stopPropagation(); // jangan ikut menutup modal di belakang popup
                close(o.confirm ? false : true);
            } else if (e.key === 'Tab') {
                var f = box.querySelectorAll('button');
                var first = f[0], last = f[f.length - 1];
                if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
                else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
            }
        }

        okBtn.addEventListener('click', function () { close(true); });
        if (cancelBtn) { cancelBtn.addEventListener('click', function () { close(false); }); }
        overlay.addEventListener('mousedown', function (e) { if (e.target === overlay && !o.confirm) { close(true); } });
        document.addEventListener('keydown', onKey, true);

        var overflowBefore = document.documentElement.style.overflow;
        document.documentElement.style.overflow = 'hidden';
        document.body.appendChild(overlay);
        // Paksa browser menghitung keadaan awal (transparan) dulu, baru jalankan animasi masuk
        void overlay.offsetWidth;
        requestAnimationFrame(function () {
            overlay.classList.add('pp-show');
            // Aksi berisiko: fokus awal di "Batal" agar tidak terkonfirmasi tanpa sengaja.
            (cancelBtn && (type === 'danger') ? cancelBtn : okBtn).focus();
        });
    }

    function push(opts) {
        return new Promise(function (resolve) {
            opts.resolve = resolve;
            queue.push(opts);
            run();
        });
    }

    window.Popup = {
        alert: function (opts) {
            opts = Object.assign({}, typeof opts === 'string' ? { message: opts } : opts);
            if (opts.type === 'success' && opts.autoClose == null) { opts.autoClose = 4000; }
            return push(opts);
        },
        confirm: function (opts) {
            opts = Object.assign({ type: 'question' }, typeof opts === 'string' ? { message: opts } : opts);
            opts.confirm = true;
            return push(opts);
        }
    };

    // Konfirmasi otomatis untuk <form data-confirm="...">
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form || !form.hasAttribute || !form.hasAttribute('data-confirm')) { return; }
        if (form.__ppLolos) { form.__ppLolos = false; return; }

        e.preventDefault();
        e.stopImmediatePropagation();
        var pengirim = e.submitter || null;

        window.Popup.confirm({
            type: form.getAttribute('data-confirm-type') || 'question',
            title: form.getAttribute('data-confirm-title') || undefined,
            message: form.getAttribute('data-confirm'),
            okText: form.getAttribute('data-confirm-ok') || undefined
        }).then(function (ya) {
            if (!ya) { return; }
            form.__ppLolos = true;
            if (form.requestSubmit) { form.requestSubmit(pengirim); } else { form.submit(); }
        });
    }, true);

    // Flash session dari server
    var flash = @json($antrian);
    function tampilkanFlash() { flash.forEach(function (f) { window.Popup.alert(f); }); }
    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', tampilkanFlash); }
    else { tampilkanFlash(); }
})();
</script>
