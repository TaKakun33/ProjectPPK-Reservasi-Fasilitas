<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\SimpanReservasiRequest;
use App\Models\Facility;
use App\Models\LogStatusReservasi;
use App\Models\Reservation;
use App\Models\User;
use App\Services\ReservationAvailability;
use App\Services\ReservationExpiry;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReservationController extends Controller
{
    // Maksimal reservasi berstatus pending (mendatang) per pengguna
    private const MAKS_PENDING_PER_USER = 3;

    // Maksimal reservasi AKTIF (pending + approved, belum selesai) per pengguna. Tanpa batas ini,
    // reservasi yang sudah disetujui tidak lagi menghitung kuota sehingga satu akun bisa
    // mengunci slot tanpa batas sampai MAKS_HARI_KEDEPAN hari ke depan (slot hoarding).
    private const MAKS_AKTIF_PER_USER = 5;

    // Route /reservasi ini cuma buat role "pengguna". Kalau yang login petugas, 
    // langsung lempar ke halaman reservasi miliknya sendiri di /petugas/reservasi (bukan ditolak) 
    // petugas memang gak boleh ajukan reservasi sendiri (lihat catatan self-approval di routes/reservasi.php),
    // tapi dia tetap punya halaman reservasi versi petugas sendiri. Admin tetap ditolak, gak ada urusan di sini.
    protected function ensurePengguna(): ?RedirectResponse
    {
        $role = auth()->user()->role;

        if ($role === UserRole::Petugas) {
            return redirect()->route('petugas.reservations.index');
        }

        abort_if($role === UserRole::Admin, 403, 'Halaman reservasi ini khusus untuk pengguna.');

        return null;
    }

    // Riwayat & status reservasi milik user yang login.
    public function index(Request $request)
    {
        if ($redirect = $this->ensurePengguna()) {
            return $redirect;
        }

        // Tampilkan status terbaru: reservasi pending yang basi langsung dikedaluwarsakan
        ReservationExpiry::kedaluwarsakanDiamDiam();

        $reservations = Reservation::with('facility')
            ->where('id_user', Auth::id())
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(10);

        return view('reservations.index', compact('reservations'));
    }

    // Form pengajuan reservasi.
    public function create(Request $request)
    {
        if ($redirect = $this->ensurePengguna()) {
            return $redirect;
        }

        $facilities = Facility::where('facility_status', 'aktif')->orderBy('facility_name')->get();

        $selectedFacilityId = $request->input('facility_id');
        // Tanggal harus benar-benar valid (bukan sekadar cocok pola): 2026-99-99 jatuh ke hari ini
        $tanggalInput = (string) $request->input('date');
        $selectedDate = Carbon::canBeCreatedFromFormat($tanggalInput, 'Y-m-d')
            ? Carbon::createFromFormat('!Y-m-d', $tanggalInput)->toDateString()
            : Carbon::today()->toDateString();

        return view('reservations.create', compact('facilities', 'selectedFacilityId', 'selectedDate'));
    }

    // Simpan reservasi baru. Validasi lengkap ada di SimpanReservasiRequest (server-side).
    public function store(SimpanReservasiRequest $request)
    {
        // Pembatasan role sudah ditangani SimpanReservasiRequest::authorize() (403 sebelum validasi)

        // Bersihkan dulu reservasi pending yang sudah kedaluwarsa agar tidak ikut menghitung kuota/slot
        ReservationExpiry::kedaluwarsakanDiamDiam(true);

        $data    = $request->validated();
        $mulai   = $data['start_time'] . ':00';
        $selesai = $data['end_time'] . ':00';

        try {
            DB::transaction(function () use ($data, $mulai, $selesai) {
                // Kunci baris pengguna: mengserialkan pengajuan milik pengguna yang sama, sehingga
                // pengecekan kuota & tabrakan jadwal antar-fasilitas tidak bisa dilewati lewat
                // request paralel. Urutan lock selalu pengguna dulu, baru fasilitas.
                User::whereKey(Auth::id())->lockForUpdate()->first();

                // Kunci baris fasilitas = satu titik serialisasi per fasilitas. Lebih aman
                // daripada lockForUpdate()->exists() pada baris reservasi (gap lock bisa deadlock).
                $fasilitas = Facility::whereKey($data['id_fasilitas'])->lockForUpdate()->first();

                if (! $fasilitas || ! $fasilitas->isReservable()) {
                    throw ValidationException::withMessages([
                        'id_fasilitas' => 'Fasilitas ini sedang tidak aktif atau dalam perbaikan.',
                    ]);
                }

                // Cek bentrok memakai service yang sama (tidak diduplikasi lagi)
                if (ReservationAvailability::hasConflict($data['id_fasilitas'], $data['date'], $mulai, $selesai)) {
                    throw ValidationException::withMessages([
                        'time' => 'Jadwal yang Anda pilih sudah terisi atau bertabrakan dengan reservasi lain yang sedang menunggu konfirmasi/disetujui.',
                    ]);
                }

                // Satu pengguna tidak boleh punya dua reservasi aktif yang jamnya beririsan,
                // walaupun di fasilitas yang berbeda (satu orang tidak bisa berada di dua tempat).
                $bentrokPemesan = Reservation::where('id_user', Auth::id())
                    ->where('date', $data['date'])
                    ->whereIn('reservation_status', ['pending', 'approved'])
                    ->where('start_time', '<', $selesai)
                    ->where('end_time', '>', $mulai)
                    ->exists();

                if ($bentrokPemesan) {
                    throw ValidationException::withMessages([
                        'time' => 'Anda sudah memiliki reservasi lain (di fasilitas apa pun) pada jam yang beririsan. Pilih jam yang berbeda atau batalkan reservasi tersebut.',
                    ]);
                }

                // Kuota per pengguna (cegah satu akun mengunci banyak slot). Hanya reservasi yang
                // belum selesai yang dihitung (yang basi sudah dikedaluwarsakan di atas).
                $jumlahPending = Reservation::where('id_user', Auth::id())
                    ->where('reservation_status', 'pending')
                    ->mendatang()
                    ->count();

                if ($jumlahPending >= self::MAKS_PENDING_PER_USER) {
                    throw ValidationException::withMessages([
                        'time' => 'Anda sudah memiliki ' . self::MAKS_PENDING_PER_USER . ' reservasi yang menunggu persetujuan. Tunggu diproses atau batalkan salah satunya.',
                    ]);
                }

                // Pending + approved yang belum selesai: reservasi yang sudah disetujui tetap menghitung kuota
                $jumlahAktif = Reservation::where('id_user', Auth::id())
                    ->whereIn('reservation_status', ['pending', 'approved'])
                    ->mendatang()
                    ->count();

                if ($jumlahAktif >= self::MAKS_AKTIF_PER_USER) {
                    throw ValidationException::withMessages([
                        'time' => 'Anda sudah memiliki ' . self::MAKS_AKTIF_PER_USER . ' reservasi aktif (menunggu atau disetujui) yang belum berlangsung. Tunggu hingga selesai atau batalkan salah satunya.',
                    ]);
                }

                $reservasi = Reservation::create([
                    'id_user'            => Auth::id(),
                    'id_fasilitas'       => $data['id_fasilitas'],
                    'date'               => $data['date'],
                    'start_time'         => $mulai,
                    'end_time'           => $selesai,
                    'purpose'            => $data['purpose'],
                    'reservation_status' => 'pending',
                ]);

                LogStatusReservasi::create([
                    'id_reservasi'  => $reservasi->id_reservasi,
                    'status_before' => null,
                    'status_after'  => 'pending',
                    'changed_by'    => Auth::id(),
                    'notes'         => 'Reservasi diajukan oleh pemesan.',
                    'created_at'    => now(),
                ]);
            });
        } catch (QueryException $e) {
            // Pengaman terakhir: trigger DB (SIGNAL SQLSTATE 45000) menolak insert yang bentrok.
            // Hanya kode 45000 yang dianggap bentrok; error DB lain dilempar ulang agar tidak
            // tersamarkan sebagai "jadwal bentrok".
            if ((string) $e->getCode() !== '45000') {
                throw $e;
            }

            report($e);

            return back()->withInput()->withErrors([
                'time' => 'Jadwal yang Anda pilih sudah terisi atau bertabrakan dengan reservasi lain yang sedang menunggu konfirmasi/disetujui.',
            ]);
        }

        return redirect()->route('reservations.index')->with('success', 'Reservasi berhasil diajukan dan sedang menunggu verifikasi petugas.');
    }

    // Detail 1 reservasi milik user yang login — termasuk alasan penolakan/
    // pembatalan dan riwayat perubahan status, biar user paham kenapa
    // status reservasinya seperti itu (bukan cuma badge status doang).
    public function show(Request $request, string $reservasi)
    {
        if ($redirect = $this->ensurePengguna()) {
            return $redirect;
        }

        $reservation = Reservation::with([
                'facility',
                'processedBy',
                'logs' => fn ($query) => $query->orderBy('created_at')->with('changedBy'),
            ])
            ->findOrFail($reservasi);

        if ($reservation->id_user !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk melihat reservasi ini.');
        }

        // Permintaan AJAX (pop-up detail): kembalikan potongan isi saja, tanpa layout halaman
        if ($request->ajax()) {
            return view('reservations.partials.detail', ['reservation' => $reservation, 'modal' => true]);
        }

        return view('reservations.show', compact('reservation'));
    }

    // Batalkan reservasi milik sendiri (transaksi + lock + tercatat di log status).
    public function destroy(Request $request, string $reservasi)
    {
        if ($redirect = $this->ensurePengguna()) {
            return $redirect;
        }

        try {
            DB::transaction(function () use ($reservasi) {
                // Lock baris agar tidak berpapasan dengan approve/reject petugas (race condition)
                $reservation = Reservation::whereKey($reservasi)->lockForUpdate()->firstOrFail();

                // 1. Cek kepemilikan (Authorization)
                abort_unless($reservation->id_user === Auth::id(), 403, 'Anda tidak memiliki akses untuk membatalkan reservasi ini.');

                // 2. Hanya reservasi aktif (pending/approved) yang bisa dibatalkan
                if (! in_array($reservation->reservation_status, ['pending', 'approved'], true)) {
                    throw ValidationException::withMessages(['status' => 'Reservasi ini sudah tidak aktif.']);
                }

                // 3. Batas waktu H-1 hanya berlaku untuk reservasi yang SUDAH disetujui.
                //    Reservasi pending (belum diproses petugas) boleh dibatalkan kapan saja
                //    selama kegiatan belum berlalu.
                $tanggal = Carbon::parse($reservation->date->format('Y-m-d'))->startOfDay();

                if ($tanggal->lt(Carbon::today())) {
                    throw ValidationException::withMessages(['status' => 'Reservasi yang sudah lewat tidak dapat dibatalkan.']);
                }

                if ($reservation->reservation_status === 'approved' && Carbon::today()->gte($tanggal)) {
                    throw ValidationException::withMessages([
                        'status' => 'Pembatalan gagal. Reservasi yang sudah disetujui hanya dapat dibatalkan maksimal H-1 sebelum jadwal kegiatan (maksimal pukul 23:59 WIB).',
                    ]);
                }

                $statusSebelum = $reservation->reservation_status;

                $reservation->update([
                    'reservation_status'  => 'cancelled',
                    'cancellation_reason' => 'Dibatalkan oleh pemesan.',
                ]);

                LogStatusReservasi::create([
                    'id_reservasi'  => $reservation->id_reservasi,
                    'status_before' => $statusSebelum,
                    'status_after'  => 'cancelled',
                    'changed_by'    => Auth::id(),
                    'notes'         => 'Dibatalkan oleh pemesan.',
                    'created_at'    => now(),
                ]);
            });
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        }

        return redirect()->route('reservations.index')->with('success', 'Reservasi Anda berhasil dibatalkan.');
    }
}
