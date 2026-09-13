<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// TODO(Akbar): isi logic asli. Ingat php artisan storage:link buat upload foto.
class ReportController extends Controller
{
    public function index(Request $request)
    {
        return response('TODO(Akbar): riwayat & status laporan milik user login.');
    }

    public function create(Request $request)
    {
        return response('TODO(Akbar): form lapor kerusakan (kategori, deskripsi, foto).');
    }

    public function store(Request $request)
    {
        return response('TODO(Akbar): simpan laporan baru.');
    }

    public function show(Request $request, string $laporan)
    {
        return response("TODO(Akbar): detail status laporan {$laporan}.");
    }
}
