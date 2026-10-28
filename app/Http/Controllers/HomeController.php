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
        'from_date' => $request->input('from_date', $today->copy()->startOfYear()->toDateString()),
        'to_date' => $request->input('to_date', $today->copy()->endOfMonth()->toDateString()),
        'group_by' => $request->input('group_by', 'month'),
    ]);
    $filters = $request->validate([
        'from_date' => 'required|date_format:Y-m-d|before_or_equal:to_date',
        'to_date' => 'required|date_format:Y-m-d|after_or_equal:from_date',
        'console_id' => 'nullable|integer',
        'group_by' => 'required|in:day,month,year',
    ]);

    $startDate = Carbon::createFromFormat('Y-m-d', $filters['from_date']);
    $endDate = Carbon::createFromFormat('Y-m-d', $filters['to_date']);
    $groupBy = $filters['group_by'];
    $intervalRanges = [
        'day' => [
            'from' => $today->copy()->startOfMonth()->toDateString(),
            'to' => $today->copy()->endOfMonth()->toDateString(),
        ],
        'month' => [
            'from' => $today->copy()->startOfYear()->toDateString(),
            'to' => $today->copy()->endOfMonth()->toDateString(),
        ],
        'year' => [
            'from' => $today->copy()->startOfYear()->subYear()->toDateString(),
            'to' => $today->copy()->endOfYear()->toDateString(),
        ],
    ];
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

    $groupedCosts = [];
    foreach ($dailyCosts as $dateKey => $dailyTotals) {
        $date = Carbon::createFromFormat('Y-m-d', $dateKey);
        $groupKey = $groupBy === 'year' ? $date->format('Y') : ($groupBy === 'month' ? $date->format('Y-m') : $dateKey);

        if (!isset($groupedCosts[$groupKey])) {
            $groupedCosts[$groupKey] = ['player' => 0, 'food_and_beverage' => 0];
        }

        $groupedCosts[$groupKey]['player'] += (float) $dailyTotals->daily_cost;
        $groupedCosts[$groupKey]['food_and_beverage'] += (float) $dailyTotals->daily_fandb_cost;
    }

    $chartStartDate = $startDate->copy();
    $chartEndDate = $endDate->copy();
    if ($groupBy === 'month') {
        $chartStartDate->startOfMonth();
        $chartEndDate->startOfMonth();
    } elseif ($groupBy === 'year') {
        $chartStartDate->startOfYear();
        $chartEndDate->startOfYear();
    }

    $labels = [];
    $costs = [];
    $foodAndBeverageCosts = [];
    $periodTotal = 0;
    $foodAndBeveragePeriodTotal = 0;

    for ($date = $chartStartDate->copy(); $date->lte($chartEndDate);) {
        if ($groupBy === 'year') {
            $groupKey = $date->format('Y');
            $labels[] = $date->format('Y');
            $date->addYear();
        } elseif ($groupBy === 'month') {
            $groupKey = $date->format('Y-m');
            $labels[] = $date->format('M Y');
            $date->addMonth();
        } else {
            $groupKey = $date->toDateString();
            $labels[] = $date->format('d M Y');
            $date->addDay();
        }

        $dailyCost = (float) ($groupedCosts[$groupKey]['player'] ?? 0);
        $dailyFoodAndBeverageCost = (float) ($groupedCosts[$groupKey]['food_and_beverage'] ?? 0);
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
        'groupBy' => $groupBy,
        'intervalRanges' => $intervalRanges,
    ]);
}



   
}