<?php

namespace App\Http\Controllers;

use App\Models\ProductPlan;
use Illuminate\Http\Request;
use App\Models\UniqueProductPlan;

class UniqueProductPlansController extends Controller
{
    public function index(Request $request)
    {
        $query = UniqueProductPlan::query()
            ->orderByRaw("CASE WHEN data_size_in_mb < 500 THEN 1 ELSE 0 END ASC")
            ->orderBy('data_size_in_mb', 'asc')
            ->orderBy('validity_in_days', 'asc')
            ->orderBy('network_id', 'asc');
    
        // Filters
        if ($request->filled('size')) {
            $query->where('data_size_in_mb', $request->size);
        }
        if ($request->filled('network')) {
            $query->where('network_id', $request->network);
        }
        if ($request->filled('validity')) {
            $query->where('validity_in_days', $request->validity);
        }
    
        $generalproductplans = $query->paginate(10); // paginate instead of get()
    
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
    
        if ($request->ajax()) {
            return response()->json([
                'plans' => $data,
                'pagination' => [
                    'total' => $generalproductplans->total(),
                    'per_page' => $generalproductplans->perPage(),
                    'current_page' => $generalproductplans->currentPage(),
                    'last_page' => $generalproductplans->lastPage(),
                ]
            ]);
        }
    
        return view('admin.unique_product_plans.index', compact('data'));
    }
    

}
