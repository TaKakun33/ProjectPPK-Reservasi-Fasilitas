<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// TODO(Abhista): rekap okupansi fasilitas & frekuensi kerusakan,
// export CSV/Excel/PDF.
class RekapController extends Controller
{
    public function index(Request $request)
    {
        return response('TODO(Abhista): rekap okupansi & kerusakan per fasilitas/lokasi.');
    }

    public function export(Request $request)
    {
        return response('TODO(Abhista): export rekap (CSV/Excel/PDF).');
    }
}
