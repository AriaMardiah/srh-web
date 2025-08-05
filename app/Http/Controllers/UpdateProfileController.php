<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\User;

class UpdateProfileController extends Controller
{
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        // 1. Dapatkan pengguna yang terotentikasi
        $user = Auth::user();

        // 2. Validasi data yang masuk
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            // Pastikan email unik, kecuali untuk pengguna ini sendiri
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            // 'images' adalah nama field dari Flutter
            'images' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Maksimal 2MB
            'password' => 'nullable|string|min:8',
            'address' => 'required|string',
            'phoneNumber' => 'required|string',
        ]);

        // 3. Handle upload gambar profil
        if ($request->hasFile('images')) {
            // Hapus gambar lama jika ada
            if ($user->images) {
                Storage::delete('public/profile_pictures/' . $user->images);
            }

            // Simpan gambar baru dan dapatkan path-nya
            $path = $request->file('images')->store('public/profile_pictures');
            // Simpan hanya nama filenya di database
            $user->images = basename($path);
        }

        // 4. Update field lainnya
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->address = $validatedData['address'];
        $user->phoneNumber = $validatedData['phoneNumber'];

        // Hanya update password jika diisi
        if (!empty($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']);
        }

        // 5. Simpan perubahan
        $user->save();

        // 6. Kembalikan respons JSON dengan data pengguna yang diperbarui
        return response()->json([
            'message' => 'Profil berhasil diperbarui!',
            'data' => $user,
        ], 200);
    }
}
