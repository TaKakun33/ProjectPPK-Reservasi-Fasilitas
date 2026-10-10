{{-- Pop-up alasan tolak / batalkan reservasi + skrip pembukanya. Dipakai di daftar, dashboard, dan halaman detail petugas. --}}
    {{-- Modal Alasan Penolakan --}}
    <div id="reject-modal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-[#252B2B]/60 backdrop-blur-[2px] px-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 border border-cream-border border-t-4 border-t-maroon-800">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <h3 class="font-extrabold text-lg text-maroon-800 leading-tight">Tolak Reservasi</h3>
                    <p class="text-xs mt-0.5" style="color:#4C4F54;">
                        Berikan alasan penolakan agar pemohon memahami alasan keputusan ini.
                    </p>
                </div>
                <button type="button" onclick="closeRejectModal()" aria-label="Tutup" class="shrink-0 -mr-2 -mt-1 w-8 h-8 rounded-full flex items-center justify-center text-[#4C4F54] hover:bg-cream-50 hover:text-maroon-800 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="reject-form" method="POST" class="mt-4">
                @csrf
                @method('PATCH')

                <label for="alasan_ditolak" class="block text-xs font-bold mb-1" style="color:#252B2B;">
                    Alasan Penolakan <span class="text-red-600">*</span>
                </label>
                <textarea id="alasan_ditolak" name="alasan_ditolak" rows="4" required maxlength="500"
                          class="w-full rounded-xl text-xs sm:text-sm p-3 transition focus:outline-none"
                          style="border:1px solid #EAE0D3; background:#FAF6F0;"
                          placeholder="Contoh: Fasilitas sedang dalam pemeliharaan berkala pada jadwal yang diajukan."></textarea>

                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" onclick="closeRejectModal()"
                            class="px-4 py-2 text-xs font-bold rounded-xl transition"
                            style="background:#FAF6F0; color:#4C4F54; border:1px solid #EAE0D3;">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-xs font-bold text-white rounded-xl transition shadow-xs hover:brightness-110"
                            style="background:#8F0B13;">
                        Tolak Reservasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Alasan Pembatalan --}}
    <div id="cancel-modal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-[#252B2B]/60 backdrop-blur-[2px] px-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 border border-cream-border border-t-4 border-t-maroon-800">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <h3 class="font-extrabold text-lg text-maroon-800 leading-tight">Batalkan Reservasi</h3>
                    <p class="text-xs mt-0.5" style="color:#4C4F54;">
                        Jelaskan alasan pembatalan reservasi yang sebelumnya telah disetujui.
                    </p>
                </div>
                <button type="button" onclick="closeCancelModal()" aria-label="Tutup" class="shrink-0 -mr-2 -mt-1 w-8 h-8 rounded-full flex items-center justify-center text-[#4C4F54] hover:bg-cream-50 hover:text-maroon-800 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="cancel-form" method="POST" class="mt-4">
                @csrf
                @method('PATCH')

                <label for="cancellation_reason" class="block text-xs font-bold mb-1" style="color:#252B2B;">
                    Alasan Pembatalan <span class="text-red-600">*</span>
                </label>
                <textarea id="cancellation_reason" name="cancellation_reason" rows="4" required maxlength="500"
                          class="w-full rounded-xl text-xs sm:text-sm p-3 transition focus:outline-none"
                          style="border:1px solid #EAE0D3; background:#FAF6F0;"
                          placeholder="Contoh: Terjadi kendala teknis kelistrikan mendadak pada ruangan."></textarea>

                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" onclick="closeCancelModal()"
                            class="px-4 py-2 text-xs font-bold rounded-xl transition"
                            style="background:#FAF6F0; color:#4C4F54; border:1px solid #EAE0D3;">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-xs font-bold text-white rounded-xl transition shadow-xs hover:brightness-110"
                            style="background:#8F0B13;">
                        Proses Pembatalan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Esc dan klik latar menutup pop-up, sama seperti pop-up formulir lainnya
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !document.querySelector('.pp-overlay')) { closeRejectModal(); closeCancelModal(); }
        });
        ['reject-modal', 'cancel-modal'].forEach(function (id) {
            document.getElementById(id).addEventListener('mousedown', function (e) {
                if (e.target === this) { id === 'reject-modal' ? closeRejectModal() : closeCancelModal(); }
            });
        });

        // Animasi sama dengan pop-up formulir: latar memudar, panel naik sambil membesar sedikit
        function tampilModal(id) {
            const modal = document.getElementById(id);
            const panel = modal.firstElementChild;
            const sm = window.matchMedia('(min-width: 640px)').matches;
            modal.style.transition = 'none';
            panel.style.transition = 'none';
            modal.style.opacity = '0';
            panel.style.opacity = '0';
            panel.style.transform = 'translateY(1rem)' + (sm ? ' scale(0.95)' : '');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            void modal.offsetWidth;
            modal.style.transition = 'opacity 200ms ease-out';
            panel.style.transition = 'opacity 200ms ease-out, transform 200ms ease-out';
            modal.style.opacity = '1';
            panel.style.opacity = '1';
            panel.style.transform = 'none';
        }

        function sembunyiModal(id) {
            const modal = document.getElementById(id);
            if (modal.classList.contains('hidden')) return;
            const panel = modal.firstElementChild;
            const sm = window.matchMedia('(min-width: 640px)').matches;
            modal.style.transition = 'opacity 150ms ease-in';
            panel.style.transition = 'opacity 150ms ease-in, transform 150ms ease-in';
            modal.style.opacity = '0';
            panel.style.opacity = '0';
            panel.style.transform = 'translateY(1rem)' + (sm ? ' scale(0.95)' : '');
            setTimeout(function () {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 150);
        }

        function openRejectModal(actionUrl) {
            document.getElementById('reject-form').setAttribute('action', actionUrl);
            document.getElementById('alasan_ditolak').value = '';
            tampilModal('reject-modal');
        }

        function closeRejectModal() { sembunyiModal('reject-modal'); }

        function openCancelModal(actionUrl) {
            document.getElementById('cancel-form').setAttribute('action', actionUrl);
            document.getElementById('cancellation_reason').value = '';
            tampilModal('cancel-modal');
        }

        function closeCancelModal() { sembunyiModal('cancel-modal'); }
    </script>
