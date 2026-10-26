<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Events\UserActivity;
use App\Submissiondata;
use App\ConsoleMaster;
use App\HoursMaster;
use App\NoofPlayerMaster;
use App\ReferenceMaster;
use App\PaymentTypeMaster;
use App\PriceMaster;

class SubmissionController extends Controller
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
       
        return view('submission.list');
    }
    public function create()
    {
        
        $this->data['console_list']=ConsoleMaster::where('status','1')->get();
        $this->data['hours_list'] = HoursMaster::where('status','1')->get();
        $this->data['reference_list'] = ReferenceMaster::where('status','1')->get();
        $this->data['payment_type_list'] = PaymentTypeMaster::where('status','1')->get();
        $input = NoofPlayerMaster::first();
        $this->data['no_of_players_list'] = range(1, $input->player_number);
        $this->data['sor_settings']=[];
        $this->data['FandBMaster'] = \App\FandBMaster::where('status',1)->get();
        return view('submission.create',$this->data);
    }
    
    
    public function SubmissionSave(Request $request){
       
       // dd($request->all());
        
        DB::beginTransaction();
//
            try {
                
                $no_of_players=$request->input('no_of_players');
                $hours_id=$request->input('hours_id');
                $start_time=$request->input('start_time');
                $total_hours=$request->input('total_hours');
                $discount=$request->input('discount');
                $payment_type=$request->input('payment_type');
                $calcpercent=$request->input('calcpercent');
                $console_id=$request->input('console_id');
                $date=$request->input('date');
              
                 $end_time=$request->input('end_time');
                 $total_cost=$request->input('total_cost');
                 $final_cost=$request->input('final_cost');
                 $fandb_cost=$request->input('fandb_cost');
                 $reference_id=$request->input('reference_id');
                
               // Extract names
                $names = array_column($calcpercent, 'name');
                $mobile_no = array_column($calcpercent, 'mobile_no');

                // Implode into a comma-separated string
                $namesString = implode(', ', $names);
                $mobilenoString = implode(', ', $mobile_no);
                
                $merged = [];

                $fnames = $request->input('name');
                $qtys = $request->input('qty');
                $prices = $request->input('price');

                for ($i = 0; $i < count($fnames); $i++) {
                    $merged[] = [
                        'name'  => $fnames[$i],
                        'qty'   => $qtys[$i],
                        'price' => $prices[$i],
                    ];
                }
                
                /*Insert Data*/
                $insert_data=[
                    'no_of_players'=>$no_of_players,
                    'console_id'=>$console_id,
                    'hours_id'=>$hours_id,
                    'date'=>isset($date) ? date('Y-m-d',strtotime($date)):NULL,
                    'start_time'=>$start_time,
                    'end_time'=>$end_time,
                    'player_informations'=>isset($calcpercent) ? serialize($calcpercent) : NULL,
                    'fandb_informations'=>isset($merged) ? serialize($merged) : NULL,
                    'total_hours'=>$total_hours,
                    'total_cost'=>$total_cost,
                    'discount'=>$discount,
                    'final_cost'=>$final_cost,
                    'fandb_cost'=>$fandb_cost ? $fandb_cost : 0.00,
                    'payment_type'=>$payment_type,
                    'status'=>'1',
                    'reference_id'=>$reference_id,
                    'filter_mobile_no'=>$mobilenoString,
                    'filter_name'=>$namesString,
                    'created_at'=>date('Y-m-d H:i:s'),
                    'created_by'=>Auth::user()->Pkey,
                    
                ];
                
                /*Insert Data*/
                $user_save=Submissiondata::insert($insert_data);
                 DB::commit();
                event(new UserActivity(["action_id"=>Auth::user()->U_Code, "crud_id"=>"Add", "menu"=>"Submission", "comments"=>"Submission- Add Submission", "action_data"=>$request->all(), "log_from"=>'Web']));
                return redirect("submission_report_list")->withSuccess('Data created successfully!');
                // all good
            } catch (\Exception $e) {
              //  DB::rollback();
                dd($e);
                return redirect("submission_report_list")->withSuccess('Please try again later!');
//                
//dd($user_save);
//                // something went wrong
            }
        
        
        
        
    }
    
    
    public function ajaxSubmissionList(Request $request){
         ## Read value
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        
       
        // Total records
        $totalRecords = Submissiondata::select('count(*) as allcount');

        $totalRecords=$totalRecords
                ->count();
        
        $totalRecordswithFilter = Submissiondata::select('count(*) as allcount');
//                ->whereNotIn('Pkey',[1])
        
        
        $totalRecordswithFilter=$totalRecordswithFilter->count();

        // Fetch records
        $records = Submissiondata::
                 select('*')
              ->skip($start)
              ->take($rowperpage);
             
              $records=$records->orderBy('id','DESC')->get();

        $data_arr = array();

        foreach($records as $key=> $record){
           $id = $key+1;
         $id=$totalRecords-$key-$start;
           $date = date('d-m-Y',strtotime($record->date));
           $console_name = $record->GetConsoleInfo->console_name;
           $hours = $record->GetHoursInfo->hours;
           $no_of_players = $record->no_of_players;
          
           $status = '';
           if($record->status=='1')
           {
          $status ='<span class="label label-success">Active</span>';
          $icon="fa-thumbs-down";
           }
           else
           {
         $status='<span class="label label-danger">Inactive</span>';
           $icon="fa-thumbs-up";
            }
           $edit_url=route('submission_edit',[encrypt($record->id)]);
           $action='';
               // 🟢 Show Web / Mobile badge
        // (If you don’t have log_from column in Submissiondata, it will default to “Web”)
        $source = $record->log_from ?? 'Web';
        $badgeColor = $source === 'Mobile' ? 'success' : 'info';
        $badge = '<span class="badge badge-' . $badgeColor . '" style="margin-right:5px;">' . $source . '</span>';

        $action .= '<div class="hidden-sm hidden-xs action-buttons">'
            . $badge
            . "<a href='".$edit_url."'><i class='fa fa-edit'></i></a>&nbsp;"
            . '<span class="red ace-icon bigger-130 change-status" 
                id="status_' . encrypt($record->id) . '" 
                data-status="' . encrypt($record->status) . '" 
                data-fid="' . encrypt($record->id) . '" 
                data-action="status" 
                style="cursor:pointer;" 
                title="Update Status">
                <i class="fa ' . $icon . '"></i>
            </span></div>';

           
          
           $data_arr[] = array(
               "Pkey" => $id,
               "date" => $date,
               "console_name" => $console_name,
               "hours" => $hours,
               "no_of_players" => $no_of_players,
               "status" => $status,
              "action" => $action

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
    
public function edit($id)
{
    try {
        $id = decrypt($id);
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Invalid ID');
    }

    $submission_details = \App\Submissiondata::find($id);

    if (!$submission_details) {
        return redirect()->back()->with('error', 'Submission not found');
    }

    // ✅ Safely handle player_informations (array / serialized / JSON)
    $playerInfo = $submission_details->player_informations;
    if (is_array($playerInfo)) {
        $this->data['sor_settings'] = $playerInfo;
    } elseif (is_string($playerInfo)) {
        $unserialized = @unserialize($playerInfo);
        if ($unserialized !== false || $playerInfo === 'b:0;') {
            $this->data['sor_settings'] = $unserialized;
        } else {
            $json = json_decode($playerInfo, true);
            $this->data['sor_settings'] = is_array($json) ? $json : [];
        }
    } else {
        $this->data['sor_settings'] = [];
    }

    // ✅ Safely handle fandb_informations (array / serialized / JSON)
    $fandbInfo = $submission_details->fandb_informations;
    if (is_array($fandbInfo)) {
        $this->data['fandb'] = $fandbInfo;
    } elseif (is_string($fandbInfo)) {
        $unserialized = @unserialize($fandbInfo);
        if ($unserialized !== false || $fandbInfo === 'b:0;') {
            $this->data['fandb'] = $unserialized;
        } else {
            $json = json_decode($fandbInfo, true);
            $this->data['fandb'] = is_array($json) ? $json : [];
        }
    } else {
        $this->data['fandb'] = [];
    }

    // ✅ Assign remaining fields
    $this->data['submission_details'] = $submission_details;
    $this->data['hours'] = \App\HoursMaster::select('id', 'hours', 'price')->orderBy('hours', 'asc')->get();
    $this->data['consoles'] = \App\ConsoleMaster::select('id', 'console_name', 'price')->orderBy('id', 'asc')->get();
    $this->data['players'] = \App\NoofPlayerMaster::select('id', 'players', 'price')->orderBy('id', 'asc')->get();
    $this->data['references'] = \App\ReferenceMaster::select('id', 'name')->orderBy('id', 'asc')->get();
    $this->data['payment_types'] = \App\PaymentTypeMaster::select('id', 'payment_type')->orderBy('id', 'asc')->get();

    return view('submission.edit', $this->data);
}

public function updateSubmission(Request $request,$id){
      
    //   dd($request->all());
        
    DB::beginTransaction();
    //
    try {
                     
        $no_of_players=$request->input('no_of_players');
        $hours_id=$request->input('hours_id');
        $start_time=$request->input('start_time');
        $total_hours=$request->input('total_hours');
        $discount=$request->input('discount');
        $payment_type=$request->input('payment_type');
        $calcpercent=$request->input('calcpercent');
        $console_id=$request->input('console_id');
        $date=$request->input('date');
        $end_time=$request->input('end_time');
        $fandb_cost=$request->input('fandb_cost');
        $reference_id=$request->input('reference_id');
                     
        // Extract names
        $names = array_column($calcpercent, 'name');
        $mobile_no = array_column($calcpercent, 'mobile_no');

        // Implode into a comma-separated string
        $namesString = implode(', ', $names);
        $mobilenoString = implode(', ', $mobile_no);
                    
        $merged = [];

        $fnames = $request->input('name');
        $qtys = $request->input('qty');
        $prices = $request->input('price');

        for ($i = 0; $i < count($fnames); $i++) {
            $merged[] = [
                'name' => $fnames[$i],
                'qty' => $qtys[$i],
                'price' => $prices[$i],
            ];
        }

        /* ✅ Auto Price Recalculation */
        $get_price = PriceMaster::where('console_id', $console_id)
            ->where('no_of_players', $no_of_players)
            ->where('hours_id', $hours_id)
            ->first();

        if ($get_price) {
            $total_cost = $get_price->price;
        } else {
            $total_cost = 0;
        }

        // Calculate final cost after discount + F&B
        $discount_amount = ($discount / 100) * $total_cost;
        $final_cost = ($total_cost - $discount_amount) + ($fandb_cost ?? 0);

        /*Insert Data*/
        $insert_data=[
            'no_of_players'=>$no_of_players,
            'console_id'=>$console_id,
            'hours_id'=>$hours_id,
            'date'=>isset($date) ? date('Y-m-d',strtotime($date)):NULL,
            'start_time'=>$start_time,
            'end_time'=>$end_time,
            'player_informations'=>isset($calcpercent) ? serialize($calcpercent) : NULL,
            'fandb_informations'=>isset($merged) ? serialize($merged) : NULL,
            'fandb_cost'=>$fandb_cost ? $fandb_cost : 0.00,
            'total_hours'=>$total_hours,
            'total_cost'=>$total_cost,
            'discount'=>$discount,
            'final_cost'=>$final_cost,
            'payment_type'=>$payment_type,
            'status'=>'1',
            'reference_id'=>$reference_id,
            'filter_mobile_no'=>$mobilenoString,
            'filter_name'=>$namesString,
            'updated_at'=>date('Y-m-d H:i:s'),
            'updated_by'=>Auth::user()->Pkey,
        ];
                     
        /*Insert Data*/
        $user_save=Submissiondata::where('id',$id)->update($insert_data);
        DB::commit();

        event(new UserActivity([
            "action_id"=>Auth::user()->U_Code, 
            "crud_id"=>"Add", 
            "menu"=>"Submission", 
            "comments"=>"Submission- Add Submission", 
            "action_data"=>$request->all(), 
            "log_from"=>'Web'
        ]));

        return redirect("submission_report_list")->withSuccess('Data updated successfully!');
                     
    } catch (\Exception $e) {
        //  DB::rollback();
        // dd($e);
        return redirect("submission_report_list")->withSuccess('Please try again later!');
    }
}


    public function getpriceinfo(Request $request){
       $get_price = PriceMaster::where('console_id',$request->console_id)
                    ->where('no_of_players',$request->no_of_players)
                    ->where('hours_id',$request->hours_id)
                    ->first();
         if(isset($get_price)){
            $data = ['status' => TRUE, 'data' => $get_price];
         }else{
            $data = ['status' => FALSE, 'data' => ''];
         }
        return $data;
    }
    public function deletesubmission(Request $request) {
        $fid = decrypt($request['pid']);
        $status = decrypt($request['status']);
        $status = ($status == 0) ? '1' : '0';
        $featureData = [];
        if ($fid) {
            $updateStatus =Submissiondata::
                        where('id', $fid)
                    ->delete();
             event(new UserActivity(["action_id"=>Auth::user()->U_Code, "crud_id"=>"Edit", "menu"=>"Submission", "comments"=>"Submission - Delete Submission", "action_data"=>$request->all(), "log_from"=>'Web']));
    
            if ($updateStatus) {
                $data = ['status' => true, 'message' => ' Record Deleted Successfully', 'status' => $status];
            } else {
                $data = ['status' => false, 'message' => 'Cannot Delete the record'];
            }
        } else {
            $data = ['status' => false, 'message' => 'No Record Found to Delete the record'];
        }
        echo json_encode($data);
    }
}