<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TransactionController extends Controller
{
    //

    

  public function user_fetch_transactions(Request $request){

        // => function($query){
        //   $query->where('slug','data');
        // }
        //   $data = Transaction::with(['product','automation','network'])->latest()->get();
      $data = Transaction::with(['user','product_plan'])->latest()->limit(4000)->get();

        //  return $data;
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
        ->addColumn('wallet_category',function($data){
            $wallet_category = $data->wallet_category == 'main_wallet' ?  'MAIN' : 'DATA_WALLET';
            return $wallet_category;
        })
        ->addColumn('phone_number',function($data){
            return $data->phone_number;
        }) 
       ->addColumn('amount',function($data){
        return '&#8358;'.(number_format($data->amount,2));
        }) 
        ->addColumn('balance_before',function($data){
            return $data->wallet_category == 'main_wallet' ? '₦'.number_format($data->balance_before,2) : number_format($data->balance_before).'MB';

        })  
        ->addColumn('data_size',function($data){
         $data_size = number_format($data->product_plan->data_size_in_mb ?? '0') .' MB';
         return $data_size;
        })  
        ->addColumn('balance_after',function($data){
        return $data->wallet_category == 'main_wallet' ? '₦'.number_format($data->balance_after,2) : number_format($data->balance_after).'MB';
        })  
        ->addColumn('status',function($data){
            if($data->status == 1){
                $status_display = '<span class="badge bg-success text-white">Success</span>';
            }
            elseif($data->status == -1){
                $status_display = '<span class="badge bg-danger text-white">Failed</span>';
            }
            elseif($data->status == 0){
                $status_display = '<span class="badge bg-warning text-white">Pending</span>';
            }
            elseif($data->status == 2){
                $status_display = '<span class="badge bg-primary text-white">Refunded</span>';
            }
            elseif($data->status == 3){
                $status_display = '<span class="badge bg-gray text-white">Processing</span>';
            }else{
                $status_display = '<span class="badge bg-gray text-white">Unknown</span>';
            }
           return $status_display;  
        }) 
        ->addColumn('created_at',function($data){
            return $data->created_at;
        }) 
        ->escapeColumns([])
        ->make(true);

  }

  public function admin_fetch_transactions(Request $request){

  $data = Transaction::with(['user','product_plan'])->latest()->get();

    //  return $data;
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
    ->addColumn('wallet_category',function($data){
        $wallet_category = $data->wallet_category == 'main_wallet' ?  'MAIN' : 'DATA_WALLET';
        return $wallet_category;
    })
    ->addColumn('phone_number',function($data){
        return $data->phone_number;
    }) 
   ->addColumn('amount',function($data){
    return '&#8358;'.(number_format($data->amount,2));
    }) 
    ->addColumn('balance_before',function($data){
        return $data->wallet_category == 'main_wallet' ? '₦'.number_format($data->balance_before,2) : number_format($data->balance_before).'MB';

    })  
    ->addColumn('data_size',function($data){
     $data_size = number_format($data->product_plan->data_size_in_mb ?? '0') .' MB';
     return $data_size;
    })  
    ->addColumn('balance_after',function($data){
    return $data->wallet_category == 'main_wallet' ? '₦'.number_format($data->balance_after,2) : number_format($data->balance_after).'MB';
    })  
    ->addColumn('status',function($data){
        if($data->status == 1){
            $status_display = '<span class="badge bg-success text-white">Success</span>';
        }
        elseif($data->status == -1){
            $status_display = '<span class="badge bg-danger text-white">Failed</span>';
        }
        elseif($data->status == 0){
            $status_display = '<span class="badge bg-warning text-white">Pending</span>';
        }
        elseif($data->status == 2){
            $status_display = '<span class="badge bg-primary text-white">Refunded</span>';
        }
        elseif($data->status == 3){
            $status_display = '<span class="badge bg-gray text-white">Processing</span>';
        }else{
            $status_display = '<span class="badge bg-gray text-white">Unknown</span>';
        }
       return $status_display;  
    }) 
    ->addColumn('created_at',function($data){
        return $data->created_at;
    }) 
    ->escapeColumns([])
    ->make(true);

}
}
