<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Skeleton model — struktur dasar saja. Logic upload/preview foto,
 * dsb. ditulis di controller (Akbar) / Petugas\ReportController (Ilham).
 */
#[Fillable(['id_user', 'id_fasilitas', 'id_kategori', 'description', 'report_status', 'resolution_notes', 'handled_by'])]
class Report extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'id_laporan';

    public $incrementing = false;

    protected $keyType = 'string';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class, 'id_fasilitas');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ReportCategory::class, 'id_kategori');
    }

    public function handledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by', 'id_user');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(LogStatusLaporan::class, 'id_laporan');
    }
    
    public function photos(): HasMany
    {
        return $this->hasMany(ReportPhoto::class, 'id_laporan')->orderBy('urutan');
    }
}
