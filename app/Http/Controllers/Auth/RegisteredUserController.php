<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrasiPenggunaRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    // Display the registration view
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(RegistrasiPenggunaRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => UserRole::Pengguna,
            'account_status' => 'pending',
        ]);

        event(new Registered($user));

        // Auth::login($user); // dihapus/comment — jangan auto-login, tunggu approval admin
        return redirect(route('login'))->with('status', 'Akun berhasil dibuat, menunggu verifikasi admin.');
    }
}
