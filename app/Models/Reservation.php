<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Skeleton model — struktur dasar saja. Logic validasi (jam operasional,
 * slot 30 menit, cek bentrok) ditulis di Service/FormRequest terpisah
 * (mis. app/Services/ReservationAvailability.php), bukan di sini.
 */
#[Fillable(['id_user', 'id_fasilitas', 'date', 'start_time', 'end_time', 'purpose', 'reservation_status', 'cancellation_reason', 'processed_by'])]
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
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
