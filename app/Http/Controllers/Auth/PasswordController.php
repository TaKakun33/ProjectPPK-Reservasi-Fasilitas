<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    // Update the user's password
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Putus semua sesi lain milik pengguna ini (SESSION_DRIVER=database): bila password diganti
        // karena akun dicurigai dibobol, sesi penyerang tidak boleh tetap hidup.
        DB::table('sessions')
            ->where('user_id', $request->user()->id_user)
            ->where('id', '!=', $request->session()->getId())
            ->delete();

        return back()->with('status', 'password-updated');
    }
}
