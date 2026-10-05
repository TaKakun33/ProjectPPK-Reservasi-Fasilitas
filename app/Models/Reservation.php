<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Skeleton model — struktur dasar saja. Logic validasi (jam operasional,
// slot 30 menit, cek bentrok) ditulis di Service/FormRequest terpisah
// (mis. app/Services/ReservationAvailability.php), bukan di sini.
    
#[Fillable(['id_user', 'id_fasilitas', 'date', 'start_time', 'end_time', 'purpose', 'reservation_status', 'cancellation_reason', 'alasan_ditolak', 'processed_by'])]
class Reservation extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'id_reservasi';

    public $incrementing = false;

    protected $keyType = 'string';

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    // withTrashed: nama pemohon tetap tampil di panel petugas walau akunnya sudah dihapus (soft delete)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user')->withTrashed();
    }

    // Reservasi yang belum selesai berlangsung (tanggal besok dst, atau hari ini dan jam selesai belum lewat)
    public function scopeMendatang($query)
    {
        $sekarang = Carbon::now(config('app.timezone', 'Asia/Jakarta'));

        return $query->where(function ($q) use ($sekarang) {
            $q->where('date', '>', $sekarang->toDateString())
              ->orWhere(function ($q2) use ($sekarang) {
                  $q2->where('date', $sekarang->toDateString())
                     ->where('end_time', '>', $sekarang->format('H:i:s'));
              });
        });
    }

    // Reservasi yang sudah selesai berlangsung (tanggal sebelum hari ini, atau hari ini dan jam selesai sudah lewat)
    public function scopeSelesai($query)
    {
        $sekarang = Carbon::now(config('app.timezone', 'Asia/Jakarta'));

        return $query->where(function ($q) use ($sekarang) {
            $q->where('date', '<', $sekarang->toDateString())
              ->orWhere(function ($q2) use ($sekarang) {
                  $q2->where('date', $sekarang->toDateString())
                     ->where('end_time', '<=', $sekarang->format('H:i:s'));
              });
        });
    }

    // Apakah reservasi ini sudah selesai berlangsung
    public function sudahSelesai(): bool
    {
        $zona = config('app.timezone', 'Asia/Jakarta');

        return Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->end_time, $zona)
            ->lte(Carbon::now($zona));
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class, 'id_fasilitas');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by', 'id_user');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(LogStatusReservasi::class, 'id_reservasi');
    }
}
