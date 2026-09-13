<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Dipakai Ilham (Modul Petugas) tiap kali status reservasi berubah
 * (approve/reject/cancel) — insert 1 baris log di sini.
 */
#[Fillable(['id_reservasi', 'status_before', 'status_after', 'changed_by', 'notes'])]
class LogStatusReservasi extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'id_log';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class, 'id_reservasi');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by', 'id_user');
    }
}
