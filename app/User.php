<?php

namespace App;

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

//        $userExist = DB::table('users as a')->where('a.id',$a)->first();

//        print_r($userExist);
//        exit;

        $userExist = DB::table('ma_usercreation as a')->where('a.Pkey',$a)->first();
//        $role = 0;



        if($userExist){
//            DB::enableQueryLog();
//            $canDothis = DB::table('role_has_permissions as a')
//                    ->leftjoin('permissions as b', 'b.id', '=', 'a.permission_id')
//                    ->leftjoin('roles as c', 'c.id', '=', 'a.role_id')
//                    ->where('b.name', $b)->select('b.*')->first();
//            $bb = DB::getQueryLog(); // get query logs from cache
//            echo '<pre>';
//            print_r($bb);
//            exit;
//            $role = $canDothis ? 1 : 0;
//            \DB::statement("SET SQL_MODE=''");




              $role = DB::table('ma_usercreation as a')
                        ->leftjoin('ma_group as f', 'f.G_Code', '=', 'a.G_Code')
                        ->leftjoin('roles as c', 'c.name', '=', 'f.Pkey')
                        ->leftjoin('role_has_permissions as d', 'd.role_id', '=', 'c.id')
                        ->leftjoin('permissions as e', 'e.id', '=', 'd.permission_id')
                        ->where('a.Pkey', '=', $a)
                        ->where('e.name', '=', $b)
                        ->count();
        }

        return $role;
    }

    protected $fillable = [
        'U_Code', 'U_Name', 'U_UserID', 'password', 'U_MobileNo', 'Company_Name', 'Sub_Contractor', 'Trade_Code', 'Trade_Name', 'U_Email', 'email', 'U_Status', 'G_Code', 'G_Name', 'password', 'remember_token', 'canResetPassword', 'show_password', 'ope_license', 'certificates', 'ptid', 'daily_checklist', 'pass_type', 'zone', 'sgroup', 'project_id', 'first_reject', 'second_reject', 'admin_reject', 'last_rejectby', 'expiry_at', 'super_admin', 'isapproved', 'profile', 'profilenomask', 'usersign', 'uen',
        'sic_date', 'dob', 'wp_no', 'wrk_issue', 'wrk_expiry', 'nationality', 'race', 'safety_training', 'training_cert', 'safety_expiry',
        'trade_cert_expiry', 'skill_set', 'Address','sub_group','role_max_id','short_name','contractor_ref','U_Phone_no','incharge','contractor_year','settings','tc_editable','logo','website','designation','property'
    ];

    public function role() {
        return $this->belongsTo('App\Role', 'G_Name', 'G_Name');
    }

    public function role_user() {
        return $this->belongsToMany('App\Role', 'role_user', 'user_Pkey', 'role_Pkey');
    }

    public function group() {
        return $this->belongsTo('App\Role', 'G_Code', 'G_Code');
    }
    
    public function trades() {
        return $this->belongsTo('App\TradeModal', 'Trade_Code','id');
    }

    public function getUStatusAttribute($value) {
        return ($value == 1) ? "Active" : "In Active";
    }

    public function genereateUserCode() {
        $last_id = DB::table('ma_usercreation')->select('Pkey')->orderBy('Pkey', 'desc')->first();
        $u_code = $last_id->Pkey + 1000 + 1;
        return "U" . $u_code;
    }
    public static function getCwTermsAndConditions()
    {
        //$termsanconditions=$this->termsAndConditions;
        $user_id=\Auth::user()->Pkey;
        $termsanconditions=RunningNumberCW::first();
        //where('user_id',$user_id)->first();
        return @$termsanconditions->quotation_terms ? unserialize($termsanconditions->quotation_terms):[];
    }
    public function termsAndConditions()
    {
        return $this->belongsTo(RunningNumberCW::class,'user_id');
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