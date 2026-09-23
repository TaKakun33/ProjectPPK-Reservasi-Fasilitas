<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

// Model fasilitas kampus dengan dukungan UUID dan soft deletes
#[Fillable(['facility_name', 'type', 'location', 'capacity', 'description', 'facility_status'])]
class Facility extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $primaryKey = 'id_fasilitas';

    public $incrementing = false;

    protected $keyType = 'string';

    // Tabel fasilitas hanya menggunakan created_at (tanpa updated_at)
    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
        ];
    }

    // Scope untuk menampilkan fasilitas yang belum dinonaktifkan (aktif atau dalam perbaikan)
    public function scopeVisible($query)
    {
        return $query->where('facility_status', '!=', 'nonaktif');
    }

    // Cek apakah fasilitas siap digunakan untuk reservasi (berstatus 'aktif')
    public function isReservable(): bool
    {
        return $this->facility_status === 'aktif';
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
