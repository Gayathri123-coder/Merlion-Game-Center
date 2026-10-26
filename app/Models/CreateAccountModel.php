<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class CreateAccountModel extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'create_account';

    protected $fillable = [
        'fullname',
        'age',
        'mobile_no',
        'location',
        'pincode',   // store plain pincode (if you need to keep it)
        'password',  // store hashed pincode for login
        'profile_image',
        'api_token'
    ];

    protected $hidden = [
        'password',        // never show hashed password
         'api_token'
    ];

    public $timestamps = true;
       // Generate API token
    public function generateToken()
    {
        $this->api_token = bin2hex(random_bytes(30)); // 60-character token
        $this->save();
        return $this->api_token;
    }
}