{{--
    Penyesuaian tampilan MOBILE khusus tamu (belum login) dan pengguna biasa.
    Dipasang hanya di layouts.app dan layouts.guest, jadi tidak menyentuh portal admin / petugas.

    Ditulis mandiri (prefix .mp-) agar langsung berfungsi tanpa build ulang Tailwind,
    sama seperti pola profile/partials/styles dan components/popup.
    Semua aturan ada di dalam media query < 640px (di bawah breakpoint `sm` Tailwind),
    sehingga tampilan tablet / desktop tidak berubah sama sekali.

    Selector penimpa sengaja diawali `body` supaya menang atas utilitas Tailwind
    (termasuk saat `npm run dev`, ketika CSS disuntikkan belakangan oleh Vite).
--}}
<style>
    /* Helper tampil/sembunyi. Default (desktop): .mp-m disembunyikan. */
    .mp-m, .mp-m-flex, .mp-m-inline { display: none; }

    @media (max-width: 639.98px) {
        .mp-m        { display: block; }
        .mp-m-flex   { display: flex; }
        .mp-m-inline { display: inline-flex; }
        body .mp-d   { display: none !important; }

        /* ---------- Dasar ---------- */
        html { -webkit-text-size-adjust: 100%; }
        body .min-h-screen { min-height: 100vh; min-height: 100dvh; }
        a, button, select, summary, [role="button"] {
            touch-action: manipulation;
            -webkit-tap-highlight-color: rgba(143, 11, 19, .12);
        }

        /* iOS Safari memperbesar halaman otomatis bila font input < 16px. Naikkan ke 16px
           dan beri tinggi minimal 44px agar nyaman disentuh. */
        body input:not([type="checkbox"]):not([type="radio"]):not([type="file"]):not([type="hidden"]):not([type="submit"]),
        body select,
        body textarea {
            font-size: 16px !important;
        }
        body input:not([type="checkbox"]):not([type="radio"]):not([type="file"]):not([type="hidden"]):not([type="submit"]),
        body select {
            min-height: 44px;
        }
        body input[type="checkbox"], body input[type="radio"] { width: 1.15rem; height: 1.15rem; }

        /* Teks 11px terlalu kecil di layar HP */
        body [class*="text-[11px]"] { font-size: 12px; }

        /* ---------- Navbar ---------- */
        body nav .mp-nav-bar { height: 3.5rem; }
        body .mp-nav-auth a {
            display: inline-flex; align-items: center; justify-content: center;
            min-height: 36px; padding: 0 .85rem; border-radius: .6rem;
            font-size: .8rem; font-weight: 700; white-space: nowrap;
        }
        body .mp-nav-login  { color: #EFDFC5; border: 1px solid rgba(239, 223, 197, .35); }
        body .mp-nav-daftar { background: #EFDFC5; color: #380F17; border: 1px solid #EFDFC5; }
        body .mp-hamburger  { min-width: 44px; min-height: 44px; }
        /* HP sangat sempit (<= 360px): rapatkan navbar agar tombol Masuk/Daftar tidak terdorong keluar layar */
        @media (max-width: 360px) {
            body .mp-nav-auth { gap: .35rem; }
            body .mp-nav-auth a { padding: 0 .6rem; font-size: .75rem; }
            body nav .mp-nav-bar a.group { gap: .5rem; }
        }
        body .mp-drawer {
            max-height: calc(100vh - 3.5rem); max-height: calc(100dvh - 3.5rem);
            overflow-y: auto; overscroll-behavior: contain;
        }
        body .mp-drawer a, body .mp-drawer button {
            min-height: 48px; display: flex; align-items: center; font-size: 1rem;
        }

        /* ---------- Judul halaman (slot header) ---------- */
        body .mp-header > div { padding-top: 1rem; padding-bottom: 1rem; }
        body .mp-head-row { flex-direction: column; align-items: stretch; gap: .75rem; }
        body .mp-head-row h2 { font-size: 1.15rem; }
        body .mp-head-row a {
            width: 100%; justify-content: center; min-height: 44px; font-size: .9rem;
        }


        /* ---------- Daftar fasilitas: kartu horizontal (foto kiri, info kanan) ---------- */
        body .mp-fcard { flex-direction: row; align-items: stretch; position: relative; min-height: 8rem; }
        body .mp-fcard:hover { transform: none; }
        body .mp-fcard-img {
            width: 36%; max-width: 9.5rem; height: auto; min-height: 8rem; flex-shrink: 0;
        }
        body .mp-fcard-img > img,
        body .mp-fcard-img > [role="img"] {
            position: absolute; inset: 0; width: 100%; height: 100%;
        }
        body .mp-fcard-img .mp-badge-type,
        body .mp-fcard-img .mp-badge-cap { display: none; }
        body .mp-fcard-img .mp-badge-status { right: auto; left: .4rem; top: .4rem; }
        body .mp-fcard-body { padding: .65rem .75rem; min-width: 0; }
        body .mp-fcard-body h3 { font-size: .95rem; line-height: 1.25; -webkit-line-clamp: 2; }
        body .mp-fcard-body .mp-meta { font-size: .78rem; color: #4C4F54; margin-top: .15rem; }
        body .mp-fcard-body .mp-desc { -webkit-line-clamp: 2; font-size: .78rem; line-height: 1.35; margin-bottom: .5rem; }
        /* Seluruh kartu bisa disentuh -> menuju halaman detail */
        body .mp-card-link { color: inherit; text-decoration: none; }
        body .mp-card-link::after { content: ""; position: absolute; inset: 0; z-index: 0; }
        body .mp-fcard-foot { position: relative; z-index: 1; gap: .5rem; }
        body .mp-fcard-foot a {
            min-height: 36px; display: inline-flex; align-items: center; justify-content: center;
            padding: 0 .9rem; border-radius: .6rem; font-size: .8rem;
        }
        body .mp-btn-detail { border: 1px solid #EAE0D3; background: #FAF6F0; }

        /* ---------- Detail fasilitas ---------- */
        body .mp-crumb { flex-wrap: nowrap; overflow: hidden; }
        body .mp-crumb > * { flex-shrink: 0; }
        body .mp-crumb > span:last-child { flex-shrink: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        body .mp-section { padding: 1rem; }
        body .mp-dateform { width: 100%; justify-content: space-between; padding: .25rem .85rem; }
        body .mp-dateform input[type="date"] { flex: 1; text-align: right; min-height: 40px; }
        body .mp-timeline { height: 2.5rem; }
        body .mp-timeline > div { min-width: 0; }
        body .mp-slot {
            flex-direction: column; align-items: flex-start; justify-content: center;
            gap: .3rem; min-height: 3.5rem; padding: .55rem .7rem;
        }
        body .mp-slot > span:first-child { font-size: 13px; }
        body .mp-legend { gap: .5rem 1rem; }
        body .mp-cta-bar {
            padding-bottom: calc(.75rem + env(safe-area-inset-bottom, 0px));
            box-shadow: 0 -4px 16px rgba(56, 15, 23, .1);
        }
        body .mp-cta-bar a { min-height: 48px; font-size: .95rem; }

        /* ---------- Kartu riwayat (pengganti tabel) ---------- */
        body .mp-cardlist { display: flex; flex-direction: column; gap: .75rem; padding: .75rem; }
        body .mp-rcard {
            background: #fff; border: 1px solid #EAE0D3; border-radius: .9rem; padding: .9rem;
            box-shadow: 0 1px 2px rgba(56, 15, 23, .05);
        }
        body .mp-rcard-top { display: flex; align-items: flex-start; justify-content: space-between; gap: .6rem; }
        body .mp-rcard-top h3 { margin: 0; font-size: .95rem; font-weight: 800; line-height: 1.3; color: #252B2B; overflow-wrap: anywhere; }
        body .mp-rcard-top > span { flex-shrink: 0; }
        body .mp-rcard-meta { display: flex; flex-wrap: wrap; gap: .25rem 1rem; margin: .5rem 0 0; font-size: .82rem; color: #4C4F54; font-weight: 600; }
        body .mp-rcard-text {
            margin: .5rem 0 0; font-size: .85rem; line-height: 1.45; color: #4C4F54;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }
        body .mp-rcard-btn {
            display: flex; align-items: center; justify-content: center; margin-top: .75rem;
            min-height: 44px; border-radius: .7rem; background: #8F0B13; color: #EFDFC5;
            font-size: .875rem; font-weight: 800;
        }

        /* ---------- Form & pop-up ---------- */
        body .mp-dialog-panel { width: calc(100% - 1rem); margin: .5rem auto; border-radius: 1rem; }
        body .mp-dialog-head {
            position: sticky; top: 0; z-index: 2; background: #fff;
            padding: 1rem 1rem .6rem; border-radius: 1rem 1rem 0 0;
            border-bottom: 1px solid #F1E9DC;
        }
        body .mp-dialog-body { padding: 1rem; }
        body .mp-dialog-close { width: 2.5rem; height: 2.5rem; margin: -.25rem -.25rem 0 0; }
        body .mp-timegrid { grid-template-columns: 1fr 1fr; gap: .75rem; }
        body .mp-rules-toggle { cursor: pointer; min-height: 32px; }
        body .mp-actions { flex-direction: column-reverse; gap: .5rem; }
        body .mp-actions > * { width: 100%; min-height: 48px; justify-content: center; }
        body .mp-pad { padding: 1rem; }

        /* ---------- Halaman masuk / daftar ---------- */
        body .mp-auth-card { margin-top: 1.25rem; }
    }
</style>
