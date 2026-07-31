<?php

namespace App\Http\Controllers;

use App\Models\Automation;
use App\Models\FundingOption;
use App\Models\Transaction;
use App\Services\BusinessProfitReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessProfitController extends Controller
{
    public function __invoke(Request $request, BusinessProfitReportService $reports): View|JsonResponse
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'category' => ['nullable', 'string', 'max:100'],
            'automation_id' => ['nullable', 'uuid', 'exists:automations,id'],
            'funding_provider' => ['nullable', 'string', 'max:100'],
            'format' => ['nullable', 'in:html,json'],
        ]);
        $report = $reports->generate($validated);

        if (($validated['format'] ?? 'html') === 'json') {
            return response()->json($report);
        }

        return view('admin.profit.index', [
            'report' => $report,
            'automations' => Automation::orderBy('automation_name')->get(['id', 'automation_name']),
            'categories' => Transaction::whereNotNull('transaction_category')->distinct()->orderBy('transaction_category')->pluck('transaction_category'),
            'fundingProviders' => FundingOption::orderBy('funding_option_name')->get(['slug', 'funding_option_name']),
        ]);
    }
}
