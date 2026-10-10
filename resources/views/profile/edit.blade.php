{{--
    Halaman Profil Akun. Satu halaman untuk semua peran; kerangka (layout) menyesuaikan peran:
    admin -> portal admin, petugas -> portal petugas, pengguna -> layout publik.
    Gaya ditulis mandiri (.pf-*) agar tidak bergantung pada build Tailwind.
--}}
@php
    $peran      = $user->role;
    $layout     = match ($peran) {
        \App\Enums\UserRole::Admin   => 'admin-layout',
        \App\Enums\UserRole::Petugas => 'petugas-layout',
        default           => 'app-layout',
    };
    $bolehHapus = $peran === \App\Enums\UserRole::Pengguna;

    // Tab yang dibuka saat halaman dimuat: ikuti bag error yang aktif
    $tabAwal = 'akun';
    if ($errors->updatePassword->any()) { $tabAwal = 'keamanan'; }
    if ($errors->userDeletion->any())   { $tabAwal = 'bahaya'; }
    if (session('status') === 'password-updated') { $tabAwal = 'keamanan'; }

    $labelPeran = [
        'admin'    => 'Administrator',
        'petugas'  => 'Petugas Fasilitas',
        'pengguna' => 'Pengguna Kampus',
    ][$peran->value] ?? $peran->label();

    $labelStatus = [
        'verified'  => ['Terverifikasi', '#D1FAE5', '#065F46'],
        'pending'   => ['Menunggu Verifikasi', '#FEF3C7', '#92400E'],
        'rejected'  => ['Ditolak', '#FEE2E2', '#991B1B'],
        'suspended' => ['Ditangguhkan', '#E5E7EB', '#374151'],
    ][$user->account_status] ?? [ucfirst((string) $user->account_status), '#E5E7EB', '#374151'];
@endphp

<x-dynamic-component :component="$layout">
    <x-slot name="header">
        <div>
            <h2 class="font-extrabold text-lg leading-tight text-maroon-800">Profil Akun</h2>
            <p class="text-xs mt-0.5" style="color:#4C4F54;">Kelola informasi pribadi dan keamanan akun Anda</p>
        </div>
    </x-slot>

    @include('profile.partials.styles')

    <div class="pf-page" x-data="{ tab: @js($tabAwal) }">
        <div class="pf-wrap">

            {{-- Kartu identitas --}}
            <section class="pf-hero" aria-label="Ringkasan akun">
                <div class="pf-hero-bg" aria-hidden="true"></div>
                <div class="pf-hero-body">
                    <div class="pf-avatar" aria-hidden="true">{{ strtoupper(mb_substr($user->name, 0, 1)) }}</div>
                    <div class="pf-hero-info">
                        <h1 class="pf-name">{{ $user->name }}</h1>
                        <p class="pf-email">{{ $user->email }}</p>
                        <div class="pf-badges">
                            <span class="pf-badge" style="background:#FBE4E5;color:#5A121D;">{{ $labelPeran }}</span>
                            <span class="pf-badge" style="background:{{ $labelStatus[1] }};color:{{ $labelStatus[2] }};">
                                <span class="pf-dot" style="background:{{ $labelStatus[2] }};"></span>{{ $labelStatus[0] }}
                            </span>
                        </div>
                    </div>
                    <dl class="pf-meta">
                        <div>
                            <dt>Bergabung sejak</dt>
                            <dd>{{ $user->created_at?->locale('id')->translatedFormat('d F Y') ?? '-' }}</dd>
                        </div>
                        @if($statistik)
                            <div>
                                <dt>Total reservasi</dt>
                                <dd>{{ $statistik['reservasi'] }}</dd>
                            </div>
                            <div>
                                <dt>Total laporan</dt>
                                <dd>{{ $statistik['laporan'] }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </section>

            <div class="pf-grid">
                {{-- Navigasi bagian --}}
                <nav class="pf-tabs" role="tablist" aria-label="Bagian profil">
                    <button type="button" role="tab" class="pf-tab" :class="{ 'is-active': tab === 'akun' }" :aria-selected="tab === 'akun'" x-on:click="tab = 'akun'">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                        <span>Informasi Akun</span>
                    </button>
                    <button type="button" role="tab" class="pf-tab" :class="{ 'is-active': tab === 'keamanan' }" :aria-selected="tab === 'keamanan'" x-on:click="tab = 'keamanan'">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                        <span>Keamanan</span>
                    </button>
                    @if($bolehHapus)
                        <button type="button" role="tab" class="pf-tab pf-tab-danger" :class="{ 'is-active': tab === 'bahaya' }" :aria-selected="tab === 'bahaya'" x-on:click="tab = 'bahaya'">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.3 3.9L1.8 18a2 2 0 001.7 3h17a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0zM12 9v4M12 17h.01"/></svg>
                            <span>Zona Berbahaya</span>
                        </button>
                    @endif
                </nav>

                {{-- Isi bagian --}}
                <div class="pf-panels">
                    <section class="pf-card" x-show="tab === 'akun'" role="tabpanel" @if($tabAwal !== 'akun') x-cloak style="display:none" @endif>
                        @include('profile.partials.update-profile-information-form')
                    </section>

                    <section class="pf-card" x-show="tab === 'keamanan'" role="tabpanel" @if($tabAwal !== 'keamanan') x-cloak style="display:none" @endif>
                        @include('profile.partials.update-password-form')
                    </section>

                    @if($bolehHapus)
                        <section class="pf-card pf-card-danger" x-show="tab === 'bahaya'" role="tabpanel" @if($tabAwal !== 'bahaya') x-cloak style="display:none" @endif>
                            @include('profile.partials.delete-user-form')
                        </section>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
