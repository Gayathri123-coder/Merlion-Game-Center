<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;
use DB;

class User extends Authenticatable {

    use HasFactory,
        Notifiable,
        HasRoles,
        HasApiTokens;

    /**

     * The attributes that are mass assignable.

     *

     * @var array

     */
    protected $table = 'ma_usercreation';
    protected $primaryKey = 'Pkey';



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

    const CREATED_AT = 'Created_At';
    const UPDATED_AT = 'Modified_At';

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s';

    public function rolepermission($a, $b) {
       $userExist = DB::table('ma_usercreation as a')->leftjoin('role_user as b', 'b.user_Pkey', '=', 'a.Pkey')->where('a.Pkey', '=', $a)->select('b.*')->first();
        $role = 0;
        if($userExist){ 
            $canDothis = DB::table('role_has_permissions as a')
                    ->leftjoin('permissions as b', 'b.id', '=', 'a.permission_id')
                    ->leftjoin('roles as c', 'c.id', '=', 'a.role_id')
                    ->where('b.name', $b)->where('c.name', $userExist->role_Pkey)->select('b.*')->first();
 
            $role = $canDothis ? 1 : 0;
        }
        
        return $role;
    }

    protected $fillable = [
        'U_Code', 'U_Name', 'U_UserID', 'password', 'U_MobileNo', 'Company_Name', 'Sub_Contractor', 'Trade_Code', 'Trade_Name', 'U_Email', 'email', 'U_Status', 'G_Code', 'G_Name', 'password', 'remember_token', 'canResetPassword', 'show_password', 'ope_license', 'certificates', 'ptid', 'daily_checklist', 'pass_type', 'zone', 'sgroup', 'project_id', 'first_reject', 'second_reject', 'admin_reject', 'last_rejectby', 'expiry_at', 'super_admin', 'isapproved', 'profile', 'profilenomask', 'usersign', 'uen',
        'sic_date', 'dob', 'wp_no', 'wrk_issue', 'wrk_expiry', 'nationality', 'race', 'safety_training', 'training_cert', 'safety_expiry',
        'trade_cert_expiry', 'skill_set', 'Address','sub_group'
    ];

    public function role() {
        return $this->belongsTo('App\Role', 'G_Name', 'G_Name');
    }

    public function role_user() {
        return $this->belongsToMany('App\Role', 'role_user', 'user_Pkey', 'role_Pkey');
    }

    public function group() {
        return $this->belongsTo('App\Role', 'G_Name', 'G_Name');
    }

    public function getUStatusAttribute($value) {
        return ($value == 1) ? "Active" : "In Active";
    }

    public function genereateUserCode() {
        $last_id = DB::table('ma_usercreation')->select('Pkey')->orderBy('Pkey', 'desc')->first();
        $u_code = $last_id->Pkey + 1000 + 1;
        return "U" . $u_code;
    }

//    public function hasRole($roles) {
//        $roles = explode('|', $roles);
//        foreach ($roles as $role) {
//            if ($this->role->G_Name == $role) {
//                return true;
//            }
//        }
//        return false;
//    }

//    public function proPrecincts() {
//        return $this->hasMany('App\MpCproBlockMapping', 'CPRO_Name', 'U_Code');
//    }
    
//    public function cssPrecincts() {
//        return $this->hasMany('App\UnitToTechnicianMapping', 'Techician_ID', 'U_Code');
//    }


//    public function ContractorPMPrecincts() {
//        return $this->hasMany('App\ContractorPmToBlockmapping', 'PM_No', 'U_Code');
//    }

//    public function consultantPMPrecincts() {
//        return $this->hasMany('App\ConsultantPmToPrecinctmapping', 'CPRO_Name', 'U_Code');
//    }

}