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
            $data[$keyy]['product_id'] = $productplan->product->product_id;
            $data[$keyy]['network_id'] = $productplan->network->network_id;
            $data[$keyy]['data_size_in_mb'] = $productplan->data_size_in_mb;
            $data[$keyy]['validity_in_days'] = $productplan->validity_in_days;
            $data[$keyy]['visibility'] = $productplan->visibility;
            $data[$keyy]['cost_price'] = $productplan->cost_price;
            $data[$keyy]['price_1'] = $productplan->price_1;
            $data[$keyy]['price_2'] = $productplan->price_2;
            $data[$keyy]['price_3'] = $productplan->price_3;
            $data[$keyy]['price_4'] = $productplan->price_4;
            $data[$keyy]['price_5'] = $productplan->price_5;
            $data[$keyy]['price_6'] = $productplan->price_6;
            $data[$keyy]['price_7'] = $productplan->price_7;
            $data[$keyy]['price_8'] = $productplan->price_8;
            $data[$keyy]['price_9'] = $productplan->price_9;
            $data[$keyy]['price_10'] = $productplan->price_10;
            $data[$keyy]['price_11'] = $productplan->price_11;
            $data[$keyy]['price_12'] = $productplan->price_12;
            
            
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

        $datad = collect($data);
        // return $data;

        return DataTables::of($datad)
        ->addIndexColumn()
        ->addColumn('DT_RowIndex',function($datad){
        return $datad['id'];
        })
        ->addColumn('product_id',function($datad){
            $unique_plan = $datad['unique_plan'];
            $price_1 = $datad['price_1'];
            $price_2 = $datad['price_2'];
            $price_3 = $datad['price_3'];
            $price_4 = $datad['price_4'];
            $price_5 = $datad['price_5'];
            $price_6 = $datad['price_6'];
            $price_7 = $datad['price_7'];
            $price_8 = $datad['price_8'];
            $price_9 = $datad['price_9'];
            $price_10 = $datad['price_10'];
            $price_11 = $datad['price_11'];
            $price_12 = $datad['price_12'];
           

            $id = $datad['id'];
            $unique_plan = htmlspecialchars($datad['unique_plan'], ENT_QUOTES, 'UTF-8');
            $cost_price = htmlspecialchars($datad['cost_price'] ?? '0', ENT_QUOTES, 'UTF-8'); // assuming cost_price field exists
          
            // return '
            //     <button 
            //         class="px-3 py-1 text-xs bg-indigo-600 text-white rounded hover:bg-indigo-700"
            //         onclick="openPricingModal(' . $id . ', \'' . $unique_plan . '\', \'' . $cost_price . '\')">
            //         Set Pricing
            //     </button>
            // ';

            return '
                '.$unique_plan.' <br>
                <button 
                    class="px-3 py-1 text-xs font-medium text-white bg-indigo-600 rounded hover:bg-indigo-700 focus:outline-none"
                    onclick="openPricingModal(
                        \''.addslashes($id).'\',
                        \''.addslashes($unique_plan).'\',
                        '.(float)$cost_price.',
                        ['.
                            implode(',', [
                                (float)$price_1, (float)$price_2, (float)$price_3, (float)$price_4,
                                (float)$price_5, (float)$price_6, (float)$price_7, (float)$price_8,
                                (float)$price_9, (float)$price_10, (float)$price_11, (float)$price_12
                            ])
                        .']
                    )"
                >
                    Manage Plan
                </button>
                ';

            
            
        
        

        })
        ->addColumn('size',function($datad){
           return $datad['data_size_in_mb'];
        })
        ->addColumn('validity',function($datad){
            return $datad['validity_in_days'];
         })
        ->addColumn('network_id',function($datad){
            return $datad['network_id'];
         })
         ->addColumn('automations', function ($datad) {
            // safety checks
            if (empty($datad['automations']) || !is_array($datad['automations'])) {
                return '<span class="text-gray-400 italic">No automation</span>';
            }
        
            // reindex to ensure numeric keys starting from 0
            $autos = array_values($datad['automations']);
            if (!isset($autos[0]) || !is_array($autos[0])) {
                return '<span class="text-gray-400 italic">No automation</span>';
            }
        
            // escape helper
            $esc = function($v) {
                return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
            };
        
            // first (summary) badge
            $first = $autos[0];
            $summary = '<span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 shadow-sm">'
                     . $esc($first['automation'] ?? 'N/A')
                     . ' <span class="ml-1 text-gray-600">(' . $esc($first['network'] ?? '-') . ')</span>'
                     . '</span>';
        
            $count = count($autos);
        
            // build badges for all automations (shown when expanded)
            $allBadges = '';
            foreach ($autos as $a) {
                $allBadges .= '<div><span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-blue-50 text-blue-800 shadow-sm">'
                            . $esc($a['automation'] ?? 'N/A')
                            . ' <span class="ml-1 text-gray-600">('
                            . $esc($a['network'] ?? '-')
                            . ' · ' . $esc($a['size'] ?? '-') . 'MB · ' . $esc($a['validity'] ?? '-') . 'd)</span>'
                            . '</span></div>';
            }
        
            // return summary with togglable vertical list (default open)
            return '
              <div x-data="{ open: true }" class="flex flex-col">
                <div class="flex items-center">
                  <div>' . $summary . '</div>
                  ' . ($count > 1
                      ? '<button @click="open = !open" class="ml-2 text-xs text-indigo-600 hover:underline focus:outline-none"
                               x-text="open ? \'Hide\' : \'' . ($count - 1) . ' more\'"></button>'
                      : ''
                    ) . '
                </div>
                <div x-show="open" x-cloak x-transition class="mt-2 flex flex-col space-y-1">
                    ' . $allBadges . '
                </div>
              </div>
            ';
        })
        
        ->addColumn('visibility', function ($datad) {
            $rows = [];
        
            $visibility = $datad['visibility'] ?? null;
        
            if ($visibility === '1' || $visibility === 1) {
                $rows[] = '<span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 shadow-sm">Visible</span>';
            } elseif ($visibility === '0' || $visibility === 0) {
                $rows[] = '<span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 shadow-sm">Hidden</span>';
            } else {
                $rows[] = '<span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600 shadow-sm">Unknown</span>';
            }
        
            return implode('<br>', $rows);
        })
        ->escapeColumns([])
        ->make(true);


    }


    public function save_unique_plan_pricing(Request $request){
        return response()->json([
            'success' =>true,
            'message' => 'test'
        ]);
    }
    

}
