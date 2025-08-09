<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Network;
use App\Models\CouponCode;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\FundingOption;
use Illuminate\Validation\Rule;
use App\Models\WalletFundingPromo;
use Illuminate\Support\Facades\DB;
use App\Models\ProductPlanCategory;
use App\Models\UserWalletFundingPromo;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class DailyCustomerFollowupController extends Controller
{
    public function index(Request $request){   

        return view('admin.daily_customer_followup.index');
    }


public function filterooo(Request $request)
{
    $data = $request->validate([
        'type' => 'required|in:generic,pos,both',
        'transaction_status' => 'required|in:atleast_one_transaction,no_transaction',
        'transaction_metric' => 'nullable|in:atleast_x_days,x_days',
        'days' => 'nullable|integer|min:1',
    ]);

    // Base user query filtering customer_category
    $query = User::query();

    if ($data['type'] === 'both') {
        $query->whereIn('customer_category', ['generic', 'pos']);
    } else {
        $query->where('customer_category', $data['type']);
    }

    // For no transaction filter, simple doesntHave works
    if ($data['transaction_status'] === 'no_transaction') {
        $query->doesntHave('transactions');
    } elseif ($data['transaction_status'] === 'atleast_one_transaction') {
        // Users with at least one transaction

        // If no days or metric, just filter those with transactions
        if (empty($data['days']) || empty($data['transaction_metric'])) {
            $query->has('transactions');
        } else {
            $daysAgo = Carbon::now()->subDays($data['days'])->startOfDay();

            // Join to subquery that gets last transaction date per user
            $lastTxSub = DB::table('transactions')
            ->select('user_id', DB::raw('MAX(created_at) as last_transaction_date'))
            ->groupBy('user_id');

            $query->joinSub($lastTxSub, 'last_tx', function ($join) {
            $join->on('users.id', '=', 'last_tx.user_id');
            });

            // Filter based on metric
            if ($data['transaction_metric'] === 'atleast_x_days') {
            $query->where('last_tx.last_transaction_date', '<=', $daysAgo);
            } else if ($data['transaction_metric'] === 'x_days') {
            $targetDate = $daysAgo->toDateString();
            $query->whereDate('last_tx.last_transaction_date', '=', $targetDate);
            }

            $query->select('users.*')->distinct();
        }
    }

    $results = $query->get() ?? collect([]);

    // $results = User::all(); // simple all users, no joins

    // dd($results->toArray()); // inspect output

   

    return view('admin.daily_customer_followup.index',compact('results'))->withInput();

}


public function filteroolll(Request $request)
{
    $data = $request->validate([
        'type' => 'required|in:generic,pos,both',
        // 'transaction_status' => 'required|in:atleast_one_transaction,no_transaction',
        // 'transaction_metric' => 'nullable|in:atleast_x_days,x_days',
        'days' => 'nullable|integer|min:1',
    ]);

    

    // Base query on Transaction, eager loading user
    $query = Transaction::with('user');

    // Filter transactions by user.customer_category according to 'type'
    $query->whereHas('user', function ($q) use ($data) {
        if ($data['type'] === 'both') {
            $q->whereIn('customer_category', ['generic', 'pos']);
        } else {
            $q->where('customer_category', $data['type']);
        }
    });

    // Handle transaction_status filter
    if ($data['transaction_status'] === 'no_transaction') {
        // No transactions means no transactions at all
        // But starting from Transaction model, no records means no results
        // So here, you must fetch users with no transactions separately
        // Or return empty collection because transactions exist in query.

        // You can handle 'no_transaction' outside this Transaction-based query,
        // like fetching users with no transactions directly.

        return view('your.view.name', ['results' => collect()]); // empty collection
    }

    // For atleast_one_transaction, filter transactions by metric if specified
    if ($data['transaction_status'] === 'atleast_one_transaction') {
        if (!empty($data['days']) && !empty($data['transaction_metric'])) {
            $daysAgo = Carbon::now()->subDays($data['days'])->startOfDay();

            if ($data['transaction_metric'] === 'atleast_x_days') {
                // Transactions on or before $daysAgo
                $query->where('created_at', '<=', $daysAgo);
            } elseif ($data['transaction_metric'] === 'x_days') {
                $targetDate = $daysAgo->toDateString();
                $query->whereDate('created_at', '=', $targetDate);
            }
        }
    }

    // Get transactions with user eager loaded
    $results = $query->latest()->get();

    // dd($results);
    return $results;

    return view('admin.daily_customer_followup.index',compact('results'))->withInput();

}

public function filter(Request $request){
    $validator = Validator::make($request->all(), [
        'type' => 'required|in:generic,pos,both',
        // 'transaction_status' => 'required|in:atleast_one_transaction,no_transaction',
        // 'transaction_metric' => 'nullable|in:atleast_x_days,x_days',
        'days_since_last_txn' => 'nullable|integer|min:1',
    ]);
    
         
    if ($validator->stopOnFirstFailure()->fails()) {
        return response()->json(['status'=>'-1', 'message'=>$validator->errors()->first(),'data' => $request->all() ]);
    }

    $data = $request->all();

    // $days =

    $check_category = $data['type'] == 'both' ? '' : $data['type'];
    $users = User::with('latestTransaction')->when($check_category !== '',function($q) use ($check_category){
        $q->where('customer_category', $check_category);
    })
    ->get();
    $dataa['users'] = $users;
    $dataa['days'] = $data['days_since_last_txn'] ?? 0;

    return $users;
    
    return view('admin.daily_customer_followup.index',compact('users'))->with($dataa);

}






}
