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
use Carbon\Carbon; 

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
        
    $placeId = session('place_id');

    if (!$placeId) {
        return redirect()->back()->with('failure', 'Please select Branch first');
    }
    $this->data['console_list'] = ConsoleMaster::where('status', 1)
        ->where('place_id', $placeId)
        ->get();
       // $this->data['console_list']=ConsoleMaster::where('status','1')->get();
        $this->data['hours_list'] = HoursMaster::where('status','1')->get();
        $this->data['reference_list'] = ReferenceMaster::where('status','1')->get();
        $this->data['payment_type_list'] = PaymentTypeMaster::where('status','1')->get();
        $input = NoofPlayerMaster::first();
        $this->data['no_of_players_list'] = range(1, $input->player_number);
        $this->data['sor_settings']=[];
       // $this->data['FandBMaster'] = \App\FandBMaster::where('status',1)->get();
        $this->data['FandBMaster'] = \App\FandBMaster::where('status', 1)
        ->where('place_id', $placeId)
        ->get();

        return view('submission.create',$this->data);
    }   
     public function SubmissionSave(Request $request)
    {
        $placeId = session('place_id');

         if (!$placeId) {
         return redirect()->back()->with('sweet_error','Please select Branch first');
        }

          $now = Carbon::now(); 
        DB::beginTransaction();
        try {
            $no_of_players = $request->input('no_of_players');
            $hours_id = $request->input('hours_id');
            $start_time = $request->input('start_time');
            $end_time = $request->input('end_time');
            $total_hours = $request->input('total_hours');
            $discount = $request->input('discount');
            $payment_type = $request->input('payment_type');
            $cash_amount = $request->input('cash_amount', 0);
            $upi_amount  = $request->input('upi_amount', 0);
            $customer_place = $request->input('customer_place');
            $calcpercent = $request->input('calcpercent');
            $console_id = $request->input('console_id');
            $date = $request->input('date');
            $total_cost = $request->input('total_cost');
            $final_cost = $request->input('final_cost');
            $fandb_cost = $request->input('fandb_cost');
            $reference_id = $request->input('reference_id');

            //Convert times to 24-hour format for accurate comparison
            $start_time_24 = Carbon::createFromFormat('h:i A', $start_time)->format('H:i:s');
            $end_time_24   = Carbon::createFromFormat('h:i A', $end_time)->format('H:i:s');
            $date_db       = date('Y-m-d', strtotime($date));

  $exists = Submissiondata::where('console_id', $console_id)
    ->where('place_id', $placeId)
    ->whereDate('date', $date_db)
    ->where('status', 1)
    ->where(function ($q) use ($start_time_24, $end_time_24) {
        $q->where('start_time', '<', $end_time_24)
          ->where('end_time', '>', $start_time_24);
    })
    ->exists();


            if ($exists) {
                return redirect()->back()->with('sweet_error', 'This console is already booked for the selected time slot!');
            }

            // Extract player & mobile info
            $names = array_column($calcpercent, 'name');
            $mobile_no = array_column($calcpercent, 'mobile_no');
            $namesString = implode(', ', $names);
            $mobilenoString = implode(', ', $mobile_no);
            $payment_split = null;

if ($cash_amount > 0 || $upi_amount > 0) {
    $payment_split = json_encode([
        'cash' => $cash_amount,
        'upi'  => $upi_amount
    ]);
}
            // F&B info merge
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
            // Insert data
            $insert_data = [
                'place_id'      => $placeId, 
                'no_of_players' => $no_of_players,
                'console_id' => $console_id,
                'hours_id' => $hours_id,
                'date' => isset($date) ? date('Y-m-d', strtotime($date)) : NULL,
                'start_time' => $start_time_24,
                'end_time' => $end_time_24,
                'player_informations' => isset($calcpercent) ? serialize($calcpercent) : NULL,
                'fandb_informations' => isset($merged) ? serialize($merged) : NULL,
                'total_hours' => $total_hours,
                'total_cost' => $total_cost,
                'discount' => $discount,
                'final_cost' => $final_cost,
                'fandb_cost' => $fandb_cost ? $fandb_cost : 0.00,
                'payment_type' => $payment_type,
                'payment_split' => $payment_split, 
                'customer_place' => $customer_place,
                'status' => '1',
                'log_from' => 'Web',
                'reference_id' => $reference_id,
                'filter_mobile_no' => $mobilenoString,
                'filter_name' => $namesString,
                'created_at' => now(),
                'created_by' => Auth::user()->Pkey,
            ];
            Submissiondata::insert($insert_data);
            DB::commit();
            event(new UserActivity([
                "action_id" => Auth::user()->U_Code,
                "crud_id" => "Add",
                "menu" => "Submission",
                "comments" => "Submission- Add Submission",
                "action_data" => $request->all(),
                "log_from" => 'Web'
            ]));

            return redirect("submission_report_list")->with('success', 'Data created successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('sweet_error', 'Something went wrong! Please try again later.');
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
               //  Show Web / Mobile badge
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
    
public function SubmissionEdit($edit_id)
{
    $edit_id = decrypt($edit_id);

    $submission_details = Submissiondata::find($edit_id);

    if (!$submission_details) {
        return redirect()->route('submission_list')->with('error', 'Submission not found.');
    }

   // Player Information - supports JSON or serialized
if (!empty($submission_details->player_informations)) {
    $playerData = $submission_details->player_informations;
    $decoded = null;

    if (is_string($playerData)) {
        $decoded = @unserialize($playerData);
        if ($decoded === false && json_decode($playerData, true) !== null) {
            $decoded = json_decode($playerData, true);
        }
    } elseif (is_array($playerData)) {
        $decoded = $playerData;
    }

    $this->data['sor_settings'] = is_array($decoded) ? $decoded : [];
} else {
    $this->data['sor_settings'] = [];
}
//  F&B Information - supports JSON, serialized, or old ID-based format
if (!empty($submission_details->fandb_informations)) {
    $fandbData = $submission_details->fandb_informations;
    $decoded = null;

    if (is_string($fandbData)) {
        //  Try unserialize first
        $decoded = @unserialize($fandbData);

        //  If unserialize fails, try JSON decode (force array)
        if ($decoded === false || !is_array($decoded)) {
            $jsonDecoded = json_decode($fandbData, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($jsonDecoded)) {
                $decoded = $jsonDecoded;
            }
        }
    } elseif (is_array($fandbData)) {
        $decoded = $fandbData;
    }

    $normalized = [];

    //  Normalize all formats for Blade
    if (is_array($decoded)) {
        foreach ($decoded as $item) {
            $foodId = $item['food_id'] ?? $item['name'] ?? null;
            $quantity = $item['qty'] ?? $item['quantity'] ?? 1;
            $price = $item['price'] ?? 0;

            //  Handle old web format where "name" is actually an ID
            $foodName = $item['name'] ?? '';
            if (is_numeric($foodId)) {
                $food = \App\FandBMaster::find($foodId);
                $foodName = $food ? $food->name : "Item #{$foodId}";
            }

            $normalized[] = [
                'food_id' => $foodId,
                'name'    => $foodName,
                'qty'     => $quantity,
                'price'   => $price,
                'subtotal'=> $price * $quantity,
            ];
        }
    }

    $this->data['fandb_informations'] = $normalized;
} else {
    $this->data['fandb_informations'] = [];
}
//dd($this->data['fandb_informations']);
$paymentSplit = [];

if (!empty($submission_details->payment_split)) {
    $paymentSplit = json_decode($submission_details->payment_split, true);
}

$this->data['payment_split'] = $paymentSplit;
    //  Load other master data
   $placeId = session('place_id');

$this->data['console_list'] = ConsoleMaster::where('status', 1)
    ->where('place_id', $placeId)
    ->get();

    $this->data['hours_list'] = HoursMaster::where('status', '1')->get();
    $this->data['reference_list'] = ReferenceMaster::where('status', '1')->get();
    $this->data['payment_type_list'] = PaymentTypeMaster::where('status', '1')->get();
    $input = NoofPlayerMaster::first();
    $this->data['no_of_players_list'] = range(1, $input->player_number);
    $this->data['FandBMaster'] = \App\FandBMaster::where('status', 1)
    ->where('place_id', $placeId)
    ->get();
    $this->data['submission_details'] = $submission_details;
    $this->data['edit_id'] = $edit_id;

    return view('submission.edit', $this->data);
}

public function updateSubmission(Request $request,$id){
      
    //   dd($request->all());
        
    DB::beginTransaction();
    try {                
        $no_of_players=$request->input('no_of_players');
        $hours_id=$request->input('hours_id');
        $start_time = Carbon::createFromFormat('h:i A', $request->start_time)->format('H:i:s');
        $end_time   = Carbon::createFromFormat('h:i A', $request->end_time)->format('H:i:s');
        $total_hours=$request->input('total_hours');
        $discount=$request->input('discount');
        $payment_type=$request->input('payment_type');
        $cash_amount = $request->input('cash_amount', 0);
        $upi_amount  = $request->input('upi_amount', 0);
        $customer_place = $request->input('customer_place');
        $calcpercent=$request->input('calcpercent');
        $console_id=$request->input('console_id');
        $date=$request->input('date');
      //  $end_time=$request->input('end_time');
        $fandb_cost=$request->input('fandb_cost');
        $reference_id=$request->input('reference_id');           
        // Extract names
        $names = array_column($calcpercent, 'name');
        $mobile_no = array_column($calcpercent, 'mobile_no');
        // Implode into a comma-separated string
        $namesString = implode(', ', $names);
        $mobilenoString = implode(', ', $mobile_no);
        $payment_split = null;
if ($cash_amount > 0 || $upi_amount > 0) {
    $payment_split = json_encode([
        'cash' => $cash_amount,
        'upi'  => $upi_amount
    ]);
}                  
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

    $submission = Submissiondata::find($id);
    $placeId = $submission->place_id;

   
$date_db = date('Y-m-d', strtotime($date));

$exists = Submissiondata::where('id', '!=', $id) // exclude self
    ->where('console_id', $console_id)
    ->where('place_id', $placeId)
    ->whereDate('date', $date_db)
    ->where('status', 1)
    ->where(function ($q) use ($start_time, $end_time) {
        $q->where('start_time', '<', $end_time)
          ->where('end_time', '>', $start_time);
    })
    ->exists();

if ($exists) {
    return redirect()->back()->with('sweet_error', 'This console is already booked for the selected time slot!');
}

   $total_cost = (float) $request->input('total_cost', 0);
$fandb_cost = (float) $request->input('fandb_cost', 0);
$discount   = (float) $request->input('discount', 0);

// FLAT discount (UI same)
$final_cost = ($total_cost - $discount) + $fandb_cost;

if ($final_cost < 0) {
    $final_cost = 0;
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
            'fandb_cost'=>$fandb_cost ? $fandb_cost : 0.00,
            'total_hours'=>$total_hours,
            'total_cost'=>$total_cost,
            'discount'=>$discount,
            'final_cost'=>$final_cost,
            'payment_type'=>$payment_type,
            'payment_split' => $payment_split,
            'customer_place' => $customer_place,
            'status'=>'1',
            'log_from' => 'Web',
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
  public function getpriceinfo(Request $request)
{
     \Log::info('getpriceinfo HIT', $request->all());
    $placeId = session('place_id');

    if (!$placeId) {
        return response()->json([
            'status' => false,
            'total_cost' => 0,
            'message' => 'Branch not selected'
        ]);
    }

    $price = PriceMaster::where('place_id', $placeId)
        ->where('console_id', $request->console_id)
        ->where('hours_id', $request->hours_id)
        ->where('no_of_players', $request->no_of_players)
      ->where('status', '1') 
        ->orderBy('id', 'DESC')
        ->value('price');

    return response()->json([
        'status' => $price !== null,
        'total_cost' => $price ?? 0
    ]);
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
    private function decodeData($raw)
{
    if (empty($raw)) return [];

    // Try unserialize first (Web)
    $data = @unserialize($raw);

    // If unserialize fails, try JSON (Mobile)
    if ($data === false || !is_array($data)) {
        $jsonDecoded = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($jsonDecoded)) {
            $data = $jsonDecoded;
        }
    }

    return is_array($data) ? $data : [];
}
public function getNameByMobile(Request $request)
{
    $mobile = trim($request->mobile);

    if (!$mobile) {
        return response()->json(['status' => false]);
    }

    // Latest submission-la search
    $record = Submissiondata::orderBy('id', 'DESC')->get();

    foreach ($record as $row) {

        if (!$row->filter_mobile_no || !$row->filter_name) continue;

        $mobiles = array_map('trim', explode(',', $row->filter_mobile_no));
        $names   = array_map('trim', explode(',', $row->filter_name));

        $index = array_search($mobile, $mobiles);

        if ($index !== false && isset($names[$index])) {
            return response()->json([
                'status' => true,
                'name'   => $names[$index]
            ]);
        }
    }

    return response()->json(['status' => false]);
}


}