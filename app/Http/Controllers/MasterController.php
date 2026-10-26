<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ConsoleMaster;
use App\ReferenceMaster;
use App\PaymentTypeMaster;
use App\NoofPlayerMaster;
use App\HoursMaster;
use App\PriceMaster;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\Events\UserActivity;
use Illuminate\Support\Facades\Event;
use DB;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;



class MasterController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('console.list');
    }
     public function saveconsole(Request $request)
      {
        $placeId = session('place_id');

if (!$placeId) {
    return redirect()->back()->with('failure','Select Place First');
}

        $messages = [
            'console_name.required' => 'Console Name is Required',
             'limited_person.required' => 'Limited Person is Required',
             'limited_person.numeric' => 'Limited Person must be number'
        ];

        $rules =  [];
if ($request->tid != '') {

    $rules = [
        'console_name' => [
            'required',
            'max:50',
            Rule::unique('console_master', 'console_name')
                ->where(function ($query) use ($placeId) {
                    return $query->where('place_id', $placeId);
                })
                ->ignore(decrypt($request->tid))  
        ],
        'limited_person' => 'required|numeric|min:1',
        'console_icon' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
    ];

}

else {

    $rules = [
        'console_name' => [
            'required',
            'max:50',
            Rule::unique('console_master', 'console_name')
                ->where(function ($query) use ($placeId) {
                    return $query->where('place_id', $placeId);
                })
        ],
        'limited_person' => 'required|numeric|min:1'
    ];

}

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()
                            ->back()
                            ->withErrors($validator)
                            ->withInput();
        }
        $iconPath = null;

    if ($request->hasFile('console_icon')) {
        $file = $request->file('console_icon');
        $filename = time() . '_' . $file->getClientOriginalName();

        $destination = public_path('icons');
        if (!File::exists($destination)) {
            File::makeDirectory($destination, 0777, true);
        }

        $file->move($destination, $filename);
        $iconPath = 'icons/' . $filename;
    }
        $fid = ($request->tid != '') ? decrypt($request->tid) : '';
        $trade=new ConsoleMaster();
        if ($fid) {
            try
            {
             DB::beginTransaction();
               //  STEP 1: get existing record
        $trade = ConsoleMaster::find($fid);

        //  STEP 2: REMOVE IMAGE CASE
        if ($request->remove_icon == 1 && $trade && $trade->console_icon) {

            $oldPath = public_path($trade->console_icon);
            if (File::exists($oldPath)) {
                File::delete($oldPath); // file delete
            }

            // DB-la NULL set
            $trade->console_icon = null;
            $trade->save();
        }

             
          $data = ['console_name' => $request->console_name,
                   'place_id' => $placeId,
                   'limited_person' => $request->limited_person, 
                   'updated_by'=>Auth::user()->Pkey];
          if ($iconPath) {
                $data['console_icon'] = $iconPath;
            }
           
            $updateStatus =ConsoleMaster::where('id',$fid)->update($data);
             DB::commit();
            event(new UserActivity(["action_id"=>Auth::user()->U_Code, "crud_id"=>"Edit", "menu"=>"Console", "comments"=>"Console - Edit Console", "action_data"=>$request->all(), "log_from"=>'Web']));
        return redirect()->route('console_master')->with('success', 'Console details Successfully Updated');
            }
            catch (Exception $e) {
               DB::rollback();
                return redirect()->route('console_master')->with('failure', $e->message);
            }
        } else {
            try
            {
            DB::beginTransaction();
            $maxid=ConsoleMaster::max('id');
            $trade->console_name=$request->console_name;
            $trade->place_id = $placeId;
            $trade->limited_person = $request->limited_person;
            $trade->console_icon   = $iconPath;
            $trade->created_by=Auth::user()->Pkey;
            $trade->status='1';
            $trade->save();
              DB::commit();
            event(new UserActivity(["action_id"=>Auth::user()->U_Code, "crud_id"=>"Add", "menu"=>"Console", "comments"=>"Console - Add Console", "action_data"=>$request->all(), "log_from"=>'Web']));
            return redirect()->route('console_master')->with('success', 'New Console details Successfully Created  ');
            }
            catch (Exception $e) {
                DB::rollback();
                return redirect()->route('console_master')->with('failure', $e->message);
            }
        }
    }
    
    public function editconsole(Request $request) {

        $fid = decrypt($request['pid']);
        $data = $newSet = [];
        if ($fid) {
            //DB::enableQueryLog(); $qlog = DB::getQueryLog();
            $featureData = ConsoleMaster::
                    select('*')
                    ->where('id', $fid)
                    ->get();

            if (count($featureData) > 0) {
                $newSet = [
                    'trade' => $featureData[0]['console_name'],
                    'limited_person' => $featureData[0]['limited_person'],
                      'icon' => $featureData[0]['console_icon'],
                    'pid' => encrypt($featureData[0]['id']),
                    'action' => 'Update',
                    'title' => 'Update Console'
                ];
                $data = ['status' => TRUE, 'data' => $newSet];
            } else {
                $data = ['status' => FALSE, 'data' => $newSet];
            }
        } else {
            $data = ['status' => FALSE, 'data' => $newSet];
        }

        echo json_encode($data);
    }
     public function deleteConsole(Request $request) {
        $fid = decrypt($request['pid']);
        $status = decrypt($request['status']);
        $status = ($status == 0) ? '1' : '0';
        $featureData = [];
        if ($fid) {
            $updateStatus =ConsoleMaster::
                        where('id', $fid)
                    ->update(['status' => $status]);
             event(new UserActivity(["action_id"=>Auth::user()->U_Code, "crud_id"=>"Edit", "menu"=>"Console", "comments"=>"Console - Delete Console", "action_data"=>$request->all(), "log_from"=>'Web']));

            if ($updateStatus) {
                $data = ['status' => true, 'message' => 'Console Status Successfully Updated', 'status' => $status];
            } else {
                $data = ['status' => false, 'message' => 'Cannot Update the Status'];
            }
        } else {
            $data = ['status' => false, 'message' => 'No Record Found to update the Status'];
        }
        echo json_encode($data);
    }
 public function masterAjaxConsoleList(Request $request)
{
    //  STEP 0: get selected place from session
    $placeId = session('place_id');

    // if no place selected → empty table
    if (!$placeId) {
        return response()->json([
            "draw" => intval($request->draw),
            "iTotalRecords" => 0,
            "iTotalDisplayRecords" => 0,
            "aaData" => []
        ]);
    }

    ## Read values
    $draw = $request->get('draw');
    $start = $request->get("start");
    $rowperpage = $request->get("length");

    $order_arr = $request->get('order');
    $columns = $request->get('columns');
    $search = $request->get('search')['value'];

    $columnIndex = $order_arr[0]['column'];
    $columnName = $columns[$columnIndex]['data'];
    $columnSortOrder = $order_arr[0]['dir'];

    //  BASE QUERY (place wise)
    $baseQuery = ConsoleMaster::where('place_id', $placeId);

    // TOTAL RECORDS (place wise)
    $totalRecords = $baseQuery->count();

    //  FILTERED RECORDS
    $filteredQuery = ConsoleMaster::where('place_id', $placeId);

    if ($search) {
        $filteredQuery->where('console_name', 'LIKE', "%{$search}%");
    }

    $totalRecordswithFilter = $filteredQuery->count();

    //  FETCH DATA
    $records = $filteredQuery
        ->orderBy($columnName, $columnSortOrder)
        ->skip($start)
        ->take($rowperpage)
        ->get();

    $data_arr = [];

    foreach ($records as $value) {

        $status = $value->status == 1
            ? '<span class="label label-success">Active</span>'
            : '<span class="label label-danger">Inactive</span>';

        $icon = $value->status == 1 ? 'fa-thumbs-down' : 'fa-thumbs-up';

        $action = '
            <div class="hidden-sm hidden-xs action-buttons">
                <span class="edit-feature" data-action="edit" data-fid="' . encrypt($value->id) . '" style="cursor:pointer;">
                    <i class="fa fa-edit"></i>
                </span>
                &nbsp;
                <span class="change-status" data-action="status"
                      data-status="' . encrypt($value->status) . '"
                      data-fid="' . encrypt($value->id) . '" style="cursor:pointer;">
                    <i class="fa ' . $icon . '"></i>
                </span>
            </div>';

        $data_arr[] = [
            "console_name"   => $value->console_name,
            "limited_person" => $value->limited_person,
            "status"         => $status,
            "action"         => $action,
        ];
    }

    return response()->json([
        "draw" => intval($draw),
        "iTotalRecords" => $totalRecords,
        "iTotalDisplayRecords" => $totalRecordswithFilter,
        "aaData" => $data_arr
    ]);
}

    public function Referencemaster()
    {
        return view('reference_master.list');
    }
     public function savereference(Request $request) {
        $messages = [
            'name.required' => 'Console Name is Required'
        ];

        $rules =  [];

         if ($request->tid != '') {
         $rules['name'] = 'required|max:50|unique:reference_master,name,'.decrypt($request->tid);
        }
        else
        {
         $rules['name'] = 'required|max:50|unique:reference_master,name';

        }
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()
                            ->back()
                            ->withErrors($validator)
                            ->withInput();
        }
        $fid = ($request->tid != '') ? decrypt($request->tid) : '';
        $trade=new ReferenceMaster();
        if ($fid) {
            try
            {
             DB::beginTransaction();
             
          $data = ['name' => $request->name,'updated_by'=>Auth::user()->Pkey];
           
            $updateStatus =ReferenceMaster::where('id',$fid)->update($data);
             DB::commit();
            event(new UserActivity(["action_id"=>Auth::user()->U_Code, "crud_id"=>"Edit", "menu"=>"Reference", "comments"=>"Reference - Edit Reference", "action_data"=>$request->all(), "log_from"=>'Web']));
        return redirect()->route('reference_master')->with('success', 'Reference details Successfully Updated');
            }
            catch (Exception $e) {
               DB::rollback();
                return redirect()->route('reference_master')->with('failure', $e->message);
            }
        } else {
            try
            {
            DB::beginTransaction();
            $maxid=ReferenceMaster::max('id');
            $trade->name=$request->name;
            $trade->created_by=Auth::user()->Pkey;
            $trade->status='1';
            $trade->save();
              DB::commit();
            event(new UserActivity(["action_id"=>Auth::user()->U_Code, "crud_id"=>"Add", "menu"=>"Reference", "comments"=>"Reference - Add Reference", "action_data"=>$request->all(), "log_from"=>'Web']));
            return redirect()->route('reference_master')->with('success', 'New Reference details Successfully Created  ');
            }
            catch (Exception $e) {
                DB::rollback();
                return redirect()->route('reference_master')->with('failure', $e->message);
            }
        }
    }
    
    public function editreference(Request $request) {

        $fid = decrypt($request['pid']);
        $data = $newSet = [];
        if ($fid) {
            //DB::enableQueryLog(); $qlog = DB::getQueryLog();
            $featureData = ReferenceMaster::
                    select('*')
                    ->where('id', $fid)
                    ->get();

            if (count($featureData) > 0) {
                $newSet = [
                    'name' => $featureData[0]['name'],
                    'pid' => encrypt($featureData[0]['id']),
                    'action' => 'Update',
                    'title' => 'Update Reference'
                ];
                $data = ['status' => TRUE, 'data' => $newSet];
            } else {
                $data = ['status' => FALSE, 'data' => $newSet];
            }
        } else {
            $data = ['status' => FALSE, 'data' => $newSet];
        }

        echo json_encode($data);
    }
     public function deletereference(Request $request) {
        $fid = decrypt($request['pid']);
        $status = decrypt($request['status']);
        $status = ($status == 0) ? '1' : '0';
        $featureData = [];
        if ($fid) {
            $updateStatus =ReferenceMaster::
                        where('id', $fid)
                    ->update(['status' => $status]);
             event(new UserActivity(["action_id"=>Auth::user()->U_Code, "crud_id"=>"Edit", "menu"=>"Reference", "comments"=>"Reference - Delete Reference", "action_data"=>$request->all(), "log_from"=>'Web']));

            if ($updateStatus) {
                $data = ['status' => true, 'message' => 'Reference Status Successfully Updated', 'status' => $status];
            } else {
                $data = ['status' => false, 'message' => 'Cannot Update the Status'];
            }
        } else {
            $data = ['status' => false, 'message' => 'No Record Found to update the Status'];
        }
        echo json_encode($data);
    }
  public function masterAjaxreferenceList(Request $request){
         ## Read value
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $searchValue = $request->get('search');
        $searchValue = $searchValue['value'];
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        // Total records
        $totalRecords = ReferenceMaster::select('count(*) as allcount');
         if($searchValue!=NULL && $searchValue!='')
        {
            $totalRecords =$totalRecords->where('name', 'LIKE', '%'.$searchValue.'%');

        }
        $totalRecords =$totalRecords->count();
        $totalRecordswithFilter = ReferenceMaster::select('count(*) as allcount');
        if($searchValue!=NULL && $searchValue!='')
        {
            $totalRecordswithFilter =$totalRecordswithFilter->where('name', 'LIKE', '%'.$searchValue.'%');

        }
         $totalRecordswithFilter =$totalRecordswithFilter->count();

        // Fetch totalRecords
        $records = ReferenceMaster::orderBy($columnName,$columnSortOrder);
        if($searchValue!=NULL && $searchValue!='')
             {
              $records =$records->where('name', 'LIKE', '%'.$searchValue.'%');
             }
              
             $records =$records->select('*')
              ->skip($start)
              ->take($rowperpage)
              ->get();

        $data_arr = array();

        foreach($records as  $value){
           $name = $value->name;
           $status = '';
            if($value->status=='1')
            {
           $status ='<span class="label label-success">Active</span>';
           $icon="fa-thumbs-down";
            }
            else
            {
          $status='<span class="label label-danger">Inactive</span>';
            $icon="fa-thumbs-up";
             }
             $action='';
            
            $action .= '<div class="hidden-sm hidden-xs action-buttons"><span class="edit-feature"  data-action="edit" data-fid="' . encrypt($value->id) . '" edit-feature" data-action="edit" data-fid="' . encrypt($value->id) . '"  style="cursor:pointer;" title="Edit"><i class="fa fa-edit" title="Edit"></i></span>&nbsp;<span class="red ace-icon  bigger-130 change-status" id="status_' . encrypt($value->id) . '"data-status="' . encrypt($value->status) . '" data-fid="' . encrypt($value->id) . '" data-action="status" style="cursor:pointer;" title="Update Status"><i class="fa ' . $icon . '"></i></span></div>';
           

           $data_arr[] = array(
               "name" => $name,
               "status" => $status,
               "action"=>$action,
           );
        }
        $response = array(
           "draw" => intval($draw),
           "iTotalRecords" => $totalRecords,
           "iTotalDisplayRecords" => $totalRecordswithFilter,
           "aaData" => $data_arr
        );

        return response()->json($response); 
    }

    //no of players start

    public function Noofplayers()
    {
    	return view('noofplayers.list');
    }
    public function masterAjaxplayersList(Request $request){
         ## Read value
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $searchValue = $request->get('search');
        $searchValue = $searchValue['value'];
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        // Total records
        $totalRecords = NoofPlayerMaster::select('count(*) as allcount');
         if($searchValue!=NULL && $searchValue!='')
        {
            $totalRecords =$totalRecords->where('player_number', 'LIKE', '%'.$searchValue.'%');

        }
        $totalRecords =$totalRecords->count();
        $totalRecordswithFilter = NoofPlayerMaster::select('count(*) as allcount');
        if($searchValue!=NULL && $searchValue!='')
        {
            $totalRecordswithFilter =$totalRecordswithFilter->where('player_number', 'LIKE', '%'.$searchValue.'%');

        }
         $totalRecordswithFilter =$totalRecordswithFilter->count();

        // Fetch records
        $records = NoofPlayerMaster::orderBy($columnName,$columnSortOrder);
         
               if($searchValue!=NULL && $searchValue!='')
        {
            $records =$records->where('player_number', 'LIKE', '%'.$searchValue.'%');

        }
              $records =$records ->select('*')
              ->skip($start)
              ->take($rowperpage)
              ->get();

        $data_arr = array();

        foreach($records as $key=>$value){
           $sno =$key+1;
           $player_number=$value->player_number;
          
             $action='';
            $action .= '<div class="hidden-sm hidden-xs action-buttons"><span class="edit-feature"  data-action="edit" data-fid="' . encrypt($value->id) . '" data-action="edit" data-fid="' . encrypt($value->id) . '"  style="cursor:pointer;" title="Edit"><i class="fa fa-edit" title="Edit"></i></span></div>';           
           

           $data_arr[] = array(
               "id" => $sno,
               "player_number" => $player_number,
               "action"=>$action,
           );
        }
        $response = array(
           "draw" => intval($draw),
           "iTotalRecords" => $totalRecords,
           "iTotalDisplayRecords" => $totalRecordswithFilter,
           "aaData" => $data_arr
        );

        return response()->json($response); 
    }
    public function SavePlayers(Request $request)
    {
    	 $messages = [
            'player_number.required' => 'Player Numbers is Required',
            
        ];

        $rules =  [
                        'player_number' => 'required',

                 ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()
                            ->back()
                            ->withErrors($validator)
                            ->withInput();
        }
        $fid = ($request->tid != '') ? decrypt($request->tid) : '';
        if ($fid) {
            try
            {
             DB::beginTransaction();
             
            $data = ['player_number' => $request->player_number,'updated_by'=>Auth::user()->Pkey];
            $updateStatus =NoofPlayerMaster::where('id',$fid)->update($data);
            DB::commit();
            event(new UserActivity(["action_id"=>Auth::user()->U_Code, "crud_id"=>"Edit", "menu"=>"No of player Master", "comments"=>"No of player - Edit No of player", "action_data"=>$request->all(), "log_from"=>'Web']));
            
                return redirect()->route('players_list')->with('success', 'Player details Successfully Updated');
            
            }
            catch (Exception $e) {
               DB::rollback();
                return redirect()->route('players_list')->with('failure', $e->message);
            }
        } else {
        	return redirect()->route('players_list')->with('failure', $e->message);
            
    }
}
     public function editPlayers(Request $request) {
        $fid = decrypt($request['pid']);
        $data = $newSet = [];
        if ($fid) {
            //DB::enableQueryLog(); $qlog = DB::getQueryLog();
            $featureData = NoofPlayerMaster::
                    select('*')
                    ->where('id', $fid)
                    ->get();

            if (count($featureData) > 0) {
                $newSet = [
                    'player_number' => $featureData[0]['player_number'],
                    'pid' => encrypt($featureData[0]['id']),
                    'action' => 'Update',
                    'title' => 'Update No of Players'
                ];
                $data = ['status' => TRUE, 'data' => $newSet];
            } else {
                $data = ['status' => FALSE, 'data' => $newSet];
            }
        } else {
            $data = ['status' => FALSE, 'data' => $newSet];
        }

        echo json_encode($data);
    }

    //start hours master

    public function Hoursmaster()
    {
        return view('hours_master.list');
    }
     public function savehours(Request $request) {
        $messages = [
            'hours.required' => 'hours is Required'
        ];

        $rules =  [];

         if ($request->tid != '') {
         $rules['hours'] = 'required|max:50|unique:hours_master,hours,'.decrypt($request->tid);
        }
        else
        {
         $rules['hours'] = 'required|max:50|unique:hours_master,hours';

        }
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()
                            ->back()
                            ->withErrors($validator)
                            ->withInput();
        }
        $fid = ($request->tid != '') ? decrypt($request->tid) : '';
        $trade=new HoursMaster();
        if ($fid) {
            try
            {
             DB::beginTransaction();
             
          $data = ['hours' => $request->hours,'updated_by'=>Auth::user()->Pkey];
           
            $updateStatus =HoursMaster::where('id',$fid)->update($data);
             DB::commit();
            event(new UserActivity(["action_id"=>Auth::user()->U_Code, "crud_id"=>"Edit", "menu"=>"Hours", "comments"=>"Hours - Edit Hours", "action_data"=>$request->all(), "log_from"=>'Web']));
        return redirect()->route('Hours_master')->with('success', 'Hours details Successfully Updated');
            }
            catch (Exception $e) {
               DB::rollback();
                return redirect()->route('Hours_master')->with('failure', $e->message);
            }
        } else {
            try
            {
            DB::beginTransaction();
            $maxid=HoursMaster::max('id');
            $trade->hours=$request->hours;
            $trade->created_by=Auth::user()->Pkey;
            $trade->status='1';
            $trade->save();
              DB::commit();
            event(new UserActivity(["action_id"=>Auth::user()->U_Code, "crud_id"=>"Add", "menu"=>"Hours", "comments"=>"RefeHoursrence - Add Hours", "action_data"=>$request->all(), "log_from"=>'Web']));
            return redirect()->route('Hours_master')->with('success', 'New Hours details Successfully Created  ');
            }
            catch (Exception $e) {
                DB::rollback();
                return redirect()->route('Hours_master')->with('failure', $e->message);
            }
        }
    }
    
    public function edithours(Request $request) {

        $fid = decrypt($request['pid']);
        $data = $newSet = [];
        if ($fid) {
            //DB::enableQueryLog(); $qlog = DB::getQueryLog();
            $featureData = HoursMaster::
                    select('*')
                    ->where('id', $fid)
                    ->get();

            if (count($featureData) > 0) {
                $newSet = [
                    'hours' => $featureData[0]['hours'],
                    'pid' => encrypt($featureData[0]['id']),
                    'action' => 'Update',
                    'title' => 'Update Hours'
                ];
                $data = ['status' => TRUE, 'data' => $newSet];
            } else {
                $data = ['status' => FALSE, 'data' => $newSet];
            }
        } else {
            $data = ['status' => FALSE, 'data' => $newSet];
        }

        echo json_encode($data);
    }
     public function deletehours(Request $request) {
        $fid = decrypt($request['pid']);
        $status = decrypt($request['status']);
        $status = ($status == 0) ? '1' : '0';
        $featureData = [];
        if ($fid) {
            $updateStatus =HoursMaster::
                        where('id', $fid)
                    ->update(['status' => $status]);
             event(new UserActivity(["action_id"=>Auth::user()->U_Code, "crud_id"=>"Edit", "menu"=>"Hours", "comments"=>"Hours - Delete Hours", "action_data"=>$request->all(), "log_from"=>'Web']));

            if ($updateStatus) {
                $data = ['status' => true, 'message' => 'Hours Status Successfully Updated', 'status' => $status];
            } else {
                $data = ['status' => false, 'message' => 'Cannot Update the Status'];
            }
        } else {
            $data = ['status' => false, 'message' => 'No Record Found to update the Status'];
        }
        echo json_encode($data);
    }
  public function masterAjaxhoursList(Request $request){
         ## Read value
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $searchValue = $request->get('search');
        $searchValue = $searchValue['value'];
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        // Total records
        $totalRecords = HoursMaster::select('count(*) as allcount');
        if($searchValue!=NULL && $searchValue!='')
        {
            $totalRecords =$totalRecords->where('hours', 'LIKE', '%'.$searchValue.'%');

        }
         $totalRecords =$totalRecords->count();
        $totalRecordswithFilter = HoursMaster::select('count(*) as allcount');
        if($searchValue!=NULL && $searchValue!='')
        {
            $totalRecordswithFilter =$totalRecordswithFilter->where('hours', 'LIKE', '%'.$searchValue.'%');

        }
         $totalRecordswithFilter =$totalRecordswithFilter->count();

        // Fetch totalRecords
        $records = HoursMaster::orderBy($columnName,$columnSortOrder);
        if($searchValue!=NULL && $searchValue!='')
             {
              $records =$records->where('hours', 'LIKE', '%'.$searchValue.'%');
             }
              
             $records =$records->select('*')
              ->skip($start)
              ->take($rowperpage)
              ->get();

        $data_arr = array();

        foreach($records as  $value){
           $Hours = $value->hours;
           $status = '';
            if($value->status=='1')
            {
           $status ='<span class="label label-success">Active</span>';
           $icon="fa-thumbs-down";
            }
            else
            {
          $status='<span class="label label-danger">Inactive</span>';
            $icon="fa-thumbs-up";
             }
             $action='';
            
            $action .= '<div class="hidden-sm hidden-xs action-buttons"><span class="edit-feature"  data-action="edit" data-fid="' . encrypt($value->id) . '" edit-feature" data-action="edit" data-fid="' . encrypt($value->id) . '"  style="cursor:pointer;" title="Edit"><i class="fa fa-edit" title="Edit"></i></span>&nbsp;<span class="red ace-icon  bigger-130 change-status" id="status_' . encrypt($value->id) . '"data-status="' . encrypt($value->status) . '" data-fid="' . encrypt($value->id) . '" data-action="status" style="cursor:pointer;" title="Update Status"><i class="fa ' . $icon . '"></i></span></div>';
           

           $data_arr[] = array(
               "hours" => $Hours,
               "status" => $status,
               "action"=>$action,
           );
        }
        $response = array(
           "draw" => intval($draw),
           "iTotalRecords" => $totalRecords,
           "iTotalDisplayRecords" => $totalRecordswithFilter,
           "aaData" => $data_arr
        );

        return response()->json($response); 
    }

    //end hours master

    //start payment type master

    public function paymenttypemaster()
    {
        return view('paymenttype.list');
    }
     public function savepaymenttype(Request $request) {
        $messages = [
            'payment_type.required' => 'Payment Type is Required'
        ];

        $rules =  [];

         if ($request->tid != '') {
         $rules['payment_type'] = 'required|max:50|unique:payment_type_master,payment_type,'.decrypt($request->tid);
        }
        else
        {
         $rules['payment_type'] = 'required|max:50|unique:payment_type_master,payment_type';

        }
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()
                            ->back()
                            ->withErrors($validator)
                            ->withInput();
        }
        $fid = ($request->tid != '') ? decrypt($request->tid) : '';
        $trade=new PaymentTypeMaster();
        if ($fid) {
            try
            {
             DB::beginTransaction();
             
          $data = ['payment_type' => $request->payment_type,'updated_by'=>Auth::user()->Pkey];
           
            $updateStatus =PaymentTypeMaster::where('id',$fid)->update($data);
             DB::commit();
            event(new UserActivity(["action_id"=>Auth::user()->U_Code, "crud_id"=>"Edit", "menu"=>"Payment type", "comments"=>"Payment type - Edit Payment type", "action_data"=>$request->all(), "log_from"=>'Web']));
        return redirect()->route('payment_type_master')->with('success', 'Payment type details Successfully Updated');
            }
            catch (Exception $e) {
               DB::rollback();
                return redirect()->route('payment_type_master')->with('failure', $e->message);
            }
        } else {
            try
            {
            DB::beginTransaction();
            $maxid=PaymentTypeMaster::max('id');
            $trade->payment_type=$request->payment_type;
            $trade->created_by=Auth::user()->Pkey;
            $trade->status='1';
            $trade->save();
              DB::commit();
            event(new UserActivity(["action_id"=>Auth::user()->U_Code, "crud_id"=>"Add", "menu"=>"Payment type", "comments"=>"Payment type - Add Payment type", "action_data"=>$request->all(), "log_from"=>'Web']));
            return redirect()->route('payment_type_master')->with('success', 'New Payment type details Successfully Created  ');
            }
            catch (Exception $e) {
                DB::rollback();
                return redirect()->route('payment_type_master')->with('failure', $e->message);
            }
        }
    }
    
    public function editpaymenttype(Request $request) {

        $fid = decrypt($request['pid']);
        $data = $newSet = [];
        if ($fid) {
            //DB::enableQueryLog(); $qlog = DB::getQueryLog();
            $featureData = PaymentTypeMaster::
                    select('*')
                    ->where('id', $fid)
                    ->get();

            if (count($featureData) > 0) {
                $newSet = [
                    'payment_type' => $featureData[0]['payment_type'],
                    'pid' => encrypt($featureData[0]['id']),
                    'action' => 'Update',
                    'title' => 'Update Payment Type'
                ];
                $data = ['status' => TRUE, 'data' => $newSet];
            } else {
                $data = ['status' => FALSE, 'data' => $newSet];
            }
        } else {
            $data = ['status' => FALSE, 'data' => $newSet];
        }

        echo json_encode($data);
    }
     public function deletepaymenttype(Request $request) {
        $fid = decrypt($request['pid']);
        $status = decrypt($request['status']);
        $status = ($status == 0) ? '1' : '0';
        $featureData = [];
        if ($fid) {
            $updateStatus =PaymentTypeMaster::
                        where('id', $fid)
                    ->update(['status' => $status]);
             event(new UserActivity(["action_id"=>Auth::user()->U_Code, "crud_id"=>"Edit", "menu"=>"Payment Type", "comments"=>"Payment Type - Delete Payment Type", "action_data"=>$request->all(), "log_from"=>'Web']));

            if ($updateStatus) {
                $data = ['status' => true, 'message' => 'Payment Type Status Successfully Updated', 'status' => $status];
            } else {
                $data = ['status' => false, 'message' => 'Cannot Update the Status'];
            }
        } else {
            $data = ['status' => false, 'message' => 'No Record Found to update the Status'];
        }
        echo json_encode($data);
    }
  public function masterAjaxpaymenttypeList(Request $request){
         ## Read value
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $searchValue = $request->get('search');
        $searchValue = $searchValue['value'];
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        // Total records
        $totalRecords = PaymentTypeMaster::select('count(*) as allcount');
        if($searchValue!=NULL && $searchValue!='')
        {
            $totalRecords =$totalRecords->where('payment_type', 'LIKE', '%'.$searchValue.'%');

        }
         $totalRecords =$totalRecords->count();
        $totalRecordswithFilter = PaymentTypeMaster::select('count(*) as allcount');
        if($searchValue!=NULL && $searchValue!='')
        {
            $totalRecordswithFilter =$totalRecordswithFilter->where('payment_type', 'LIKE', '%'.$searchValue.'%');

        }
         $totalRecordswithFilter =$totalRecordswithFilter->count();

        // Fetch totalRecords
        $records = PaymentTypeMaster::orderBy($columnName,$columnSortOrder);
        if($searchValue!=NULL && $searchValue!='')
             {
            $records =$records->where('payment_type', 'LIKE', '%'.$searchValue.'%');
             }
              
             $records =$records->select('*')
              ->skip($start)
              ->take($rowperpage)
              ->get();

        $data_arr = array();

        foreach($records as  $value){
           $payment_type = $value->payment_type;
           $status = '';
            if($value->status=='1')
            {
           $status ='<span class="label label-success">Active</span>';
           $icon="fa-thumbs-down";
            }
            else
            {
          $status='<span class="label label-danger">Inactive</span>';
            $icon="fa-thumbs-up";
             }
             $action='';
            
            $action .= '<div class="hidden-sm hidden-xs action-buttons"><span class="edit-feature"  data-action="edit" data-fid="' . encrypt($value->id) . '" edit-feature" data-action="edit" data-fid="' . encrypt($value->id) . '"  style="cursor:pointer;" title="Edit"><i class="fa fa-edit" title="Edit"></i></span>&nbsp;<span class="red ace-icon  bigger-130 change-status" id="status_' . encrypt($value->id) . '"data-status="' . encrypt($value->status) . '" data-fid="' . encrypt($value->id) . '" data-action="status" style="cursor:pointer;" title="Update Status"><i class="fa ' . $icon . '"></i></span></div>';
           

           $data_arr[] = array(
               "payment_type" => $payment_type,
               "status" => $status,
               "action"=>$action,
           );
        }
        $response = array(
           "draw" => intval($draw),
           "iTotalRecords" => $totalRecords,
           "iTotalDisplayRecords" => $totalRecordswithFilter,
           "aaData" => $data_arr
        );

        return response()->json($response); 
    }

    //end payment type master


    //start price master

    public function priceMaster(Request $request){
       $placeId = session('place_id');

$this->data['console_list'] = ConsoleMaster::where('status','1')
    ->where('place_id', $placeId)
    ->get();

        $this->data['hours_list'] = HoursMaster::where('status','1')->get();
       // $input = NoofPlayerMaster::first();
      //  $this->data['no_of_players_list'] = range(1, $input->player_number);

        return view('price_master.list',$this->data);
    }


    public function masterAjaxprice(Request $request){
        ## Read value
          $placeId = session('place_id');

       $draw = $request->get('draw');
       $start = $request->get("start");
       $rowperpage = $request->get("length"); // Rows display per page

       $columnIndex_arr = $request->get('order');
       $columnName_arr = $request->get('columns');
       $order_arr = $request->get('order');
       $searchValue = $request->get('search');
       $searchValue = $searchValue['value'];
       $columnIndex = $columnIndex_arr[0]['column']; // Column index
       $columnName = $columnName_arr[$columnIndex]['data']; // Column name
       $columnName = 'id';
       $columnSortOrder = $order_arr[0]['dir']; // asc or desc
       // Total records
       $totalRecords = PriceMaster::select('count(*) as allcount')
                       ->leftjoin('console_master','console_master.id','price_master.console_id')
                       ->leftjoin('hours_master','hours_master.id','price_master.hours_id')
                        ->where('console_master.place_id', $placeId);
      
       if($searchValue){
              $totalRecords = $totalRecords->where(function($q) use($searchValue) {
                $q ->where('price_master.price', 'like', '%' . $searchValue . '%')
                ->orwhere('console_master.console_name', 'like', '%' . $searchValue . '%')
               ->orwhere('hours_master.hours', 'like', '%' . $searchValue . '%');
                });
            }
       $totalRecords =$totalRecords->count();
       $totalRecordswithFilter = PriceMaster::select('count(*) as allcount')
                       ->leftjoin('console_master','console_master.id','price_master.console_id')
                       ->leftjoin('hours_master','hours_master.id','price_master.hours_id')
                        ->where('console_master.place_id', $placeId);
        if($searchValue){
              $totalRecordswithFilter = $totalRecordswithFilter->where(function($q) use($searchValue) {
                $q ->where('price_master.price', 'like', '%' . $searchValue . '%')
                ->orwhere('console_master.console_name', 'like', '%' . $searchValue . '%')
               ->orwhere('hours_master.hours', 'like', '%' . $searchValue . '%');
                });
            }
        $totalRecordswithFilter =$totalRecordswithFilter->count();

       // Fetch totalRecords
       $records = PriceMaster::orderBy('price_master.id','DESC')
                      ->leftjoin('console_master','console_master.id','price_master.console_id')
                       ->leftjoin('hours_master','hours_master.id','price_master.hours_id')
                        ->where('console_master.place_id', $placeId);
        if($searchValue){
              $records = $records->where(function($q) use($searchValue) {
                $q ->where('price_master.price', 'like', '%' . $searchValue . '%')
                ->orwhere('console_master.console_name', 'like', '%' . $searchValue . '%')
               ->orwhere('hours_master.hours', 'like', '%' . $searchValue . '%');
                });
            }
            $records =$records->select('*','price_master.id as id','price_master.status as status')
             ->skip($start)
             ->take($rowperpage)
             ->get();

       $data_arr = array();

       foreach($records as  $value){
          $console_name = $value->GetConsoleInfo->console_name;
          $hours = $value->GetHoursInfo->hours;
          $players = $value->no_of_players ==1 ? 'Player' : 'Players';
          $players = $value->no_of_players.'-'.$players;
          $price = $value->price;
          $status = '';
           if($value->status=='1')
           {
          $status ='<span class="label label-success">Active</span>';
          $icon="fa-thumbs-down";
           }
           else
           {
         $status='<span class="label label-danger">Inactive</span>';
           $icon="fa-thumbs-up";
            }
            $action='';
           
           $action .= '<div class="hidden-sm hidden-xs action-buttons"><span class="edit-feature"  data-action="edit" data-fid="' . encrypt($value->id) . '" edit-feature" data-action="edit" data-fid="' . encrypt($value->id) . '"  style="cursor:pointer;" title="Edit"><i class="fa fa-edit" title="Edit"></i></span>&nbsp;<span class="red ace-icon  bigger-130 change-status" id="status_' . encrypt($value->id) . '"data-status="' . encrypt($value->status) . '" data-fid="' . encrypt($value->id) . '" data-action="status" style="cursor:pointer;" title="Update Status"><i class="fa ' . $icon . '"></i></span></div>';
          

          $data_arr[] = array(
              "console_name" => $console_name,
              "hours" => $hours,
              "players" => $players,
              "price" => $price,
              "status" => $status,
              "action"=>$action,
          );
       }
       $response = array(
          "draw" => intval($draw),
          "iTotalRecords" => $totalRecords,
          "iTotalDisplayRecords" => $totalRecordswithFilter,
          "aaData" => $data_arr
       );

       return response()->json($response); 
   }

   public function saveprice(Request $request) {
   
    $fid = ($request->tid != '') ? decrypt($request->tid) : '';
    $placeId = session('place_id');

    $request->validate([
    'console_id' => 'required|exists:console_master,id',
    'hours_id' => 'required|exists:hours_master,id',
    'no_of_players' => 'required|numeric|min:1',
    'price' => 'required|numeric|min:0'
]);

  $console = ConsoleMaster::where('id', $request->console_id)
    ->where('place_id', $placeId)
    ->first();


if (!$console) {
    return redirect()->route('price_master')
        ->with('failure', 'Invalid console');
}

if ($request->no_of_players > $console->limited_person) {
    return redirect()->route('price_master')
        ->with('failure', 'Players exceed console limit');
}

    $trade=new PriceMaster();
    if ($fid) {
        try
        {
         DB::beginTransaction();
         
      $data = ['console_id' => $request->console_id,'hours_id' => $request->hours_id,'no_of_players' => $request->no_of_players,'price' => $request->price, 'place_id' => $placeId,'updated_by'=>Auth::user()->Pkey];
   
      $exists = PriceMaster::where('place_id', $placeId)
    ->where('console_id', $request->console_id)
    ->where('hours_id', $request->hours_id)
    ->where('no_of_players', $request->no_of_players)
    ->where('id','!=',$fid)
    ->exists();

if ($exists) {
    DB::rollback();
    return redirect()->route('price_master')
        ->with('failure', 'Duplicate price configuration for this branch');
}

        $updateStatus =PriceMaster::where('id',$fid)->update($data);
         DB::commit();
        event(new UserActivity(["action_id"=>Auth::user()->U_Code, "crud_id"=>"Edit", "menu"=>"Price", "comments"=>"Price - Edit Price", "action_data"=>$request->all(), "log_from"=>'Web']));
    return redirect()->route('price_master')->with('success', 'Price details Successfully Updated');
        }
        catch (Exception $e) {
           DB::rollback();
            return redirect()->route('price_master')->with('failure', $e->message);
        }
    } else {
        try
        {
        DB::beginTransaction();
        $maxid=PriceMaster::max('id');
     
  $exists = PriceMaster::where('place_id', $placeId)
    ->where('console_id', $request->console_id)
    ->where('hours_id', $request->hours_id)
    ->where('no_of_players', $request->no_of_players)
    ->when($fid, function ($q) use ($fid) {
        $q->where('id', '!=', $fid);
    })
    ->exists();


if ($exists) {
    DB::rollback();
    return redirect()->route('price_master')
        ->with('failure', 'Price already exists for this branch & console');
}
        $trade->console_id=$request->console_id;
        $trade->hours_id=$request->hours_id;
        $trade->no_of_players=$request->no_of_players;
        $trade->price=$request->price;
        $trade->place_id       = $placeId; 
        $trade->created_by=Auth::user()->Pkey;
        $trade->status='1';
        $trade->save();
          DB::commit();
        event(new UserActivity(["action_id"=>Auth::user()->U_Code, "crud_id"=>"Add", "menu"=>"Price", "comments"=>"Price - Add Price", "action_data"=>$request->all(), "log_from"=>'Web']));
        return redirect()->route('price_master')->with('success', 'New Price details Successfully Created  ');
        }
        catch (Exception $e) {
            DB::rollback();
            return redirect()->route('price_master')->with('failure', $e->message);
        }
    }
}
public function editpprice(Request $request) {

    $fid = decrypt($request['pid']);
    $data = $newSet = [];
    if ($fid) {
        //DB::enableQueryLog(); $qlog = DB::getQueryLog();
        $featureData = PriceMaster::
                select('*')
                ->where('id', $fid)
                ->get();

        if (count($featureData) > 0) {
            $newSet = [
                'console_id' => $featureData[0]['console_id'],
                'hours_id' => $featureData[0]['hours_id'],
                'no_of_players' => $featureData[0]['no_of_players'],
                'price' => $featureData[0]['price'],
                'pid' => encrypt($featureData[0]['id']),
                'action' => 'Update',
                'title' => 'Update price'
            ];
            $data = ['status' => TRUE, 'data' => $newSet];
        } else {
            $data = ['status' => FALSE, 'data' => $newSet];
        }
    } else {
        $data = ['status' => FALSE, 'data' => $newSet];
    }

    echo json_encode($data);
}

public function deleteprice(Request $request) {
    $fid = decrypt($request['pid']);
    $status = decrypt($request['status']);
    $status = ($status == 0) ? '1' : '0';
    $featureData = [];
    if ($fid) {
        $updateStatus =PriceMaster::
                    where('id', $fid)
                ->update(['status' => $status]);
         event(new UserActivity(["action_id"=>Auth::user()->U_Code, "crud_id"=>"Edit", "menu"=>"Price", "comments"=>"Price - Delete Price", "action_data"=>$request->all(), "log_from"=>'Web']));

        if ($updateStatus) {
            $data = ['status' => true, 'message' => 'Price Status Successfully Updated', 'status' => $status];
        } else {
            $data = ['status' => false, 'message' => 'Cannot Update the Status'];
        }
    } else {
        $data = ['status' => false, 'message' => 'No Record Found to update the Status'];
    }
    echo json_encode($data);
}
    //end price master

 //start F&B master

    public function fandbMaster(Request $request){
       
        return view('fandbmaster.list');
    }


    public function masterAjaxfandb(Request $request){
       $placeId = session('place_id');


if (!$placeId) {
    return response()->json([
         "draw" => intval($request->draw),
        "iTotalRecords" => 0,
        "iTotalDisplayRecords" => 0,
        "aaData" => []
    ]);
}

        ## Read value
       $draw = $request->get('draw');
       $start = $request->get("start");
       $rowperpage = $request->get("length"); // Rows display per page

       $columnIndex_arr = $request->get('order');
       $columnName_arr = $request->get('columns');
       $order_arr = $request->get('order');
       $searchValue = $request->get('search');
       $searchValue = $searchValue['value'];
       $columnIndex = $columnIndex_arr[0]['column']; // Column index
       $columnName = $columnName_arr[$columnIndex]['data']; // Column name
       $columnName = 'id';
       $columnSortOrder = $order_arr[0]['dir']; // asc or desc
       // Total records
      $totalRecords = \App\FandBMaster::where('place_id', $placeId)->count();
$totalRecordswithFilter = $totalRecords;


       // Fetch totalRecords
     $records = \App\FandBMaster::where('place_id', $placeId)
            ->orderBy('id','DESC')

                        ->select('*')
                        ->skip($start)
                        ->take($rowperpage)
                        ->get();

       $data_arr = array();

       foreach($records as  $value){
          $name = $value->name;
          $price = $value->price;
          $status = '';
           if($value->status=='1')
           {
          $status ='<span class="label label-success">Active</span>';
          $icon="fa-thumbs-down";
           }
           else
           {
         $status='<span class="label label-danger">Inactive</span>';
           $icon="fa-thumbs-up";
            }
            $action='';
           
           $action .= '<div class="hidden-sm hidden-xs action-buttons"><span class="edit-feature"  data-action="edit" data-fid="' . encrypt($value->id) . '" edit-feature" data-action="edit" data-fid="' . encrypt($value->id) . '"  style="cursor:pointer;" title="Edit"><i class="fa fa-edit" title="Edit"></i></span>&nbsp;<span class="red ace-icon  bigger-130 change-status" id="status_' . encrypt($value->id) . '"data-status="' . encrypt($value->status) . '" data-fid="' . encrypt($value->id) . '" data-action="status" style="cursor:pointer;" title="Update Status"><i class="fa ' . $icon . '"></i></span></div>';
          

          $data_arr[] = array(
              "name" => $name,
              "price" => $price,
              "status" => $status,
              "action"=>$action,
          );
       }
       $response = array(
          "draw" => intval($draw),
          "iTotalRecords" => $totalRecords,
          "iTotalDisplayRecords" => $totalRecordswithFilter,
          "aaData" => $data_arr
       );

       return response()->json($response); 
   }

  public function savefandb(Request $request)
{
    $placeId = session('place_id');

    if (!$placeId) {
        return redirect()->back()->with('failure', 'Select Place First');
    }

    $request->validate([
        'name' => 'required|string|max:100',
        'price' => 'required|numeric|min:0',
        'food_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
    ]);

    $fid = $request->tid ? decrypt($request->tid) : null;

    DB::beginTransaction();
    try {

        $trade = $fid ? \App\FandBMaster::findOrFail($fid) : new \App\FandBMaster();

        $trade->name = $request->name;
        $trade->price = $request->price;
        $trade->place_id = $placeId;

        if (!$fid) {
            $trade->created_by = Auth::user()->Pkey;
            $trade->status = 1;
        } else {
            $trade->updated_by = Auth::user()->Pkey;
        }

        // ✅ IMAGE UPLOAD (ONE TIME ONLY)
        if ($request->hasFile('food_image')) {

            $file = $request->file('food_image');

            if (!$file->isValid()) {
                throw new \Exception('Invalid image upload');
            }

            $filename = time().'_'.$file->getClientOriginalName();
            $destination = public_path('storage/foodimage');

            if (!File::exists($destination)) {
                File::makeDirectory($destination, 0775, true);
            }

            $file->move($destination, $filename);

            $trade->food_image = 'storage/foodimage/'.$filename;
        }

        $trade->save();

        DB::commit();

        return redirect()->route('fandb_master')
            ->with('success', $fid ? 'Updated Successfully' : 'Created Successfully');

    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()->with('failure', $e->getMessage());
    }
}

public function editfandb(Request $request) {

    $fid = decrypt($request['pid']);
    $data = $newSet = [];
    if ($fid) {
        //DB::enableQueryLog(); $qlog = DB::getQueryLog();
        $featureData = \App\FandBMaster::
                select('*')
                ->where('id', $fid)
                ->get();

        if (count($featureData) > 0) {
            $newSet = [
                'name' => $featureData[0]['name'],
                'price' => $featureData[0]['price'],
                 'food_image' => $featureData[0]['food_image'],
                'pid' => encrypt($featureData[0]['id']),
                'action' => 'Update',
                'title' => 'Update F&B'
            ];
            $data = ['status' => TRUE, 'data' => $newSet];
        } else {
            $data = ['status' => FALSE, 'data' => $newSet];
        }
    } else {
        $data = ['status' => FALSE, 'data' => $newSet];
    }

    echo json_encode($data);
}

public function deletefandb(Request $request) {
    $fid = decrypt($request['pid']);
    $status = decrypt($request['status']);
    $status = ($status == 0) ? '1' : '0';
    $featureData = [];
    if ($fid) {
        $updateStatus = \App\FandBMaster::
                    where('id', $fid)
                ->update(['status' => $status]);
         event(new UserActivity(["action_id"=>Auth::user()->U_Code, "crud_id"=>"Edit", "menu"=>"F&B", "comments"=>"F&B - Delete F&B", "action_data"=>$request->all(), "log_from"=>'Web']));

        if ($updateStatus) {
            $data = ['status' => true, 'message' => 'Status Successfully Updated', 'status' => $status];
        } else {
            $data = ['status' => false, 'message' => 'Cannot Update the Status'];
        }
    } else {
        $data = ['status' => false, 'message' => 'No Record Found to update the Status'];
    }
    echo json_encode($data);
}
    //end F&B master



}