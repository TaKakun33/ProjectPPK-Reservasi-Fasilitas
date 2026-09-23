<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// (Modul Petugas) tiap kali status laporan berubah
// (baru/diproses/selesai/ditolak)

#[Fillable(['id_laporan', 'status_before', 'status_after', 'changed_by', 'notes'])]
class LogStatusLaporan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'log_status_laporan';

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
