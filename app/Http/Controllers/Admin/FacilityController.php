<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// TODO(Abhista): CRUD data master fasilitas. Ingat: nonaktifkan pakai
// Facility::is_active (boolean), JANGAN pakai facility_status (itu punya
// Ilham buat status 'dalam perbaikan').
class FacilityController extends Controller
{
    public function index(Request $request)
    {
        return response('TODO(Abhista): daftar fasilitas (admin).');
    }

    public function create(Request $request)
    {
        return response('TODO(Abhista): form tambah fasilitas.');
    }

    public function store(Request $request)
    {
        return response('TODO(Abhista): simpan fasilitas baru.');
    }

    public function edit(Request $request, string $fasilitas)
    {
        return response("TODO(Abhista): form edit fasilitas {$fasilitas}.");
    }

    public function update(Request $request, string $fasilitas)
    {
        return response("TODO(Abhista): update fasilitas {$fasilitas}.");
    }

    public function destroy(Request $request, string $fasilitas)
    {
        return response("TODO(Abhista): nonaktifkan fasilitas {$fasilitas} (toggle is_active, bukan hard delete).");
    }
}
