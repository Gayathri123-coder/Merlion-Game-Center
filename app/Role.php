<?php

namespace App;

//use Zizaco\Entrust\EntrustRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Config;

class Role extends Model
{


    /**
     * The connection name for the model.
     *
     * @var string
     */
    //protected $connection = 'connection-name';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ma_group';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'Pkey';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

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

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        //'delayed' => false,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'G_Name', 'G_Code' , 'G_Status', 'NotificationStatus'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        //'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        //'email_verified_at' => 'datetime',
    ];

    public function getNameAttribute()
    {
        return $this->G_Name;
    }

    public function permission(){
        return $this->belongsToMany('App\Permission', 'permission_role', 'role_Pkey', 'permission_Pkey');
    }

    public function mobile_permission(){
        return $this->belongsToMany('App\MenuMobile', 'mobile_role', 'role_Pkey', 'permission_Pkey');
    }

    //Crud
    public function crud(){
        return $this->belongsToMany('App\Crud', 'crud_role_menu', 'role_Pkey', 'crud_Pkey');
    }
    //Crud

    public function getAllMenus($isCode=0,$ParentIDCondition = "="){
        $mainMenu = Menu::where('isCrud',"=",$isCode)
        ->where('ParentID',$ParentIDCondition,NULL)
        ->pluck('MenuName','Pkey')->toArray();
        $menus = Menu::where('isCrud',"=",$isCode)->get();
        foreach ($menus as $key => $value) {
            if($value->ParentID==NULL):
                continue;
            endif;
            if(in_array($value->ParentID,array_keys($mainMenu))):
                $result[$mainMenu[$value->ParentID]][] = $value;
            else:
                $result[$value->MenuName] = [];
            endif;
        }
        return $result;
    }

    public function getAllMobileMenus($isCode=0, $ParentIDCondition = "=") {
        $mainMenu = MenuMobile::where('isCrud',"=",$isCode)
        ->where('ParentID',$ParentIDCondition,NULL)
        ->pluck('MenuName','Pkey')->toArray();
        $menus = MenuMobile::where('isCrud',"=",$isCode)->get();
        $result =array();
        foreach ($menus as $key => $value) {
            if($value->ParentID==NULL):
                continue;
            endif;
            if(in_array($value->ParentID,array_keys($mainMenu))):
                $result[$mainMenu[$value->ParentID]][] = $value;
            else:
              $result[$value->MenuName] = [];
            endif;
        }
        return $result;
    }

    public function getAllCrudMenus() {
        $submenu = $this->permission()->where('ParentID','!=',NULL)->where('isCrud','=',0)->pluck('Pkey')->toArray();
        foreach ($submenu as $key => $value) {
            $result[$value] = $this->permission()->where('ParentID','=',$value)->where('isCrud','=',1)->pluck('crudID')->toArray();
        }
        return $result;
    }

    public function getAllCrudMobileMenus() {
        $submenu = $this->mobile_permission()->where('ParentID','!=',NULL)->where('isCrud','=',0)->pluck('Pkey')->toArray();
        foreach ($submenu as $key => $value) {
            $result[$value] = $this->mobile_permission()->where('ParentID','=',$value)->where('isCrud','=',1)->pluck('crudID')->toArray();
        }
        return $result;
    }

    public function getGStatusAttribute($value)
    {
        return ($value==1) ? "Active" : "Deactive";
    }

    public function getNotificationStatusAttribute($value)
    {
        return ($value ==1) ? "Active" : "Deactive";
    }

    public function genereateGroupCode() {
        $last_id = Role::select('Pkey')->orderBy('Pkey','desc')->first();
        $u_code = $last_id['Pkey']+1000+1;
        return "GR".$u_code;
    }
}
