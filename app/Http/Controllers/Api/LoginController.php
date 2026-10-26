<?php
 
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CreateAccountModel;
use Illuminate\Support\Facades\Hash;
 
class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'mobile_no' => 'required|string',
            'password'  => 'required|string', // actually pincode
        ]);
 
       $user = CreateAccountModel::where('mobile_no', $request->mobile_no)->first();

if (!$user || !Hash::check($request->password, $user->password)) {
    return response()->json(['status' => false, 'message' => 'Invalid credentials'], 401);
}

// Generate custom token
$token = $user->generateToken();

return response()->json([
    'status'  => true,
    'message' => 'Login successful',
    'token'   => $token,
    'user'    => [
        'id'            => $user->id,
        'fullname'      => $user->fullname,
        'mobile_no'     => $user->mobile_no,
        'location'      => $user->location,
        'age'           => $user->age,
        'pincode'       => $user->pincode,
        'profile_image' => $user->profile_image ? url($user->profile_image) : null,
'initials'      => $user->profile_image ? null : $this->getInitials($user->fullname),

    ]
]);

    }
 
    public function logout(Request $request)
    {
        if ($request->user() && $request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }
 
        return response()->json([
            'status' => true,
            'message' => 'Logout successful'
        ]);
    }
 
    // helper for initials
    private function getInitials($fullname)
    {
        $names = preg_split('/\s+/', trim($fullname));
        $initials = '';
        foreach (array_slice($names, 0, 2) as $n) {
            $initials .= strtoupper(substr($n, 0, 1));
        }
        return $initials;
    }
}