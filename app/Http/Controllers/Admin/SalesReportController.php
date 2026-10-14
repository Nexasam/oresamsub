<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SalesReportRequest;
use App\Models\Network;
use App\Models\ProductPlan;
use App\Models\ProductPlanCategory;
use App\Models\User;
use App\Services\Reports\SalesReportService;
use Illuminate\Contracts\View\View;

class SalesReportController extends Controller
{
    public function __invoke(SalesReportRequest $request, SalesReportService $service): View
    {
        $filters = $request->filters();

        return view('admin.reports.sales-calculator', [
            'report' => $service->generate($filters),
            'filters' => $filters,
            'users' => User::query()
                ->select(['id', 'username', 'first_name', 'last_name', 'email', 'phone_number'])
                ->orderBy('username')
                ->get(),
            'networks' => Network::query()
                ->select(['id', 'network_name'])
                ->where('visibility', 1)
                ->orderBy('network_name')
                ->get(),
            'categories' => ProductPlanCategory::query()
                ->select(['id', 'product_plan_category_name', 'network_id', 'product_id'])
                ->with(['network:id,network_name', 'product:id,slug,product_name'])
                ->where('visibility', 1)
                ->orderBy('product_plan_category_name')
                ->get(),
            'plans' => ProductPlan::query()
                ->select(['id', 'product_plan_name', 'api_id', 'product_plan_category_id', 'data_size_in_mb', 'validity_in_days'])
                ->where('visibility', 1)
                ->orderBy('product_plan_name')
                ->get(),
            'periods' => [
                'today' => 'Today',
                'yesterday' => 'Yesterday',
                'week' => '7 Days',
                'month' => '30 Days',
                'custom' => 'Custom',
            ],
            'productTypes' => [
                'data' => 'Data',
                'airtime' => 'Airtime',
                'cable_subscription' => 'Cable TV',
                'utility_bills' => 'Electricity',
            ],
        ]);
    }
}
