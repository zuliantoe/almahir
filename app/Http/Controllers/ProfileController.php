<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = Auth::user();
        if ($user) {
            $user->load('pegawai.typePegawai');
        }

        return view('profile.edit', [
            'user' => $user,
            'title' => 'Pengaturan Profil'
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:sys_users,email,' . $user->id],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
        ]);

        // Mencegah error jika mengganti email dan ada conflict di tabel pegawai 
        // yang secara unik mengikat email
        $user->fill([
            'email' => $validated['email'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null; // optional logic
        }

        $user->save();

        if ($user->pegawai) {
            $user->pegawai->update([
                'email' => $validated['email'], // Sinkronasi tabel pegawai jika exist
                'no_hp' => $validated['no_hp'] ?? $user->pegawai->no_hp,
                'alamat' => $validated['alamat'] ?? $user->pegawai->alamat,
            ]);
        }

        return redirect()->route('profile.edit')->with('success', 'Informasi profil berhasil diperbarui.');
    }

    /**
     * Update the user's avatar.
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $user = Auth::user();

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar' => $path]);
        }

        return redirect()->route('profile.edit')->with('success', 'Foto profil berhasil diperbarui.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.edit')->with('success', 'Sandi keamanan berhasil diperbarui.');
    }
}
