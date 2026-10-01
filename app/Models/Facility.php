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

    // Foto sampul per fasilitas (Pexels stock, tanpa manusia).
    // Aturan: foto harus memperlihatkan barang yang disewakan dalam keadaan
    // kosong (ruangan/peralatan saja). Tanpa kolom DB baru: mapping
    // deterministik dari nama + tipe ke foto Pexels yang deskripsinya
    // eksplisit "empty / vacant / no people".
    public function getPhotoUrlAttribute(): string
    {
        return $this->photoGroup()[0];
    }

    // Kelompok 3 foto sekategori untuk cover + galeri detail.
    private function photoGroup(): array
    {
        $px = fn (int $id) => "https://images.pexels.com/photos/{$id}/pexels-photo-{$id}.jpeg?auto=compress&cs=tinysrgb&w=800";
        $name = strtolower($this->facility_name ?? '');
        $type = strtolower($this->type ?? '');
        $hay = $name.' '.$type;

        // Urutan pencocokan: nama spesifik dulu, baru tipe umum.
        if (str_contains($hay, 'komputer')) {
            return [$px(39178037), $px(18471480), $px(39178044)];
        }
        if (str_contains($hay, 'jaringan') || str_contains($hay, 'iot')) {
            return [$px(18471568), $px(18471529), $px(4508751)];
        }
        if (str_contains($hay, 'seminar')) {
            return [$px(31560351), $px(38816548), $px(36159716)];
        }
        if (str_contains($hay, 'auditorium')) {
            return [$px(31593645), $px(13936294), $px(31560351)];
        }
        if (str_contains($hay, 'serbaguna') || ($type === 'aula')) {
            return [$px(13936294), $px(31593645), $px(31560351)];
        }
        if (str_contains($hay, 'futsal')) {
            return [$px(11301810), $px(28271640), $px(16927366)];
        }
        if (str_contains($hay, 'basket')) {
            return [$px(16927366), $px(11301810), $px(28271640)];
        }
        if (str_contains($hay, 'badminton') || str_contains($hay, 'bulu tangkis')) {
            return [$px(35300321), $px(11301810), $px(16927366)];
        }
        if (str_contains($hay, 'senat') || str_contains($hay, 'rapat')) {
            return [$px(38816548), $px(31560351), $px(36159716)];
        }
        if (str_contains($hay, 'podcast') || str_contains($hay, 'studio') || str_contains($hay, 'multimedia')) {
            return [$px(11901222), $px(7383469), $px(7383471)];
        }
        if (str_contains($hay, 'cowork') || str_contains($hay, 'perpustakaan') || str_contains($hay, 'diskusi')) {
            return [$px(11456020), $px(7244576), $px(36159716)];
        }
        if (str_contains($hay, 'classroom') || str_contains($hay, 'kelas')) {
            return [$px(36159716), $px(18471480), $px(31560351)];
        }

        // Fallback per tipe umum.
        if (str_contains($hay, 'lab')) {
            return [$px(39178037), $px(18471568), $px(4508751)];
        }
        if (str_contains($hay, 'olahraga') || str_contains($hay, 'lapangan') || str_contains($hay, 'sport')) {
            return [$px(11301810), $px(16927366), $px(35300321)];
        }
        if (str_contains($hay, 'aula') || str_contains($hay, 'convention')) {
            return [$px(31593645), $px(13936294), $px(31560351)];
        }

        return [$px(11456020), $px(36159716), $px(31560351)];
    }

    // Galeri 3 foto sekategori untuk halaman detail ala tiket.com.
    public function getGalleryAttribute(): array
    {
        return $this->photoGroup();
    }

    // Amenitas per tipe untuk baris ikon ala tiket.com.
    public function getAmenitiesAttribute(): array
    {
        $type = strtolower($this->type ?? '');

        return match (true) {
            str_contains($type, 'lab') => ['WiFi Cepat', 'AC', 'Proyektor', '40 PC', 'Stopkontak'],
            str_contains($type, 'olahraga') => ['Penerangan', 'Ruang Ganti', 'Tribun', 'Parkir Luas'],
            str_contains($type, 'aula') => ['Sound System', 'Panggung', 'AC Central', 'Kapasitas Besar'],
            str_contains($type, 'seminar') || str_contains($type, 'rapat') => ['Smart TV', 'Video Conference', 'Mic Meja', 'AC'],
            str_contains($type, 'studio') => ['Kedap Suara', 'Mic Shure', 'Kamera 4K', 'Lighting'],
            str_contains($type, 'diskusi') => ['WiFi Cepat', 'Whiteboard', 'Stopkontak', 'Kopi Corner'],
            default => ['WiFi', 'AC', 'Proyektor', 'Papan Tulis'],
        };
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
