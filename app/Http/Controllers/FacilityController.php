<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// TODO(Zhafran): isi logic asli di sini. Method & signature udah dicocokin
// sama routes/reservasi.php, tinggal ganti isinya, jangan ganti nama method.
class FacilityController extends Controller
{
    public function index(Request $request)
    {
        return response('TODO(Zhafran): daftar fasilitas + cari + status ketersediaan per slot.');
    }

    public function show(Request $request, string $fasilitas)
    {
        return response("TODO(Zhafran): detail ketersediaan fasilitas {$fasilitas}.");
    }
}
