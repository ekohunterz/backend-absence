<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MyProfileController extends Controller
{
    public function index()
    {
        $profile = Student::query()
            ->where('id', auth()->guard('api')->user()->id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'message' => 'Detail Profil',
            'student' => $profile
        ]);
    }

    public function update(Request $request)
    {
        //set validation
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:students,email,' . auth()->guard('api')->user()->id,
            'password' => 'nullable|min:6|confirmed',
            'avatar_url' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'gender' => 'nullable|in:L,P',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }


        // Ambil profil student yang login
        $profile = Student::query()
            ->where('id', auth()->guard('api')->user()->id)
            ->firstOrFail();

        // Jika ada upload gambar, simpan dan update
        if ($request->hasFile('avatar_url')) {

            // Hapus gambar lama jika ada
            if ($profile->avatar_url) {
                Storage::delete('avatars/' . $profile->avatar_url);
            }

            // Upload gambar baru
            $avatar_url = $request->file('avatar_url');
            $avatar_url->storeAs('avatars', $avatar_url->hashName());

            // Simpan nama file ke database
            $profile->avatar_url = $avatar_url->hashName();
        }

        // Update data profil
        $profile->name = $request->name;
        $profile->email = $request->email;
        $profile->gender = $request->gender;
        $profile->birth_date = $request->birth_date;
        $profile->address = $request->address;
        $profile->phone = $request->phone;


        // Update password jika diberikan
        if ($request->filled('password')) {
            $profile->password = bcrypt($request->password);
        }

        // Simpan perubahan
        $profile->save();

        return response()->json([
            'success' => true,
            'message' => 'Update Profil Berhasil',
            'data' => $profile
        ]);
    }
}
