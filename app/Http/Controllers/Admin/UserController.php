<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// TODO(Abhista): daftarkan akun petugas/pengguna langsung, dan
// verifikasi/tolak akun hasil registrasi mandiri lewat kolom
// account_status di tabel users (udah ada di migration, default 'pending').
class UserController extends Controller
{
    public function index(Request $request)
    {
        return response('TODO(Abhista): daftar user + yang pending verifikasi.');
    }

    public function store(Request $request)
    {
        return response('TODO(Abhista): daftarkan akun petugas/pengguna langsung.');
    }

    public function verify(Request $request, string $user)
    {
        return response("TODO(Abhista): verifikasi akun user {$user}.");
    }

    public function reject(Request $request, string $user)
    {
        return response("TODO(Abhista): tolak akun user {$user}.");
    }
}
