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

    protected $appends = ['photo_url', 'gallery', 'amenities'];

    /**
     * Foto utama fasilitas
     */
    public function getPhotoUrlAttribute(): string
    {
        return $this->gallery[0];
    }

    /**
     * Koleksi galeri foto fasilitas (3 foto resolusi tinggi sesuai jenis fasilitas)
     */
    public function getGalleryAttribute(): array
    {
        $name = strtolower($this->facility_name ?? '');
        $type = strtolower($this->type ?? '');

        // 1. Pemetaan berdasarkan nama fasilitas spesifik (Fasilitas Undip & Kampus)
        if (str_contains($name, 'perpustakaan undip')) {
            return [
                'https://images.unsplash.com/photo-1521587760476-6c12a4b040da?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1541829070764-84a7d30dd3f3?auto=format&fit=crop&w=800&q=80',
            ];
        }

        if (str_contains($name, 'laboratorium terpadu')) {
            return [
                'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1532094349884-543bc11b234d?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1579154204601-01588f351e67?auto=format&fit=crop&w=800&q=80',
            ];
        }

        if (str_contains($name, 'arena olahraga') || str_contains($name, 'stadion')) {
            return [
                'https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?auto=format&fit=crop&w=800&q=80',
            ];
        }

        if (str_contains($name, 'rumah susun') || str_contains($name, 'rusunawa') || str_contains($name, 'asrama')) {
            return [
                'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?auto=format&fit=crop&w=800&q=80',
            ];
        }

        if (str_contains($name, 'masjid')) {
            return [
                'https://images.unsplash.com/photo-1564769625905-50e93615e769?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1584551246679-0daf3d275d0f?auto=format&fit=crop&w=800&q=80',
            ];
        }

        if (str_contains($name, 'gedung undip') || str_contains($name, 'rektorat') || str_contains($name, 'soedarto')) {
            return [
                'https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=800&q=80',
            ];
        }

        if (str_contains($name, 'student center')) {
            return [
                'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=800&q=80',
            ];
        }

        if (str_contains($name, 'taman')) {
            return [
                'https://images.unsplash.com/photo-1519331379826-f10be5486c6f?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1588880331179-bc9b93a8cb5e?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1470240731273-7821a6eeb6bd?auto=format&fit=crop&w=800&q=80',
            ];
        }

        if (str_contains($name, 'serba guna') || str_contains($name, 'gsg')) {
            return [
                'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1587825140708-dfaf72ae4b04?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&w=800&q=80',
            ];
        }

        if (str_contains($name, 'komputer')) {
            return [
                'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=800&q=80',
            ];
        }

        if (str_contains($name, 'jaringan') || str_contains($name, 'iot')) {
            return [
                'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=800&q=80',
            ];
        }

        if (str_contains($name, 'futsal')) {
            return [
                'https://images.unsplash.com/photo-1518091043644-c1d4457512c6?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1529900748604-07564a03e7a6?auto=format&fit=crop&w=800&q=80',
            ];
        }

        if (str_contains($name, 'basket')) {
            return [
                'https://images.unsplash.com/photo-1546519638-68e109498ffc?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1505666287802-931dc83948e9?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1519766304817-4f37bda74a29?auto=format&fit=crop&w=800&q=80',
            ];
        }

        if (str_contains($name, 'badminton') || str_contains($name, 'tangkis')) {
            return [
                'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1613918108466-292b78a8ef95?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1521537634581-0dced2fee2ef?auto=format&fit=crop&w=800&q=80',
            ];
        }

        if (str_contains($name, 'podcast') || str_contains($name, 'multimedia') || str_contains($name, 'studio')) {
            return [
                'https://images.unsplash.com/photo-1590602847861-f357a9332bbc?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1478737270239-2f02b77fc618?auto=format&fit=crop&w=800&q=80',
            ];
        }

        if (str_contains($name, 'senat') || str_contains($name, 'rapat')) {
            return [
                'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1497366811353-6870744d04b2?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1431540015161-0bf868a2d407?auto=format&fit=crop&w=800&q=80',
            ];
        }

        if (str_contains($name, 'coworking') || str_contains($name, 'diskusi')) {
            return [
                'https://images.unsplash.com/photo-1527192491265-7e15c55b1ed2?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1517502884422-41eaead166d4?auto=format&fit=crop&w=800&q=80',
            ];
        }

        if (str_contains($name, 'classroom') || str_contains($name, 'kelas')) {
            return [
                'https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&w=800&q=80',
            ];
        }

        if (str_contains($name, 'auditorium') || str_contains($name, 'aula')) {
            return [
                'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1544928147-79a2dbc1f389?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1517457373958-b7bdd4587205?auto=format&fit=crop&w=800&q=80',
            ];
        }

        // 2. Pemetaan berdasarkan kategori Tipe Fasilitas
        return match ($type) {
            'perpustakaan' => [
                'https://images.unsplash.com/photo-1521587760476-6c12a4b040da?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1541829070764-84a7d30dd3f3?auto=format&fit=crop&w=800&q=80',
            ],
            'laboratorium' => [
                'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1532094349884-543bc11b234d?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1579154204601-01588f351e67?auto=format&fit=crop&w=800&q=80',
            ],
            'olahraga' => [
                'https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?auto=format&fit=crop&w=800&q=80',
            ],
            'ruang rapat' => [
                'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1497366811353-6870744d04b2?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1431540015161-0bf868a2d407?auto=format&fit=crop&w=800&q=80',
            ],
            'ruang seminar' => [
                'https://images.unsplash.com/photo-1517457373958-b7bdd4587205?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1475721027785-f74eccf877e2?auto=format&fit=crop&w=800&q=80',
            ],
            'aula', 'gedung serbaguna', 'gedung pertemuan' => [
                'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1587825140708-dfaf72ae4b04?auto=format&fit=crop&w=800&q=80',
            ],
            'tempat ibadah' => [
                'https://images.unsplash.com/photo-1564769625905-50e93615e769?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1584551246679-0daf3d275d0f?auto=format&fit=crop&w=800&q=80',
            ],
            'area terbuka' => [
                'https://images.unsplash.com/photo-1519331379826-f10be5486c6f?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1588880331179-bc9b93a8cb5e?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1470240731273-7821a6eeb6bd?auto=format&fit=crop&w=800&q=80',
            ],
            default => [
                'https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=800&q=80',
            ],
        };
    }

    /**
     * Amenitas fasilitas yang relevan
     */
    public function getAmenitiesAttribute(): array
    {
        $name = strtolower($this->facility_name ?? '');
        $type = strtolower($this->type ?? '');

        if (str_contains($name, 'perpustakaan') || str_contains($name, 'coworking')) {
            return ['Wi-Fi Cepat', 'AC Dingin', 'Stopkontak', 'Area Baca Senyap'];
        }
        if (str_contains($name, 'komputer') || str_contains($name, 'jaringan') || str_contains($name, 'iot') || str_contains($type, 'laboratorium')) {
            return ['PC High-End', 'LAN Gigabit', 'AC Sentral', 'Proyektor'];
        }
        if (str_contains($type, 'olahraga') || str_contains($name, 'futsal') || str_contains($name, 'basket') || str_contains($name, 'badminton') || str_contains($name, 'arena')) {
            return ['Tribun Penonton', 'Lampu Lapangan', 'Kamar Ganti', 'Toilet Bersih'];
        }
        if (str_contains($type, 'aula') || str_contains($type, 'gedung') || str_contains($name, 'auditorium') || str_contains($name, 'gsg')) {
            return ['Sound System Pro', 'Panggung Utama', 'Mic Wireless', 'Kapasitas Besar'];
        }
        if (str_contains($name, 'podcast') || str_contains($name, 'studio')) {
            return ['Kedap Suara', 'Mic Condenser', 'Kamera 4K', 'Lighting Studio'];
        }
        if (str_contains($name, 'masjid')) {
            return ['Tempat Wudhu Luas', 'AC Masjid', 'Sound System', 'Sajadah Bersih'];
        }
        if (str_contains($name, 'rusunawa') || str_contains($name, 'rumah susun') || str_contains($name, 'asrama')) {
            return ['Kamar Mandi Dalam', 'Dapur Bersama', 'Wi-Fi Kampus', 'Keamanan 24 Jam'];
        }
        if (str_contains($name, 'taman')) {
            return ['Gazebo Teduh', 'Pedestrian', 'Pencahayaan Taman', 'Area Santai'];
        }

        return ['AC', 'Wi-Fi', 'Proyektor', 'Papan Tulis'];
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
