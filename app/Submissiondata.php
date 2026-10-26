<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;
use DB;

class Submissiondata extends Authenticatable {

    use HasFactory,
        Notifiable,
        HasRoles,
        HasApiTokens;

    /**

     * The attributes that are mass assignable.

     *

     * @var array

     */
    protected $table = 'submission_data';
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
        'no_of_players','console_id',
        'hours_id','date','customer_place',
        'start_time','end_time','place_id',
        'player_informations','total_hours',
        'total_cost','discount','fandb_cost','fandb_informations',
        'final_cost','payment_type',
        'reference_id', 'created_by',
        'updated_by','created_at','updated_at','status','filter_mobile_no','filter_name'
    ];
    protected $casts = [
    'status' => 'integer',
];


    public function GetConsoleInfo() {
        return $this->belongsTo('App\ConsoleMaster', 'console_id','id');
    }
    public function GetHoursInfo() {
        return $this->belongsTo('App\HoursMaster', 'hours_id','id');
    }
    public function GetPaymentInfo() {
        return $this->belongsTo('App\PaymentTypeMaster', 'payment_type','id');
    }
    public function GetReferenceInfo() {
        return $this->belongsTo('App\ReferenceMaster', 'reference_id','id');
    }
}