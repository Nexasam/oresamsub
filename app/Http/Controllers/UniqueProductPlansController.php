<?php

namespace App\Http\Controllers;

use App\Models\ProductPlan;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\UniqueProductPlan;

class UniqueProductPlansController extends Controller
{
    public function index(Request $request)
    {
        // $query = UniqueProductPlan::query()
        //     ->orderByRaw("CASE WHEN data_size_in_mb < 500 THEN 1 ELSE 0 END ASC")
        //     ->orderBy('data_size_in_mb', 'asc')
        //     ->orderBy('validity_in_days', 'asc')
        //     ->orderBy('network_id', 'asc');
    
        // // Filters
   
        // // Filters
        // if ($request->filled('size') && is_numeric($request->size)) {
        //     $query->where('data_size_in_mb', $request->size);
        // }

        // if ($request->filled('network')) {
        //     $query->where('network_id', $request->network);
        // }

        // if ($request->filled('validity')) {
        //     $query->where('validity_in_days', $request->validity);
        // }

    
        // $generalproductplans = $query->paginate(10); // paginate instead of get()
    
        // $data = [];
        // foreach ($generalproductplans as $keyy => $productplan) {
        //     $size = $productplan->data_size_in_mb;
        //     $validity = $productplan->validity_in_days;
        //     $network_id = $productplan->network_id;
        //     $product_id = $productplan->product_id;
    
        //     $associated_automationplans = ProductPlan::with(['product_plan_category.network','product_plan_category.product','automation'])
        //         ->where('validity_in_days', $validity)
        //         ->where('data_size_in_mb', $size)
        //         ->get();
    
        //     $data[$keyy]['unique_plan'] = $productplan->product_plan_name;
        //     $dataa = [];
    
        //     foreach ($associated_automationplans as $key => $associated_automationplan) {
        //         $getnetworkid = $associated_automationplan->product_plan_category->network->id ?? null;
        //         $network_namee = $associated_automationplan->product_plan_category->network->network_name ?? 'nil';
        //         $productid = $associated_automationplan->product_plan_category->product->id ?? null;
        //         $sizee = $associated_automationplan->data_size_in_mb;
        //         $vall = $associated_automationplan->validity_in_days;
    
        //         if ($getnetworkid == $network_id && $productid == $product_id && $size == $sizee && $validity == $vall) {
        //             $dataa[$key] = [
        //                 'product_plan' => $associated_automationplan->product_plan_name,
        //                 'size' => $sizee,
        //                 'validity' => $vall,
        //                 'visibility' => $associated_automationplan->visibility,
        //                 'automation' => $associated_automationplan->automation->automation_name,
        //                 'network' => $network_namee,
        //             ];
        //         }
        //     }
    
        //     $data[$keyy]['automations'] = $dataa;
        // }
    
        // if ($request->ajax()) {
        //     return response()->json([
        //         'plans' => $data,
        //         'pagination' => [
        //             'total' => $generalproductplans->total(),
        //             'per_page' => $generalproductplans->perPage(),
        //             'current_page' => $generalproductplans->currentPage(),
        //             'last_page' => $generalproductplans->lastPage(),
        //         ]
        //     ]);
        // }
    
        return view('admin.unique_product_plans.index', compact('data'));
    }


    public function admin_fetch_unique_product_plans(Request $request){

       
        $query = UniqueProductPlan::query()
        ->orderByRaw("CASE WHEN data_size_in_mb < 500 THEN 1 ELSE 0 END ASC")
        ->orderBy('data_size_in_mb', 'asc')
        ->orderBy('validity_in_days', 'asc')
        ->orderBy('network_id', 'asc');

           
        // Filters
        if ($request->filled('size') && is_numeric($request->size)) {
            $query->where('data_size_in_mb', $request->size);
        }

        if ($request->filled('network')) {
            $query->where('network_id', $request->network);
        }

        if ($request->filled('validity')) {
            $query->where('validity_in_days', $request->validity);
        }


        $generalproductplans = $query->get(); // paginate instead of get()

        $data = [];
    
        foreach ($generalproductplans as $keyy => $productplan) {
            $size = $productplan->data_size_in_mb;
            $validity = $productplan->validity_in_days;
            $network_id = $productplan->network_id;
            $product_id = $productplan->product_id;

            $associated_automationplans = ProductPlan::with(['product_plan_category.network','product_plan_category.product','automation'])
                ->where('validity_in_days', $validity)
                ->where('data_size_in_mb', $size)
                ->get();

            $data[$keyy]['unique_plan'] = $productplan->product_plan_name;
            $dataa = [];

            foreach ($associated_automationplans as $key => $associated_automationplan) {
                $getnetworkid = $associated_automationplan->product_plan_category->network->id ?? null;
                $network_namee = $associated_automationplan->product_plan_category->network->network_name ?? 'nil';
                $productid = $associated_automationplan->product_plan_category->product->id ?? null;
                $sizee = $associated_automationplan->data_size_in_mb;
                $vall = $associated_automationplan->validity_in_days;

                if ($getnetworkid == $network_id && $productid == $product_id && $size == $sizee && $validity == $vall) {
                    $dataa[$key] = [
                        'product_plan' => $associated_automationplan->product_plan_name,
                        'size' => $sizee,
                        'validity' => $vall,
                        'visibility' => $associated_automationplan->visibility,
                        'automation' => $associated_automationplan->automation->automation_name,
                        'network' => $network_namee,
                    ];
                }
            }

            $data[$keyy]['automations'] = $dataa;
        }


        $data = (object) $data;

        return $data;
 
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
            // $route = route('transactions.transaction_details',$data->id);
            // $actionBtn = '<a href="'.$route.'" type="button" class="hs-dropdown-toggle ti-btn ti-btn-primary" data-hs-overlay="#hs-vertically-centered-scrollable-modal'.$data->email.'">
            // Details
            // </a>';
            return '';
        })
        ->escapeColumns([])
        ->make(true);


    }
    

}
