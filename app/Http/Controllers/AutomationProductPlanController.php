<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AutomationProductPlan;

class AutomationProductPlanController extends Controller
{
    /**
     * Store a new automation provider for a product plan
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_plan_id' => 'required|exists:product_plans,id',
            'automation_id'   => 'required|exists:automations,id',
            'priority'        => 'required|integer|min:1',
            'cost_price'      => 'required|numeric|min:0',
            'selling_price'   => 'nullable|numeric|min:0',
            'provider_plan_id'   => 'required',
            'is_active' => 'required|boolean',
        ]);

        // 🚫 prevent duplicate provider for same plan
        $exists = AutomationProductPlan::where('product_plan_id', $request->product_plan_id)
            ->where('automation_id', $request->automation_id)
            ->exists();

        if ($exists) {
            return back()->with('failure', 'This provider already exists for this product plan.');
        }

        // 🔥 create provider
        $provider = AutomationProductPlan::create([
            'product_plan_id' => $request->product_plan_id,
            'automation_id'   => $request->automation_id,
            'priority'        => $request->priority,
            'cost_price'      => $request->cost_price,
            'is_active'      => $request->is_active,
            'provider_plan_id'      => $request->provider_plan_id,
            'selling_price'   => $request->selling_price,
            // 'is_active'       => true,
        ]);

        return back()->with('success', 'Provider added successfully.');
    }

    public function storenew(Request $request)
    {
        $data = $request->validate([
            'product_plan_id' => 'required',
            'automation_id' => 'required',
            'provider_plan_id' => 'required',
            'priority' => 'required|integer',
            'status' => 'required|boolean',
            'cost_price' => 'nullable|numeric',
            'selling_price' => 'nullable|numeric',
        ]);

           // 🚫 prevent duplicate provider for same plan
           $exists = AutomationProductPlan::where('product_plan_id', $request->product_plan_id)
           ->where('automation_id', $request->automation_id)
           ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => "Exists already...Failed to create"
                ]);
            }

        $provider = AutomationProductPlan::create($data);

        return response()->json([
            'success' => true,
            'product_plan_id' => $provider->product_plan_id,
            'automation_name' => $provider->automation->automation_name,
            'priority' => $provider->priority,
            'cost_price' => $provider->cost_price,
            'selling_price' => $provider->selling_price,
        ]);
    }

    /**
     * Update provider (priority, pricing, status)
     */
    public function update(Request $request, $id)
    {
        $provider = AutomationProductPlan::findOrFail($id);
    
        $provider->update([
            'priority' => $request->priority,
            'cost_price' => $request->cost_price,
            'selling_price' => $request->selling_price,
            'is_active' => $request->is_active,
        ]);
    
        return back()->with('success', 'Provider updated successfully');
    }

    /**
     * Soft toggle provider status
     */
    public function toggle($id)
    {
        $provider = AutomationProductPlan::findOrFail($id);

        $provider->is_active = !$provider->is_active;
        $provider->save();

        return back()->with('success', 'Provider status updated.');
    }

    /**
     * Delete provider from plan
     */
    public function destroy($id)
    {
        $provider = AutomationProductPlan::findOrFail($id);
        $provider->delete();

        return back()->with('success', 'Provider removed successfully.');
    }
}