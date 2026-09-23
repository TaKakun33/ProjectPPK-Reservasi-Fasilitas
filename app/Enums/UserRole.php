<?php

namespace App\Enums;

// Enum untuk role pengguna dalam sistem.
// Dipakai supaya penulisan nama role konsisten di seluruh aplikasi
// (menghindari typo/beda penulisan seperti 'user' vs 'pengguna').

enum UserRole: string
{
    case Pengguna = 'pengguna';
    case Petugas = 'petugas';
    case Admin = 'admin';

    // Label yang enak dibaca untuk ditampilkan di UI
    public function label(): string
    {
        return match ($this) {
            self::Pengguna => 'Pengguna',
            self::Petugas => 'Petugas',
            self::Admin => 'Admin',
        };
    }
}
