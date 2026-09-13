<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// TODO(Zhafran): isi logic asli. Jangan lupa validasi server: jam operasional
// 07.00-20.00, slot 30 menit, dan cek bentrok jadwal di store().
class ReservationController extends Controller
{
    public function index(Request $request)
    {
        return response('TODO(Zhafran): riwayat & status reservasi milik user login.');
    }

    public function create(Request $request)
    {
        return response('TODO(Zhafran): form ajukan reservasi.');
    }

    public function store(Request $request)
    {
        return response('TODO(Zhafran): simpan reservasi baru + validasi server.');
    }

    public function destroy(Request $request, string $reservasi)
    {
        return response("TODO(Zhafran): batalkan reservasi {$reservasi} milik sendiri.");
    }
}
