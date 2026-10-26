<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;
use App\Models\PlaceMaster;



class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
 
protected function redirectTo()
{
    // 🔹 If no place selected yet
    if (!session()->has('place_id')) {

        // 🔹 First ACTIVE place (first created)
        $place = PlaceMaster::where('status', 1)
            ->orderBy('id', 'asc')
            ->first();

        if ($place) {
            session(['place_id' => $place->id]);
        }
    }

    return RouteServiceProvider::HOME;
}


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}