<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// TODO(Ilham): approve/reject/cancel reservasi. Approve wajib cek bentrok
// jadwal (reuse logic milik Zhafran kalau bisa). Tiap ganti status, insert
// baris ke App\Models\LogStatusReservasi.
class ReservationController extends Controller
{
    public function index(Request $request)
    {
        return response('TODO(Ilham): antrian reservasi pending.');
    }

    public function approve(Request $request, string $reservasi)
    {
        return response("TODO(Ilham): approve reservasi {$reservasi}, cek bentrok dulu.");
    }

    public function reject(Request $request, string $reservasi)
    {
        return response("TODO(Ilham): reject reservasi {$reservasi}.");
    }

    public function cancel(Request $request, string $reservasi)
    {
        return response("TODO(Ilham): batalkan reservasi {$reservasi} yang sudah disetujui, wajib alasan.");
    }
}
