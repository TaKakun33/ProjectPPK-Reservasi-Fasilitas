<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Foto fasilitas yang diunggah admin (maksimal 5 per fasilitas; urutan 0 = foto utama)
#[Fillable(['id_fasilitas', 'photo_path', 'urutan'])]
class FacilityPhoto extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'id_foto';

    public $incrementing = false;

    protected $keyType = 'string';

    // Jumlah maksimal foto per fasilitas
    public const MAKS_PER_FASILITAS = 5;

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class, 'id_fasilitas');
    }

    // URL foto lewat route facilities.photo; parameter v mengganti cache browser bila berkasnya berubah
    public function getUrlAttribute(): string
    {
        return route('facilities.photo', $this->id_foto) . '?v=' . substr(md5($this->photo_path), 0, 8);
    }
}
