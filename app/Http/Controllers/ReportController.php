<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Submissiondata;
use App\ConsoleMaster;
use App\HoursMaster;
use App\NoofPlayerMaster;
use App\ReferenceMaster;
use App\PaymentTypeMaster;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

   public function index()
{
    $placeId = session('place_id');

   if (!$placeId) {
    return redirect()->route('home')
        ->with('failure', 'Please select Place first');
}
    $this->data['console_list'] = ConsoleMaster::where('status', 1)
        ->where('place_id', $placeId)
        ->get();

    $this->data['hours_list'] = HoursMaster::where('status', 1)->get();
    $this->data['reference_list'] = ReferenceMaster::where('status', 1)->get();
    $this->data['payment_type_list'] = PaymentTypeMaster::where('status', 1)->get();

    $input = NoofPlayerMaster::first();
    $this->data['no_of_players_list'] = range(1, $input->player_number);

    return view('report.list', $this->data);
}

public function ajaxReportList(Request $request)
{
    $allBranches = $request->get('allBranches'); 
    
    $placeId = session('place_id');
    $draw = $request->get('draw');
    $start = $request->get("start");
    $rowperpage = $request->get("length");
    $name = $request->get('name');
    $mobile_no = $request->get('mobile_no');
    $console_id = $request->get('console_id');
    $no_of_players = $request->get('no_of_players');
    $reference_id = $request->get('reference_id');
    $start_time = $request->get('start_time');
    $end_time = $request->get('end_time');

    // --- Convert date filters ---
    foreach (['start_time', 'end_time'] as $var) {
        if (!empty($$var)) {
            try {
                $$var = Carbon::createFromFormat('d-m-Y', $$var)->format('Y-m-d');
            } catch (\Exception $e) {}
        }
    }
    // --- Base Query ---
  $baseQuery = Submissiondata::query()
    ->leftJoin('console_master', 'console_master.id', '=', 'submission_data.console_id')
    ->leftJoin('hours_master', 'hours_master.id', '=', 'submission_data.hours_id')
    ->leftJoin('payment_type_master', 'payment_type_master.id', '=', 'submission_data.payment_type')
    ->leftJoin('reference_master', 'reference_master.id', '=', 'submission_data.reference_id')
    ->select(
        'submission_data.*',
        'submission_data.id as id',
        'submission_data.payment_type as payment_type',
        'hours_master.hours as hours'
    );

/*PLACE FILTER ONLY WHEN NOT ALL BRANCHES */
if (!$allBranches) {
    $baseQuery->where('console_master.place_id', $placeId);
}


    // --- Apply Filters (custom filters) ---
    if ($name) $baseQuery->where('submission_data.filter_name', 'like', "%$name%");
    if ($mobile_no) $baseQuery->where('submission_data.filter_mobile_no', 'like', "%$mobile_no%");
    if ($console_id) $baseQuery->where('submission_data.console_id', $console_id);
    if ($no_of_players) $baseQuery->where('submission_data.no_of_players', $no_of_players);
    if ($reference_id) $baseQuery->where('submission_data.reference_id', $reference_id);
    if ($start_time) $baseQuery->where('submission_data.date', '>=', $start_time);
    if ($end_time) $baseQuery->where('submission_data.date', '<=', $end_time);

    // --- Global Search (DataTables Search box) ---
    $searchValue = $request->input('search.value');
    if ($searchValue) {
        $baseQuery->where(function($q) use ($searchValue) {
            $q->where('submission_data.filter_name', 'like', "%{$searchValue}%")
              ->orWhere('submission_data.filter_mobile_no', 'like', "%{$searchValue}%")
              ->orWhere('console_master.console_name', 'like', "%{$searchValue}%")
              ->orWhere('payment_type_master.payment_type', 'like', "%{$searchValue}%")
              ->orWhere('reference_master.name', 'like', "%{$searchValue}%");
        });
    }

    // --- Count records ---
  $totalRecordsQuery = Submissiondata::join(
    'console_master',
    'console_master.id',
    '=',
    'submission_data.console_id'
);

if (!$allBranches) {
    $totalRecordsQuery->where('console_master.place_id', $placeId);
}

$totalRecords = $totalRecordsQuery->count();


    $totalRecordswithFilter = (clone $baseQuery)->count();

    // --- Pagination ---
    if ($rowperpage != "-1") {
        $baseQuery->skip($start)->take($rowperpage);
    }

    $records = $baseQuery->orderBy('submission_data.id', 'DESC')->get();

    // --- Calculations ---
    $totalCostSum = $records->sum('total_cost');
    $discount = $records->sum('discount');
    $final_cost = $records->sum('final_cost');
    $fandb_cost = $records->sum('fandb_cost');

    $totalMinutes = 0;
    $data_arr = [];

    foreach ($records as $key => $record) {
        try {
            if (preg_match('/AM|PM/i', $record->start_time)) {
                $startTimeObj = Carbon::createFromFormat('h:i A', trim($record->start_time));
            } else {
                $startTimeObj = Carbon::createFromFormat('H:i', trim($record->start_time));
            }
        } catch (\Exception $e) {
            $startTimeObj = Carbon::parse($record->start_time);
        }

        // START TIME display
$start_display = $startTimeObj->format('h:i A');

// END TIME display (USE DB VALUE)
try {
    if (preg_match('/AM|PM/i', $record->end_time)) {
        $endTimeObj = Carbon::createFromFormat('h:i A', trim($record->end_time));
    } else {
        $endTimeObj = Carbon::createFromFormat('H:i:s', trim($record->end_time));
    }
} catch (\Exception $e) {
    $endTimeObj = Carbon::parse($record->end_time);
}

$end_display = $endTimeObj->format('h:i A');


        if (!empty($record->total_hours)) {
            [$h, $m] = explode(':', $record->total_hours);
            $totalMinutes += ((int)$h * 60) + (int)$m;
        }

        $date = date('d-m-Y', strtotime($record->date));
        $console_name = $record->GetConsoleInfo->console_name ?? '';
   // ✅ Show only first player's NAME (no mobile number)
$player_name = '-';

if (!empty($record->player_informations)) {
    $decoded = @unserialize($record->player_informations);

    if ($decoded === false || !is_array($decoded)) {
        $decoded = json_decode($record->player_informations, true);
    }

    if (is_array($decoded) && count($decoded) > 0) {
        $first = $decoded[0];
        $name = isset($first['name']) && $first['name'] !== '' ? $first['name'] : '-';
        $player_name = $name;
    }
} else {
    // Fallback for older records
    $player_name = $record->filter_name ?: '-';
}


        $edit_url = route('submission_edit', [encrypt($record->id)]);
        $logFrom = strtolower($record->log_from ?? 'web');
        $badge = $logFrom === 'mobile' ? "<span class='badge badge-success'>M</span> " : "<span class='badge badge-info'>W</span> ";

        $action = '<div class="hidden-sm hidden-xs action-buttons">'
            . $badge
            . "<a href='" . $edit_url . "'><i class='fa fa-edit'></i></a>&nbsp;"
            . '<span class="red ace-icon bigger-130 change-status" 
                id="status_' . encrypt($record->id) . '" 
                data-status="' . encrypt($record->status) . '" 
                data-fid="' . encrypt($record->id) . '" 
                data-action="status" 
                style="cursor:pointer;" 
                title="Delete Record">
                <i class="fa fa-trash"></i>
              </span>'
            . '</div>';

        $data_arr[] = [
            "no_of_players" => $record->no_of_players,
            "player_name" => $player_name,
            "console_name" => $console_name,
            "hours" => $record->hours ?? '00:00',
            "date" => $date,
            "start_time" => $start_display,
            "end_time" => $end_display,
            "total_hours" => $record->total_hours,
            "total_cost" => $record->total_cost,
            "discount" => $record->discount,
            "fandb_cost" => $record->fandb_cost,
            "final_cost" => $record->final_cost,
            "payment_type" => $record->GetPaymentInfo->payment_type ?? '',
            "action" => $action
        ];
    }

    $totalHours = floor($totalMinutes / 60) . ':' . sprintf('%02d', $totalMinutes % 60);

    return response()->json([
        "draw" => intval($draw),
        "iTotalRecords" => $totalRecords,
        "iTotalDisplayRecords" => $totalRecordswithFilter,
        "aaData" => $data_arr,
        "totalCostSum" => $totalCostSum,
        "discount" => $discount,
        "final_cost" => $final_cost,
        "fandb_cost" => $fandb_cost,
        "totalHours" => $totalHours
    ]);
}

}