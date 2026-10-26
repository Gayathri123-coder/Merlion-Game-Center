<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\PlaceMaster;
use App\Events\UserActivity;
use Illuminate\Support\Facades\Event;



class PlaceController extends Controller
{
    public function index()
    {
        return view('place.list');
    }
     public function saveplace(Request $request) {
        $messages = [
            'place_name.required' => 'Place Name is Required'
        ];

        $rules =  [];

         if ($request->tid != '') {
         $rules['place_name'] = 'required|max:50|unique:place_master,place_name,'.decrypt($request->tid);
        }
        else
        {
         $rules['place_name'] = 'required|max:50|unique:place_master,place_name';

        }
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()
                            ->back()
                            ->withErrors($validator)
                            ->withInput();
        }
        $fid = ($request->tid != '') ? decrypt($request->tid) : '';
        $trade=new PlaceMaster();
        if ($fid) {
            try
            {
             DB::beginTransaction();
             
          $data = ['place_name' => $request->place_name,'updated_by'=>Auth::user()->Pkey];
           
            $updateStatus =PlaceMaster::where('id',$fid)->update($data);
             DB::commit();
            event(new UserActivity(["action_id"=>Auth::user()->U_Code, "crud_id"=>"Edit", "menu"=>"Place", "comments"=>"Place - Edit Place", "action_data"=>$request->all(), "log_from"=>'Web']));
        return redirect()->route('place_master')->with('success', 'Place details Successfully Updated');
            }
            catch (Exception $e) {
               DB::rollback();
                return redirect()->route('place_master')->with('failure', $e->message);
            }
        } else {
            try
            {
            DB::beginTransaction();
            $maxid=PlaceMaster::max('id');
            $trade->place_name=$request->place_name;
            $trade->created_by=Auth::user()->Pkey;
            $trade->status='1';
            $trade->save();
              DB::commit();
            event(new UserActivity(["action_id"=>Auth::user()->U_Code, "crud_id"=>"Add", "menu"=>"Place", "comments"=>"Place - Add Place", "action_data"=>$request->all(), "log_from"=>'Web']));
            return redirect()->route('place_master')->with('success', 'New Place details Successfully Created  ');
            }
            catch (Exception $e) {
                DB::rollback();
                return redirect()->route('place_master')->with('failure', $e->message);
            }
        }
    }
    
    public function editplace(Request $request) {

        $fid = decrypt($request['pid']);
        $data = $newSet = [];
        if ($fid) {
            //DB::enableQueryLog(); $qlog = DB::getQueryLog();
            $featureData =PlaceMaster::
                    select('*')
                    ->where('id', $fid)
                    ->get();

            if (count($featureData) > 0) {
                $newSet = [
                    'trade' => $featureData[0]['place_name'],
                    'pid' => encrypt($featureData[0]['id']),
                    'action' => 'Update',
                    'title' => 'Update Place'
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
     public function deleteplace(Request $request) {
        $fid = decrypt($request['pid']);
        $status = decrypt($request['status']);
        $status = ($status == 0) ? '1' : '0';
        $featureData = [];
        if ($fid) {
         $updateStatus = PlaceMaster::where('id', $fid)
    ->update(['status' => $status]);

// if place set to INACTIVE
if ($status == 0 && session('place_id') == $fid) {
    session()->forget('place_id');
}

             event(new UserActivity(["action_id"=>Auth::user()->U_Code, "crud_id"=>"Edit", "menu"=>"Place", "comments"=>"Place - Delete Place", "action_data"=>$request->all(), "log_from"=>'Web']));

            if ($updateStatus) {
                $data = ['status' => true, 'message' => 'Place Status Successfully Updated', 'status' => $status];
            } else {
                $data = ['status' => false, 'message' => 'Cannot Update the Status'];
            }
        } else {
            $data = ['status' => false, 'message' => 'No Record Found to update the Status'];
        }
        echo json_encode($data);
    }
  public function masterAjaxPlaceList(Request $request){

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
        $totalRecords = PlaceMaster::select('count(*) as allcount');
        if($searchValue!=NULL && $searchValue!='')
        {
            $totalRecords =$totalRecords->where('place_name', 'LIKE', '%'.$searchValue.'%');

        }
        $totalRecords =$totalRecords->count();
        $totalRecordswithFilter = PlaceMaster::select('count(*) as allcount');
        if($searchValue!=NULL && $searchValue!='')
        {
            $totalRecordswithFilter =$totalRecordswithFilter->where('place_name', 'LIKE', '%'.$searchValue.'%');

        }
         $totalRecordswithFilter =$totalRecordswithFilter->count();

        // Fetch totalRecords
        $records = PlaceMaster::orderBy($columnName,$columnSortOrder);
        if($searchValue!=NULL && $searchValue!='')
        {
            $records =$records->where('place_name', 'LIKE', '%'.$searchValue.'%');

        }
        if($searchValue!=NULL && $searchValue!='')
             {
              $records =$records->where('place_name', 'LIKE', '%'.$searchValue.'%');
             }
              
             $records =$records->select('*')
              ->skip($start)
              ->take($rowperpage)
              ->get();

        $data_arr = array();

        foreach($records as  $value){
           $place_name = $value->place_name;
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
               "place_name" => $place_name,
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
      public function setPlace(Request $request)
{
    if (!$request->place_id) {
        session()->forget('place_id');
        return redirect()->back();
    }

    session(['place_id' => $request->place_id]);

    return redirect()->back();
}

}