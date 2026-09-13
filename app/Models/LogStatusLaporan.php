<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Dipakai Ilham (Modul Petugas) tiap kali status laporan berubah
 * (baru/diproses/selesai/ditolak) — insert 1 baris log di sini.
 */
#[Fillable(['id_laporan', 'status_before', 'status_after', 'changed_by', 'notes'])]
class LogStatusLaporan extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'id_log';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class, 'id_laporan');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by', 'id_user');
    }
}
