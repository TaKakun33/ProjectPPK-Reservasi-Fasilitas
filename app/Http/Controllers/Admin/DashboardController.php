<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// TODO(Abhista): isi resources/views/admin/dashboard.blade.php dengan rekap
// ringkas lintas fasilitas. View-nya udah pakai <x-app-layout> yang sama
// dengan dashboard pengguna biasa (jadi navbar + logout otomatis ikut).
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.dashboard');
    }
}
