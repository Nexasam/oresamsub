<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarketersController extends Controller
{


    public function index()
    {
        return view('oresamsub.marketers.dashboard');
    }

    public function stats(Request $request){
    $user = Auth::user();

    $startDate = $request->input('start_date') ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
    $endDate   = $request->input('end_date') ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();
    $search    = $request->input('search');

    $referrals = $user->referrals()->pluck('id');

    $totalRefs = $user->referrals()->count();
    $totalTxns = Transaction::whereIn('user_id', $referrals)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count();

    $usersQuery = $user->referrals()
        ->withCount([
            'transactions as total_txn_month' => function ($q) {
                $q->whereMonth('created_at', now()->month);
            },
            'transactions as total_txn_today' => function ($q) {
                $q->whereDate('created_at', now()->toDateString());
            }
        ]);

    if ($search) {
        $usersQuery->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    $users = $usersQuery->get(['id', 'first_name', 'phone_number', 'created_at']);

    return response()->json([
        'totalRefs' => $totalRefs,
        'totalTxns' => $totalTxns,
        'userTarget' => $user->user_target,
        'txnTarget'  => $user->txn_target,
        'users'      => $users,
    ]);
}



    // public function index(Request $request){
    //     $marketer = auth()->user();

    //     // Date filter (custom range or default = current month)
    //     $from = $request->input('from', now()->startOfMonth());
    //     $to   = $request->input('to', now()->endOfMonth());


    //     // Referrals count
    //     $totalAllTimeReferrals = $marketer->referrals()->count();
    //     $totalAllTimeReferrals2 = User::where('upline_id', $marketer->id)->count();
    //     $refUserIds = User::where('upline_id', $marketer->id)->pluck('id');

    //     $totalAllTimeTransactions = Transaction::whereIn('user_id', $refUserIds)
    //     ->count();

    //     $totalTransactionsThisMonth = Transaction::whereIn('user_id', $refUserIds)
    //     ->whereBetween('created_at', [$from, $to])
    //     ->count();

    //     // Targets (assuming stored on marketer’s profile)  #TODO
    //     $userTarget = 70;
    //     $txnTarget  = 5000;

    //     // List of referred users
    //     $referredUsers = User::where('upline_id', $marketer->id)
    //     ->withCount([
    //         'transactions as total_success_txn_month' => function ($q) use ($from, $to) {
    //             $q->whereBetween('created_at', [$from, $to])
    //             ->where('status', 1)
    //             ->where('set_for_manual', 1);
    //         },
    //         'transactions as total_success_txn_today' => function ($q) {
    //             $q->whereDate('created_at', today())
    //             ->where('status', 1)
    //             ->where('set_for_manual', 1);
    //         },
    //     ])
    //     ->get();

    //     $data['refs'] = $totalAllTimeReferrals;
    //     $data['refs2'] = $totalAllTimeReferrals2;
    //     $data['all_time_txns'] = $totalAllTimeTransactions;
    //     $data['txns_this_month'] = $totalTransactionsThisMonth;
    //     $data['marketerTarget'] = $refs2;
    //     $data['txnsTarget'] = $refs2;

    //     return view('marketers.dashboard', compact(
    //         'totalReferrals', 'totalTransactions',
    //         'userTarget', 'txnTarget',
    //         'users', 'from', 'to'
    //     ));


        
    // }




}
