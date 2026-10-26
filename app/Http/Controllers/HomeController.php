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
 public function index()
{
    $placeId = session('place_id');
    if (!$placeId) {
        return redirect()->route('place_master')
            ->with('info', 'Please add your first Branch to continue');
    }

    $endDate = Carbon::today();
    $startDate = $endDate->copy()->subDays(29);

    $dailyCosts = DB::table('submission_data')
        ->join('console_master', 'console_master.id', '=', 'submission_data.console_id')
        ->where('console_master.place_id', $placeId)
        ->whereBetween('submission_data.date', [$startDate->toDateString(), $endDate->toDateString()])
        ->select('submission_data.date as submission_date', DB::raw('SUM(submission_data.total_cost) as daily_cost'))
        ->groupBy('submission_data.date')
        ->pluck('daily_cost', 'submission_date');

    $labels = [];
    $costs = [];
    $periodTotal = 0;

    for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
        $dateKey = $date->toDateString();
        $dailyCost = (float) ($dailyCosts->get($dateKey) ?: 0);

        $labels[] = $date->format('d M');
        $costs[] = $dailyCost;
        $periodTotal += $dailyCost;
    }

    return view('home', [
        'labels' => $labels,
        'costs' => $costs,
        'periodTotal' => $periodTotal,
        'startDate' => $startDate,
        'endDate' => $endDate,
    ]);
}



   
}