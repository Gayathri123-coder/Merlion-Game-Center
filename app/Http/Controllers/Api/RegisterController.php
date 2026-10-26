<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CreateAccountModel;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                'fullname'  => 'required|string|max:250',
                'age'       => 'required|string|max:2', 
                'mobile_no' => 'required|string|max:15|unique:create_account,mobile_no',
                'location'  => 'required|string|max:200',
                'pincode'   => 'required|string',
            ]);

            // Create user
            $user = CreateAccountModel::create([
                'fullname'  => $request->fullname,
                'age'       => $request->age,
                'mobile_no' => $request->mobile_no,
                'location'  => $request->location,
                'pincode'   => $request->pincode,
                'password'  => Hash::make($request->pincode),
            ]);

           
        // ✅ Use default profile image
        $defaultImagePath = 'uploads/profile/profile_icon.png';
        $user->profile_image = file_exists(public_path($defaultImagePath))
            ? $defaultImagePath
            : null; // fallback if file missing
        $user->save();

        return response()->json([
            'status'  => true,
            'message' => 'Account created successfully',
            'data'    => [
                'user_id'       => $user->id,
                'fullname'      => $user->fullname,
                'mobile_no'     => $user->mobile_no,
                'profile_image' => $user->profile_image ? url($user->profile_image) : null,
                'initials'      => $this->getInitials($user->fullname),
            ],
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'status' => false,
            'errors' => $e->errors(),
        ], 422);
    }
}
}