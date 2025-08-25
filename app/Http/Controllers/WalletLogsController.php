<?php

namespace App\Http\Controllers;

use App\Models\WalletLog;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;


class WalletLogsController extends Controller
{
    public function index(){

        // $logs = WalletLog::all();
        return view('admin.wallet_logs.index');
        
    }

    public function admin_fetch_wallet_logs(Request $request){

        // $date_from = $request->date_from ?? date('Y-m-d');
        // date('Y-m-d', strtotime('-10 days'))
        $date_from = $request->date_from ?? '';

        // ?? date('Y-m-d')
        $date_to= $request->date_to ?? '';

        $product_plan_category_filter = $request->product_plan_category_filter ?? '';
        
        $phone = $request->phone_recharged ?? '';
    
        $limit = $request->limit ?? 400;

        
        $data = WalletLog::with('user','transaction','actionBy')->when(!empty($date_from) && !empty($date_to) , function ($query) use ($date_from,$date_to){
            $date_to = date('Y-m-d', strtotime('+1 day', strtotime($date_to)));
            $query->where('created_at','>=',$date_from)->where('created_at','<=',$date_to);
        })
        ->latest()
        ->limit($limit)
        ->get();

        return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('DT_RowIndex',function($data){
        return $data->id;
        })
        ->addColumn('user_id',function($data){
            $first_name = $data->user->first_name  ?? 'nil';
            $last_name = $data->user->last_name  ?? 'nil';
            $phone_number = $data->user->phone_number  ?? 'nil';
            $user_details =  $first_name.'<br>'.$last_name.'<br>'.$phone_number.'<br>';
            return $user_details;
        })
        ->addColumn('transaction_id',function($data){
           return $data->transaction_id;
        })
        ->addColumn('action_by',function($data){
            $first_name = $data->actionBy->first_name  ?? 'nil';
            $last_name = $data->actionBy->last_name  ?? 'nil';
            $phone_number = $data->actionBy->phone_number  ?? 'nil';
            $user_details =  $first_name.'<br>'.$last_name.'<br>'.$phone_number.'<br>';
            return $user_details;
        })
        ->addColumn('transaction_category',function($data){
            return $data->transaction_category;
         })
         ->addColumn('balance_before',function($data){
            return '₦' . number_format($data->balance_before, 2);

         })
         ->addColumn('balance_after',function($data){
            // return $data->balance_after;
            return '₦' . number_format($data->balance_after, 2);


         })
    
         ->addColumn('description',function($data){
            return $data->description;
         })
     
        ->addColumn('created_at',function($data){
            $cat = $data->created_at;
          

            return $cat;
        }) 
        ->addColumn('action',function($data){
            $route = route('transactions.transaction_details',$data->id);
            $actionBtn = '<a href="'.$route.'" type="button" class="hs-dropdown-toggle ti-btn ti-btn-primary" data-hs-overlay="#hs-vertically-centered-scrollable-modal'.$data->email.'">
            Details
            </a>';
            return $actionBtn;
        })
        ->escapeColumns([])
        ->make(true);


  }
}
