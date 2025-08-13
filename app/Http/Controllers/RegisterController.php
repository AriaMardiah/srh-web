<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
class RegisterController extends Controller
{

    public function register(Request $request)
    {
        // Custom validasi manual untuk kontrol respons JSON lebih baik
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phoneNumber' => 'required|string|max:15',
            'email' => 'required|string|email',
            'password' => 'required|string|min:6|confirmed',
            'address' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Cek apakah email sudah ada di database
        if (User::where('email', $request->email)->exists()) {
            return response()->json([
                'message' => 'Email sudah terdaftar',
            ], 409); // 409 = Conflict
        }

        // Buat user
        $user = User::create([
            'name' => $request->name,
            'address' => $request->address,
            'roles' => 'customer',
            'phoneNumber' => $request->phoneNumber,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Kirim email verifikasi
        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Register berhasil, silakan verifikasi email',
            'user' => $user,
        ], 201); // 201 = Created
    }
    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Invalid verification link.'], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email sudah diverifikasi.']);
        }

        $user->markEmailAsVerified();

        return response()->json(['message' => 'Email berhasil diverifikasi.']);
    }
}
