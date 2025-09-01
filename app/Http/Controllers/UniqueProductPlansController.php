<?php

namespace App\Http\Controllers;

use App\Models\ProductPlan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\UniqueProductPlan;

class UniqueProductPlansController extends Controller
{
    public function index(Request $request)
    {
       
    
        return view('admin.unique_product_plans.index');
    }


    public function fetch(Request $request){

       
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

            $data[$keyy]['id'] = $productplan->id;
            $data[$keyy]['unique_plan'] = $productplan->product_plan_name;
            $data[$keyy]['product_id'] = $productplan->product_id;
            $data[$keyy]['network_id'] = $productplan->network_id;
            $data[$keyy]['data_size_in_mb'] = $productplan->data_size_in_mb;
            $data[$keyy]['validity_in_days'] = $productplan->validity_in_days;
            $data[$keyy]['visibility'] = $productplan->visibility;
            
            
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

        $data = collect($data);
        // return $data;

        return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('DT_RowIndex',function($data){
        return $data['id'];
        })
        ->addColumn('product_id',function($data){
            return $data['product_id'];
        })
        ->addColumn('size',function($data){
           return $data['data_size_in_mb'];
        })
        ->addColumn('validity',function($data){
            return $data['validity_in_days'];
         })
        ->addColumn('network_id',function($data){
            return $data['network_id'];
         })
         ->addColumn('automations', function ($data) {
            // $rows = [];
        
            // foreach ($data['automations'] as $a) {
            //     $rows[] = $a['automation'] . ' (' . $a['network'] . ', ' . $a['size'] . 'MB, ' . $a['validity'] . ' day(s))';
            // }
        
            // return implode('<br>', $rows); // show each on new line
            return 1;
        })
         ->addColumn('visibility',function($data){
           $visibility = $data['visibility'];

           return $visibility;
         })     
        ->escapeColumns([])
        ->make(true);


    }
    

}
