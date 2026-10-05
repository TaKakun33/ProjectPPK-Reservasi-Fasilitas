<?php

namespace App\Console\Commands;

use App\Services\ReservationExpiry;
use Illuminate\Console\Command;

class KedaluwarsakanReservasi extends Command
{
    protected $signature = 'reservasi:kedaluwarsakan';

    protected $description = 'Tolak otomatis reservasi pending yang jadwal mulainya sudah lewat';

    public function handle(): int
    {
        $jumlah = ReservationExpiry::kedaluwarsakan();

        $this->info("{$jumlah} reservasi pending dikedaluwarsakan.");

        return self::SUCCESS;
    }
}
