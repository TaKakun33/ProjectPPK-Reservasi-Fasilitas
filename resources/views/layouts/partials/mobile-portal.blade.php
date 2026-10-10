{{--
    Penyesuaian tampilan MOBILE untuk portal Admin dan Petugas (dipasang di components/portal-layout).
    Pola sama dengan mobile-pengguna: gaya mandiri (prefix .mb-), tanpa build ulang Tailwind,
    dan seluruh aturan ada di dalam media query sehingga tampilan desktop tidak berubah.

    Ide utamanya:
      - tabel data berubah menjadi kartu (label kolom diisi otomatis oleh skrip kecil di portal-layout),
      - formulir filter bertumpuk dengan tombol selebar layar,
      - tombol aksi di judul halaman menjadi tombol melayang (.mb-fab),
      - sidebar menjadi laci dengan target sentuh yang lebih besar.
--}}
<style>
    .mb-show-flex { display: none; }

    @media (max-width: 767.98px) {
        .mb-hide { display: none !important; }
        .mb-show-flex { display: flex; }

        /* ---------- Judul halaman ---------- */
        body .mb-topbar > div { padding: .6rem .75rem; gap: .5rem; }
        body .mb-topbar button[aria-label="Buka menu"] { min-width: 44px; min-height: 44px; display: inline-flex; align-items: center; justify-content: center; }
        body .mb-topbar h2 { font-size: 1.02rem; line-height: 1.25; }
        body .mb-topbar p { display: none; }
        body .mb-topbar h2 > span { display: inline-block; margin: .15rem 0 0 0; }

        /* Tombol aksi di judul -> tombol melayang di kanan bawah */
        body .mb-topbar .mb-fab {
            position: fixed; right: 1rem; bottom: calc(1rem + env(safe-area-inset-bottom, 0px)); z-index: 40;
            display: inline-flex; align-items: center; gap: .4rem; min-height: 48px; padding: 0 1.1rem;
            border-radius: 9999px; font-size: .85rem; font-weight: 800; box-shadow: 0 8px 20px rgba(56, 15, 23, .35);
        }
        body .mb-main { padding-bottom: 5.5rem; }

        /* ---------- Sidebar (laci) ---------- */
        body aside { width: min(18rem, 86vw); }
        body aside nav a { min-height: 48px; font-size: .95rem; }
        body aside .border-t a, body aside .border-t button { min-height: 44px; font-size: .85rem; }

        /* ---------- Isi halaman ---------- */
        body .mb-main > div[class*="px-4"] { padding-left: .75rem; padding-right: .75rem; padding-top: .9rem; }

        /* ---------- Formulir filter / pencarian ---------- */
        body .mb-main form.flex.flex-wrap { gap: .6rem; align-items: stretch; }
        body .mb-main form.flex.flex-wrap > div { flex: 1 1 100%; min-width: 0; }
        body .mb-main form.flex.flex-wrap select,
        body .mb-main form.flex.flex-wrap input[type="text"],
        body .mb-main form.flex.flex-wrap input[type="date"] { width: 100%; }
        body .mb-main form.flex.flex-wrap > button[type="submit"] {
            flex: 1 1 0; min-height: 44px; display: inline-flex; align-items: center; justify-content: center;
        }
        body .mb-main form.flex.flex-wrap > a {
            flex: 1 1 100%; min-height: 44px; display: inline-flex; align-items: center; justify-content: center; text-align: center;
        }
        body .mb-main form.flex.flex-wrap > a[class*="w-[38px]"] { flex: 0 0 44px; width: 44px; height: 44px; }
        body .mb-main form.flex.flex-wrap > button[type="submit"] + a[class*="w-[38px]"] { flex: 0 0 44px; }
        /* Tab status yang bisa digeser: sembunyikan scrollbar */
        body .mb-main .overflow-x-auto:not(:has(table)) { scrollbar-width: none; -webkit-overflow-scrolling: touch; }

        /* ---------- Kartu statistik dashboard: 2 kolom ringkas ---------- */
        body .mb-main .grid[class*="lg:grid-cols-4"] { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .6rem; }
        body .mb-main .grid[class*="lg:grid-cols-4"] > a { padding: .8rem; border-radius: 1rem; }
        body .mb-main .grid[class*="lg:grid-cols-4"] > a .w-10 { width: 2rem; height: 2rem; border-radius: .6rem; }
        body .mb-main .grid[class*="lg:grid-cols-4"] > a .w-10 svg { width: 1rem; height: 1rem; }
        body .mb-main .grid[class*="lg:grid-cols-4"] > a .text-3xl { font-size: 1.6rem; line-height: 1.1; }
        body .mb-main .grid[class*="lg:grid-cols-4"] > a .tracking-wider { font-size: .62rem; line-height: 1.25; letter-spacing: .04em; }
        body .mb-main .grid[class*="lg:grid-cols-4"] > a p { font-size: .68rem; line-height: 1.3; margin-top: .35rem; }
        body .mb-main .grid[class*="lg:grid-cols-4"] > a > .mb-3 { margin-bottom: .5rem; gap: .4rem; align-items: flex-start; }

        /* ---------- Tabel -> kartu ---------- */
        body .mb-main .overflow-x-auto:has(table) { overflow: visible; }
        body .mb-main table, body .mb-main tbody { display: block; width: 100%; }
        body .mb-main table thead { display: none; }
        body .mb-main table tbody { padding: .65rem; display: flex; flex-direction: column; gap: .65rem; }
        body .mb-main table tbody > tr {
            display: block; background: #fff; border: 1px solid #EAE0D3; border-radius: .95rem;
            padding: .25rem .95rem; box-shadow: 0 1px 2px rgba(56, 15, 23, .05);
        }
        body .mb-main table tbody > tr.bg-red-50 { background: #FEF2F2; }
        body .mb-main table tbody > tr.bg-orange-50 { background: #FFF7ED; }
        body .mb-main table tbody > tr > td {
            display: block; width: auto; max-width: none !important; padding: .55rem 0 !important;
            text-align: left !important; white-space: normal !important; overflow: visible !important;
            text-overflow: clip !important; border: 0; border-bottom: 1px dashed #EAE0D3;
            font-size: .875rem; overflow-wrap: anywhere;
        }
        body .mb-main table tbody > tr > td::before {
            content: attr(data-label); display: block; margin-bottom: .15rem;
            font-size: .66rem; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; color: #7C7F84;
        }
        /* Kolom pendek (status, angka): label di kiri, nilai di kanan */
        body .mb-main table tbody > tr > td.text-center:not(:last-child) {
            display: flex; align-items: center; justify-content: space-between; gap: .75rem;
        }
        body .mb-main table tbody > tr > td.text-center:not(:last-child)::before { margin: 0; flex: 1; }
        /* Kolom pertama = judul kartu */
        body .mb-main table tbody > tr > td:first-child { font-size: 1rem; font-weight: 800; padding-top: .75rem !important; }
        body .mb-main table tbody > tr > td:first-child::before { display: none; }
        body .mb-main table tbody > tr > td:first-child:has(img) { padding-bottom: .35rem !important; }
        body .mb-main table tbody > tr > td:first-child img,
        body .mb-main table tbody > tr > td:first-child > div[role="img"] { width: 100%; height: 8rem; border-radius: .7rem; }
        /* Kolom terakhir = tombol aksi, selebar kartu */
        body .mb-main table tbody > tr > td:last-child { border-bottom: 0; padding: .7rem 0 .75rem !important; }
        body .mb-main table tbody > tr > td:last-child::before { display: none; }
        body .mb-main table tbody > tr > td:last-child > div { gap: .5rem; justify-content: stretch; }
        body .mb-main table tbody > tr > td:last-child form { flex: 1 1 8rem; display: flex; }
        body .mb-main table tbody > tr > td:last-child a,
        body .mb-main table tbody > tr > td:last-child button {
            display: flex; flex: 1; align-items: center; justify-content: center; width: 100%;
            min-height: 44px; padding: 0 1rem; border-radius: .7rem; font-size: .85rem;
        }
        body .mb-main table tbody > tr > td:last-child > a { width: 100%; }
        /* Nama fasilitas = judul kartu bila kolom pertama berisi foto */
        body .mb-main table tbody > tr > td:first-child:has(img) + td { font-size: 1rem; font-weight: 800; border-bottom: 0; padding-top: .2rem !important; }
        body .mb-main table tbody > tr > td:first-child:has(img) + td::before { display: none; }
        /* Tabel yang tidak punya kolom aksi (mis. rekap): kolom terakhir diperlakukan seperti kolom biasa */
        body .mb-main table.mb-noaction tbody > tr > td:last-child {
            display: flex; align-items: center; justify-content: space-between; gap: .75rem; padding: .55rem 0 .65rem !important;
        }
        body .mb-main table.mb-noaction tbody > tr > td:last-child::before { display: block; margin: 0; flex: 1; }

        /* ---------- Pop-up di portal ---------- */
        body .mp-dialog-body .justify-end,
        body #reject-modal .justify-end, body #cancel-modal .justify-end {
            flex-direction: column-reverse; gap: .5rem; align-items: stretch;
        }
        body .mp-dialog-body .justify-end > *,
        body #reject-modal .justify-end > *, body #cancel-modal .justify-end > * {
            width: 100%; min-height: 46px; margin: 0 !important; justify-content: center; display: inline-flex; align-items: center;
        }
        body .mb-actions { flex-direction: column-reverse; align-items: stretch; gap: .6rem; }
        body .mb-actions > a, body .mb-actions > button { width: 100%; min-height: 46px; display: inline-flex; align-items: center; justify-content: center; }
        body .mb-actions > div { display: flex; gap: .5rem; }
        body .mb-actions > div > *, body .mb-actions > div form { flex: 1; display: flex; }
        body .mb-actions > div button { flex: 1; min-height: 46px; justify-content: center; }
        body #reject-modal > div, body #cancel-modal > div { max-height: 92vh; overflow-y: auto; padding: 1.1rem; }
    }
</style>
