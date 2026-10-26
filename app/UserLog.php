<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class UserLog extends Model
{

    public $timestamps = false;

    public function setCrudIdAttribute($value)
    {
       $curd_id =Crud::select("Pkey")->where("display_name", $value)->first();
       $this->attributes['crud_id'] = $curd_id->Pkey;
    }

    protected $fillable = [
        'crud_id','action_id','menu','action_data','comments','log_from',
        'user_id','created_at'
    ];

    public function user() {
        return $this->hasOne('App\User', 'Pkey', 'user_id');
    }

    public function getNameAttribute($value)
    {
        return $this->user->U_Name." - ".$this->user->U_Code;
    }

    public function getCreatedAtAttribute($value)
    {
        return (!empty($value))?date(Config::get('constants.VIEW_DATE_TIME_MERIDIEM_FORMAT'),strtotime($value)):"";
    }

    public function getCrudIdAttribute($value)
    {
       $curd_id =Crud::select("display_name")->where("Pkey", $value)->first();
       return (!empty($curd_id)) ? $curd_id->display_name : "";
    }

    public function setActionDataAttribute($value)
    {
        $this->attributes['action_data']= (!empty($value)) ? json_encode($value) : "";
    }

    public function getActionDataAttribute($value)
    {
        return (!empty($value)) ? json_decode($value) : "";
    }
}
