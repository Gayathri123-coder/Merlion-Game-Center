<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\JobOrder;
use Auth;
use App\JobOrderTrades;
use App\TcQuotation;
use App\CwQuotation;
use App\CwFBQ;
use App\TcFBQ;
use DB;
use App\User;
use App\ConsoleMaster;
use Carbon\Carbon;

class HomeController extends Controller
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
 public function index(Request $request)
{
    $placeId = session('place_id');
    if (!$placeId) {
        return redirect()->route('place_master')
            ->with('info', 'Please add your first Branch to continue');
    }

    $today = Carbon::today();
    $request->merge([
        'from_date' => $request->input('from_date', $today->copy()->startOfMonth()->toDateString()),
        'to_date' => $request->input('to_date', $today->toDateString()),
    ]);
    $filters = $request->validate([
        'from_date' => 'required|date_format:Y-m-d|before_or_equal:to_date',
        'to_date' => 'required|date_format:Y-m-d|after_or_equal:from_date',
        'console_id' => 'nullable|integer',
    ]);

    $startDate = Carbon::createFromFormat('Y-m-d', $filters['from_date']);
    $endDate = Carbon::createFromFormat('Y-m-d', $filters['to_date']);
    $consoleList = ConsoleMaster::where('place_id', $placeId)
        ->where('status', 1)
        ->orderBy('console_name')
        ->get();
    $consoleId = $filters['console_id'] ?? null;

    if ($consoleId !== null && !$consoleList->contains('id', (int) $consoleId)) {
        return redirect()->route('home')
            ->withErrors(['console_id' => 'Please select a console from this branch.'])
            ->withInput();
    }

    $dailyCosts = DB::table('submission_data')
        ->join('console_master', 'console_master.id', '=', 'submission_data.console_id')
        ->where('console_master.place_id', $placeId)
        ->whereBetween('submission_data.date', [$startDate->toDateString(), $endDate->toDateString()])
        ->when($consoleId !== null, function ($query) use ($consoleId) {
            $query->where('submission_data.console_id', $consoleId);
        })
        ->select(
            'submission_data.date as submission_date',
            DB::raw('SUM(submission_data.total_cost) as daily_cost'),
            DB::raw('SUM(submission_data.fandb_cost) as daily_fandb_cost')
        )
        ->groupBy('submission_data.date')
        ->get()
        ->keyBy('submission_date');

    $labels = [];
    $costs = [];
    $foodAndBeverageCosts = [];
    $periodTotal = 0;
    $foodAndBeveragePeriodTotal = 0;

    for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
        $dateKey = $date->toDateString();
        $dailyTotals = $dailyCosts->get($dateKey);
        $dailyCost = (float) ($dailyTotals->daily_cost ?? 0);
        $dailyFoodAndBeverageCost = (float) ($dailyTotals->daily_fandb_cost ?? 0);

        $labels[] = $date->format('d M');
        $costs[] = $dailyCost;
        $foodAndBeverageCosts[] = $dailyFoodAndBeverageCost;
        $periodTotal += $dailyCost;
        $foodAndBeveragePeriodTotal += $dailyFoodAndBeverageCost;
    }

    return view('home', [
        'labels' => $labels,
        'costs' => $costs,
        'foodAndBeverageCosts' => $foodAndBeverageCosts,
        'periodTotal' => $periodTotal,
        'foodAndBeveragePeriodTotal' => $foodAndBeveragePeriodTotal,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'consoleList' => $consoleList,
        'selectedConsoleId' => $consoleId,
    ]);
}



   
}