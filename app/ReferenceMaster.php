<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;
use DB;

class ReferenceMaster extends Authenticatable {

    use HasFactory,
        Notifiable,
        HasRoles,
        HasApiTokens;

    /**

     * The attributes that are mass assignable.

     *

     * @var array

     */
    protected $table = 'reference_master';
    protected $primaryKey = 'id';



    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    //protected $keyType = 'string';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;


    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */

   

    protected $fillable = [
        'name', 'status','created_by','updated_by','created_at','updated_at'
    ];

    

}
