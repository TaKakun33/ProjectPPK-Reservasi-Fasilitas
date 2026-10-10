{{--
    Pop-up yang isinya dimuat lewat AJAX dari sebuah URL (untuk detail / sunting per baris data).
    Buka dengan: $dispatch('buka-ajax', { name: 'nama', url: '...' })
    Server mengenali permintaan ini lewat $request->ajax() dan hanya mengembalikan potongan (partial) tanpa layout.
    $htmlAwal dipakai membuka kembali pop-up beserta isinya setelah validasi server gagal.
--}}
@props(['name', 'title', 'subtitle' => null, 'maxWidth' => '2xl', 'htmlAwal' => null])

<x-dialog-form :name="$name" :title="$title" :subtitle="$subtitle" :max-width="$maxWidth" :show="filled($htmlAwal)">
    <div x-data="{
            html: @js($htmlAwal ?? ''),
            memuat: false,
            gagal: false,
            url: null,
            async muat(url) {
                this.url = url;
                this.html = '';
                this.gagal = false;
                this.memuat = true;
                window.dispatchEvent(new CustomEvent('open-modal', { detail: @js($name) }));
                try {
                    const res = await fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                        credentials: 'same-origin'
                    });
                    // Sesi habis / dialihkan (mis. ke halaman masuk): ikuti pengalihan sebagai halaman penuh
                    if (res.redirected) { window.location.href = res.url; return; }
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    this.html = await res.text();
                } catch (e) {
                    this.gagal = true;
                } finally {
                    this.memuat = false;
                }
            }
         }"
         x-on:buka-ajax.window="$event.detail.name === @js($name) ? muat($event.detail.url) : null">

        <div x-show="memuat" x-cloak class="py-10 flex flex-col items-center gap-3 text-sm text-[#4C4F54]" role="status">
            <svg class="w-6 h-6 animate-spin text-maroon-700" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
            Memuat data...
        </div>

        <div x-show="gagal" x-cloak class="py-8 text-center text-sm">
            <p class="font-semibold text-red-700">Data gagal dimuat.</p>
            <div class="mt-3 flex justify-center gap-2">
                <button type="button" x-on:click="muat(url)" class="px-3 py-1.5 rounded-lg border border-cream-border text-maroon-800 font-semibold hover:bg-cream-50">Coba lagi</button>
                <a x-bind:href="url" class="px-3 py-1.5 rounded-lg bg-maroon-700 text-cream font-semibold hover:bg-maroon-900">Buka sebagai halaman</a>
            </div>
        </div>

        <div x-html="html"></div>
    </div>
</x-dialog-form>
