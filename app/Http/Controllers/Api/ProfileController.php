<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CreateAccountModel;

class ProfileController extends Controller
{

public function updateProfileImage(Request $request)
{
    $request->validate([
        'mobile_no'     => 'required|string',
        'fullname'      => 'sometimes|string|max:250',
        'age'           => 'sometimes|string|max:2',
        'location'      => 'sometimes|string|max:200',
        'pincode'       => 'sometimes|string|max:6',
        'profile_image' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    $user = CreateAccountModel::where('mobile_no', $request->mobile_no)->first();
    if (!$user) {
        return response()->json(['status' => false, 'message' => 'User not found'], 404);
    }

    // Update basic fields if provided
    if ($request->has('fullname')) $user->fullname = $request->fullname;
    if ($request->has('age')) $user->age = $request->age;
    if ($request->has('location')) $user->location = $request->location;

    // 🔹 Handle pincode update
    if ($request->has('pincode')) {
        $newPincode = $request->pincode;

        // Update only if pincode has changed
        if ($user->pincode !== $newPincode) {
            $user->pincode = $newPincode;
            $user->password = bcrypt($newPincode); // 🔥 auto-update password to new pincode
        }
    }

    // 🔹 Handle profile image upload
    if ($request->hasFile('profile_image')) {
        $file = $request->file('profile_image');
        $filename = $user->mobile_no . '.' . $file->getClientOriginalExtension();

        if (!file_exists(public_path('uploads/profile'))) {
            mkdir(public_path('uploads/profile'), 0755, true);
        }

        $file->move(public_path('uploads/profile'), $filename);
        $user->profile_image = 'uploads/profile/' . $filename;
    }

    $user->save();

    return response()->json([
        'status'  => true,
        'message' => 'Profile updated successfully',
        'user'    => [
            'id'            => $user->id,
            'fullname'      => $user->fullname,
            'age'           => $user->age,
            'mobile_no'     => $user->mobile_no,
            'location'      => $user->location,
            'pincode'       => $user->pincode,
            'profile_image' => $user->profile_image ? url($user->profile_image) : null,
        ],
    ]);
}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}