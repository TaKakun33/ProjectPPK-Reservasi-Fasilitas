{{-- Gaya khusus halaman profil (mandiri, tidak perlu build ulang Tailwind). --}}
<style>
    .pf-page{background:#FAF6F0;min-height:100%;padding:1.5rem 1rem 3rem;font-family:'Plus Jakarta Sans',system-ui,sans-serif;color:#252B2B}
    .pf-wrap{max-width:62rem;margin:0 auto;display:flex;flex-direction:column;gap:1.25rem}
    /* Kartu identitas */
    .pf-hero{position:relative;background:#fff;border:1px solid #EAE0D3;border-radius:1rem;overflow:hidden;box-shadow:0 1px 2px rgba(0,0,0,.04)}
    .pf-hero-bg{height:5.5rem;background:linear-gradient(135deg,#380F17 0%,#5A121D 45%,#8F0B13 100%);position:relative;overflow:hidden}
    .pf-hero-bg::after{content:"";position:absolute;right:-2rem;top:-3rem;width:12rem;height:12rem;border-radius:9999px;background:#EFDFC5;opacity:.14}
    .pf-hero-body{display:flex;flex-wrap:wrap;align-items:flex-end;gap:1rem 1.25rem;padding:0 1.5rem 1.4rem}
    .pf-avatar{width:5rem;height:5rem;margin-top:-2.5rem;border-radius:1.1rem;background:#EFDFC5;color:#380F17;border:4px solid #fff;display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:900;box-shadow:0 6px 16px -6px rgba(56,15,23,.45);flex-shrink:0;position:relative;z-index:1}
    .pf-hero-info{flex:1 1 14rem;min-width:0;padding-top:.75rem}
    .pf-name{margin:0;font-size:1.35rem;line-height:1.25;font-weight:800;color:#252B2B;overflow-wrap:anywhere}
    .pf-email{margin:.15rem 0 0;font-size:.875rem;color:#4C4F54;overflow-wrap:anywhere}
    .pf-badges{display:flex;flex-wrap:wrap;gap:.4rem;margin-top:.65rem}
    .pf-badge{display:inline-flex;align-items:center;gap:.35rem;padding:.2rem .65rem;border-radius:9999px;font-size:.72rem;font-weight:700}
    .pf-dot{width:.4rem;height:.4rem;border-radius:9999px;display:inline-block}
    .pf-meta{display:flex;flex-wrap:wrap;gap:1.5rem;margin:0;padding-top:.75rem}
    .pf-meta dt{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#7C7F84}
    .pf-meta dd{margin:.15rem 0 0;font-size:1rem;font-weight:800;color:#252B2B}
    /* Grid */
    .pf-grid{display:grid;grid-template-columns:1fr;gap:1rem;align-items:start}
    @media (min-width:900px){.pf-grid{grid-template-columns:15rem 1fr;gap:1.5rem}}
    .pf-tabs{display:flex;gap:.4rem;overflow-x:auto;padding:.25rem;background:#fff;border:1px solid #EAE0D3;border-radius:.9rem}
    @media (min-width:900px){.pf-tabs{flex-direction:column;position:sticky;top:5.5rem;overflow:visible}}
    .pf-tab{display:flex;align-items:center;gap:.65rem;padding:.65rem .85rem;border:0;border-radius:.65rem;background:transparent;color:#4C4F54;font:inherit;font-size:.875rem;font-weight:600;cursor:pointer;white-space:nowrap;text-align:left;transition:background .15s,color .15s}
    .pf-tab svg{width:1.15rem;height:1.15rem;flex-shrink:0}
    .pf-tab:hover{background:#FAF6F0;color:#5A121D}
    .pf-tab.is-active{background:#5A121D;color:#EFDFC5}
    .pf-tab-danger{color:#991B1B}
    .pf-tab-danger:hover{background:#FEF2F2;color:#991B1B}
    .pf-tab-danger.is-active{background:#B91C1C;color:#fff}
    .pf-tab:focus-visible{outline:2px solid #8F0B13;outline-offset:2px}
    /* Kartu isi */
    .pf-card{background:#fff;border:1px solid #EAE0D3;border-radius:1rem;padding:1.5rem;box-shadow:0 1px 2px rgba(0,0,0,.04)}
    @media (min-width:640px){.pf-card{padding:2rem}}
    .pf-card-danger{border-color:#FECACA}
    .pf-card-head{padding-bottom:1.1rem;margin-bottom:1.4rem;border-bottom:1px solid #EAE0D3}
    .pf-card-title{margin:0;font-size:1.1rem;font-weight:800;color:#5A121D}
    .pf-card-sub{margin:.3rem 0 0;font-size:.85rem;line-height:1.5;color:#4C4F54}
    .pf-form{display:flex;flex-direction:column;gap:1.25rem;max-width:34rem}
    .pf-field label{display:block;margin-bottom:.4rem;font-size:.82rem;font-weight:700;color:#252B2B}
    .pf-input-wrap{position:relative}
    .pf-input{display:block;width:100%;box-sizing:border-box;padding:.65rem .85rem;border:1px solid #D9CBB4;border-radius:.7rem;background:#fff;font:inherit;font-size:.9rem;color:#252B2B;transition:border-color .15s,box-shadow .15s}
    .pf-input:focus{outline:none;border-color:#8F0B13;box-shadow:0 0 0 3px rgba(143,11,19,.15)}
    .pf-input.has-error{border-color:#DC2626}
    .pf-input-wrap .pf-input{padding-right:2.9rem}
    .pf-eye{position:absolute;right:.35rem;top:50%;transform:translateY(-50%);width:2.2rem;height:2.2rem;display:flex;align-items:center;justify-content:center;border:0;background:transparent;color:#7C7F84;border-radius:.5rem;cursor:pointer}
    .pf-eye:hover{color:#5A121D;background:#FAF6F0}
    .pf-eye svg{width:1.2rem;height:1.2rem}
    .pf-hint{margin:.4rem 0 0;font-size:.76rem;line-height:1.45;color:#7C7F84}
    .pf-error{margin:.4rem 0 0;padding:0;list-style:none;font-size:.78rem;font-weight:600;color:#B91C1C}
    .pf-notice{display:flex;gap:.7rem;padding:.85rem 1rem;border-radius:.8rem;font-size:.82rem;line-height:1.5}
    .pf-notice svg{width:1.2rem;height:1.2rem;flex-shrink:0;margin-top:.1rem}
    .pf-notice-info{background:#EFF6FF;border:1px solid #BFDBFE;color:#1E40AF}
    .pf-notice-warn{background:#FEF3C7;border:1px solid #FDE68A;color:#92400E}
    .pf-notice-danger{background:#FEF2F2;border:1px solid #FECACA;color:#991B1B}
    .pf-reveal{padding:1rem;border:1px dashed #D9CBB4;border-radius:.8rem;background:#FAF6F0}
    .pf-actions{display:flex;flex-wrap:wrap;gap:.6rem;align-items:center;padding-top:.5rem}
    .pf-btn{display:inline-flex;align-items:center;justify-content:center;gap:.4rem;padding:.65rem 1.3rem;border-radius:.7rem;border:1px solid transparent;font:inherit;font-size:.875rem;font-weight:700;cursor:pointer;transition:filter .15s,background .15s}
    .pf-btn:focus-visible{outline:2px solid #8F0B13;outline-offset:2px}
    .pf-btn-primary{background:#8F0B13;color:#EFDFC5}
    .pf-btn-primary:hover{filter:brightness(1.12)}
    .pf-btn-ghost{background:#fff;color:#4C4F54;border-color:#D9CBB4}
    .pf-btn-ghost:hover{background:#FAF6F0}
    .pf-btn-danger{background:#B91C1C;color:#fff}
    .pf-btn-danger:hover{filter:brightness(1.1)}
    .pf-btn[disabled]{opacity:.5;cursor:not-allowed;filter:none}
    /* Kekuatan password */
    .pf-meter{display:flex;gap:.3rem;margin-top:.55rem}
    .pf-meter span{flex:1;height:.3rem;border-radius:9999px;background:#EAE0D3;transition:background .2s}
    .pf-rules{list-style:none;margin:.55rem 0 0;padding:0;display:grid;grid-template-columns:1fr;gap:.25rem;font-size:.76rem;color:#7C7F84}
    @media (min-width:480px){.pf-rules{grid-template-columns:1fr 1fr}}
    .pf-rules li{display:flex;align-items:center;gap:.4rem}
    .pf-rules li::before{content:"";width:.55rem;height:.55rem;border-radius:9999px;border:2px solid #D9CBB4;box-sizing:border-box}
    .pf-rules li.ok{color:#047857}
    .pf-rules li.ok::before{background:#059669;border-color:#059669}
    .pf-match{margin:.4rem 0 0;font-size:.76rem;font-weight:600}
    /* ---------- MOBILE: tampilan profil lebih ringkas & rapi ---------- */
    @media (max-width:639.98px){
        .pf-page{padding:.75rem .75rem 2.5rem}
        .pf-wrap{gap:.85rem}
        .pf-hero{border-radius:1.1rem}
        .pf-hero-bg{height:4.25rem}
        .pf-hero-body{flex-direction:column;align-items:center;text-align:center;gap:.25rem;padding:0 1rem 1rem}
        .pf-avatar{width:4.5rem;height:4.5rem;margin-top:-2.25rem;border-radius:9999px;font-size:1.75rem}
        .pf-hero-info{flex:none;width:100%;padding-top:.35rem}
        .pf-name{font-size:1.15rem}
        .pf-email{font-size:.82rem}
        .pf-badges{justify-content:center;margin-top:.55rem}
        .pf-meta{width:100%;display:grid;grid-template-columns:repeat(auto-fit,minmax(0,1fr));gap:0;margin-top:.85rem;padding:.7rem 0 0;border-top:1px solid #EAE0D3}
        .pf-meta>div{padding:0 .35rem}
        .pf-meta>div+div{border-left:1px solid #EAE0D3}
        .pf-meta dt{font-size:.62rem;letter-spacing:.04em}
        .pf-meta dd{font-size:.9rem}
        /* Tab jadi kontrol segmen satu baris, ikon di atas label */
        .pf-tabs{display:grid;grid-auto-flow:column;grid-auto-columns:1fr;gap:.25rem;overflow:visible;padding:.3rem;border-radius:.95rem}
        .pf-tab{flex-direction:column;justify-content:center;gap:.25rem;padding:.55rem .25rem;font-size:.7rem;line-height:1.15;text-align:center;white-space:normal;min-height:3.2rem;border-radius:.7rem}
        .pf-tab svg{width:1.2rem;height:1.2rem}
        .pf-card{padding:1.1rem 1rem;border-radius:1.1rem}
        .pf-card-head{padding-bottom:.85rem;margin-bottom:1.1rem}
        .pf-card-title{font-size:1rem}
        .pf-card-sub{font-size:.8rem}
        .pf-form{gap:1.05rem;max-width:none}
        .pf-input{font-size:16px;min-height:2.9rem}
        .pf-actions{flex-direction:column-reverse;align-items:stretch;gap:.5rem;padding-top:.25rem}
        .pf-actions .pf-btn{width:100%;min-height:2.9rem}
        .pf-reveal{padding:.8rem}
    }
</style>
