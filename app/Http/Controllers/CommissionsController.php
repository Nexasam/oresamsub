<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Network;
use App\Models\Commissions;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;

class CommissionsController extends Controller
{
    public function index(){

        //quick population seeding
        // $fetch_success_txns_ids = Transaction::where('status',1)->pluck('id')->toArray();
        // for($i=1; $i <= 3000; $i++ ){
        //     $txnid = $fetch_success_txns_ids[rand(0,count($fetch_success_txns_ids) - 1)];
        //     $commissionssss = Commissions::create([
        //         'transaction_id' => $txnid,
        //         'commission' => 20,
        //         'beneficiary' => auth()->id(),
        //         'transaction_by' => Transaction::where('id',$txnid)->first()->user_id,
        //     ]);
        // }
        //quick population seeding
    
        // dd('commissions here');
        $userid = auth()->id();
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        $limit = $request->limit ?? 500;
        $data['commissions'] = Commissions::when(auth()->user()->role->role_name == 'User',function($query) use ($userid){
            $query->where('beneficiary',$userid);
        })
        ->whereDate('created_at','>=',$start_date)
        ->whereDate('created_at','<=',$end_date)
        ->get();

        $data['pending_commissions_balance'] = Commissions::when(auth()->user()->role->role_name == 'User',function($query) use ($userid){
            $query->where('beneficiary',$userid);
        })
        ->where('payout_status',0)
        ->sum('commission');

        $data['total_commissions_balance'] = Commissions::
        when(auth()->user()->role->role_name == 'User',function($query) use ($userid){
            $query->where('beneficiary',$userid);
        })
        ->sum('commission');

        // dd($data);
        $data['user'] = auth()->user();
        return view('user.commissions.index')->with($data);
    }


    public function fetch_commissions(Request $request){
        
        $date_from = $request->date_from ?? date('Y-m-d', strtotime('-2 days'));
        
        $date_to= $request->date_to ?? date('Y-m-t');
 
        $limit = $request->limit ?? 5000;

        $rolee = strtolower(auth()->user()->role->role_name);
        
        $user = auth()->user();

        $data = Commissions::when(!empty($date_from) && !empty($date_to) , function ($query) use ($date_from,$date_to){
            $date_to = date('Y-m-d', strtotime('+1 day', strtotime($date_to)));
            $query->where('created_at','>=',$date_from)->where('created_at','<=',$date_to);
        })
        ->when( $rolee == 'user' , function ($query) use ($user){
            $query->where('beneficiary','<=',$user->id);
        })
        // ->where('payout_status')
        ->with(['beneficiary','transaction.user','transaction.product_plan'])
        ->limit($limit)
        ->latest()
        ->get();

        //  return $data;
        return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('DT_RowIndex',function($data){
            return $data->id;
            })
            ->addColumn('user_details',function($data){
                    $transaction_category = $data->transaction->transaction_category;
                    $dataa =  'Transaction By: '.$data->transaction->user->username.'<br>';
                    $dataa .=  'Beneficiary: '.$data->transaction->user->username.'<br>';
                    // $dataa .=  'Product Name By: '.$data->transaction->product_plan->product_plan_name.'<br>';
                    $dataa .=  'Transaction Category: '.$transaction_category.'<br>';
                    return '<span style="white-space: normal;word-wrap: break-word;word-break: normal;width:auto">'.$dataa.'</span>';
            })
            ->addColumn('commission',function($data){
                return $data->commission ?? 0;
            })
            ->addColumn('status',function($data){
                if($data->payout_status == 1){
                    $status_display = '<span class="badge bg-success text-white">Success</span>';
                }elseif($data->payout_status == 0){
                    $status_display = '<span class="badge bg-warning text-white">Pending</span>';
                }else{
                    $status_display = '<span class="badge bg-gray text-white">Unknown</span>';
                }
            return $status_display;  
            }) 
            ->addColumn('created_at',function($data){
                return $data->created_at;
            }) 
            ->addColumn('action',function($data){
                // $route = 'transactions.transaction_details';
                // $route = route('transactions.transaction_details',$data->id);
                // $actionBtn = '<a href="'.$route.'" type="button" class="hs-dropdown-toggle ti-btn ti-btn-primary" data-hs-overlay="#hs-vertically-centered-scrollable-modal'.$data->email.'">
                // Details
                // </a>';
                // return $actionBtn;

                return 'nil';
            })
            
            ->escapeColumns([])
            ->make(true);


       
  }

 
}
