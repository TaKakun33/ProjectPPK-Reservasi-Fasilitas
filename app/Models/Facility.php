<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Skeleton model — struktur dasar saja (fillable, casts, relasi).
 * Business logic (search/filter, cek ketersediaan slot, dst.) ditulis
 * di controller masing-masing modul, bukan di sini, biar file ini
 * gak jadi rebutan edit banyak orang.
 */
#[Fillable(['facility_name', 'type', 'location', 'capacity', 'description', 'facility_status', 'is_active'])]
class Facility extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $primaryKey = 'id_fasilitas';

    public $incrementing = false;

    protected $keyType = 'string';

    // Laravel default expects updated_at too; migration hanya punya created_at.
    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'capacity' => 'integer',
        ];
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'id_fasilitas');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'id_fasilitas');
    }
}
