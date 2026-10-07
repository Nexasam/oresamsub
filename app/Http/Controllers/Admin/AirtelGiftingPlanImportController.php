<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Automation;
use App\Models\Network;
use App\Models\Product;
use App\Models\ProductPlanCategory;
use App\Services\ProductPlans\AirtelGiftingPlanImportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AirtelGiftingPlanImportController extends Controller
{
    public function index(AirtelGiftingPlanImportService $importer): View
    {
        return view('admin.product_plans.airtel-gifting-import', [
            'plansText' => old('plans_text', $importer->defaultText()),
            'options' => $this->defaultOptions(),
            'result' => null,
            'mode' => null,
        ] + $this->selectionData());
    }

    public function preview(Request $request, AirtelGiftingPlanImportService $importer): View
    {
        $payload = $this->validatedPayload($request);
        $plansText = $this->plansTextFromRequest($request, $importer);
        $result = $importer->preview($plansText, $payload);

        return view('admin.product_plans.airtel-gifting-import', [
            'plansText' => $plansText,
            'options' => $payload,
            'result' => $result,
            'mode' => 'preview',
        ] + $this->selectionData());
    }

    public function apply(Request $request, AirtelGiftingPlanImportService $importer): View
    {
        $payload = $this->validatedPayload($request);
        $plansText = $this->plansTextFromRequest($request, $importer);
        $result = $importer->execute($plansText, $payload);

        return view('admin.product_plans.airtel-gifting-import', [
            'plansText' => $plansText,
            'options' => $payload,
            'result' => $result,
            'mode' => 'apply',
        ] + $this->selectionData());
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedPayload(Request $request): array
    {
        $validated = $request->validate([
            'plans_text' => ['nullable', 'string'],
            'rows' => ['nullable', 'array'],
            'rows.*.public_id' => ['nullable', 'string', 'max:50'],
            'rows.*.name' => ['nullable', 'string', 'max:255'],
            'rows.*.code' => ['nullable', 'string', 'max:255'],
            'rows.*.size' => ['nullable', 'string', 'max:50'],
            'rows.*.validity' => ['nullable', 'string', 'max:50'],
            'rows.*.price' => ['nullable', 'string', 'max:50'],
            'network_id' => ['nullable', 'string'],
            'product_id' => ['nullable', 'string'],
            'category_id' => ['nullable', 'string'],
            'automation_id' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:120'],
            'source_slug' => ['nullable', 'string', 'max:120'],
            'source_name' => ['nullable', 'string', 'max:120'],
            'level_discount_percent_1' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'level_discount_percent_2' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'level_discount_percent_3' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'level_discount_percent_4' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'level_discount_percent_5' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'level_discount_percent_6' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'level_discount_percent_7' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'airtime_purchase_rate_per_100' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'update_existing' => ['nullable', 'boolean'],
        ]);

        return array_merge($this->defaultOptions(), $validated, [
            'update_existing' => $request->boolean('update_existing'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function defaultOptions(): array
    {
        return [
            'network_id' => '',
            'product_id' => '',
            'category_id' => '',
            'automation_id' => '',
            'category' => 'Airtel Gifting',
            'source_slug' => 'oresamplug',
            'source_name' => 'ORESAMPLUG AUTOMATION',
            'level_discount_percent_1' => 0.5,
            'level_discount_percent_2' => 0.5,
            'level_discount_percent_3' => 0.5,
            'level_discount_percent_4' => 0.5,
            'level_discount_percent_5' => 0.5,
            'level_discount_percent_6' => 0.5,
            'level_discount_percent_7' => 0.5,
            'airtime_purchase_rate_per_100' => 94,
            'update_existing' => false,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function selectionData(): array
    {
        return [
            'networks' => Network::query()->orderBy('network_name')->get(),
            'products' => Product::query()->orderBy('product_name')->get(),
            'categories' => ProductPlanCategory::query()
                ->with(['product', 'network', 'automation'])
                ->orderBy('product_plan_category_name')
                ->get(),
            'automations' => Automation::query()->orderBy('automation_name')->get(),
        ];
    }

    private function plansTextFromRequest(Request $request, AirtelGiftingPlanImportService $importer): string
    {
        $rows = $request->input('rows');

        if (is_array($rows) && count($rows) > 0) {
            return $importer->rowsToText($rows);
        }

        return (string) $request->input('plans_text', '');
    }
}
