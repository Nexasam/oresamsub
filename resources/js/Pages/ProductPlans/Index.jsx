import { useState } from "react";
import { usePage, Link } from "@inertiajs/react";
import axios from "axios";
import Swal from "sweetalert2";
import DashboardLayout from "@/Layouts/DashboardLayout";

export default function Index() {
  const { productPlans, userAutomations } = usePage().props;

  const [openId, setOpenId] = useState(null);
  const [loading, setLoading] = useState(false);
  const [search, setSearch] = useState("");

  const [formState, setFormState] = useState({});

  const setPlanState = (planId, key, value) => {
    setFormState((prev) => ({
      ...prev,
      [planId]: {
        ...(prev[planId] || {}),
        [key]: value,
      },
    }));
  };

  const getPlanState = (planId) => {
    return (
      formState[planId] || {
        user_automation_id: "",
        automation_product_plan_id: "",
      }
    );
  };

  const submitFavourite = async (planId) => {
    const state = getPlanState(planId);

    if (!state.user_automation_id || !state.automation_product_plan_id) {
      Swal.fire("Warning", "Select automation and enter plan ID", "warning");
      return;
    }

    setLoading(true);

    try {
        await axios.post(route("product_plans.favourite.store"), {
          product_plan_id: planId,
          user_automation_id: state.user_automation_id,
          automation_product_plan_id: state.automation_product_plan_id,
        });
      
        Swal.fire("Success", "Saved successfully", "success");
      
        setTimeout(() => {
          window.location.reload();
        }, 3000); // 3 seconds delay
      
      } catch (e) {
        Swal.fire("Error", "Something went wrong", "error");
    }

    setLoading(false);
  };

  const filteredPlans = productPlans.filter((plan) =>
    plan.product_plan_name.toLowerCase().includes(search.toLowerCase())
  );

  return (
    <DashboardLayout title="Product Plans">
      <div className="min-h-screen p-4 bg-gray-50 dark:bg-[#0b0f17]">
        <div className="max-w-4xl mx-auto space-y-4">

          {/* HEADER */}
          <div className="p-4 rounded-xl border bg-white dark:bg-[#111827] border-gray-200 dark:border-gray-800 space-y-3">

            <Link
              href={route("dashboard")}
              className="text-xs text-gray-500 dark:text-gray-400 hover:text-emerald-500"
            >
              ← Back to Dashboard
            </Link>

            <div>
              <h1 className="text-lg font-semibold text-gray-900 dark:text-white">
                Product Plans
              </h1>
              <p className="text-xs text-gray-500 dark:text-gray-400">
                Manage automation mappings per plan
              </p>
            </div>

            <input
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              placeholder="Search plans..."
              className="w-full p-2 rounded-md text-sm
                bg-white dark:bg-gray-900
                text-gray-900 dark:text-gray-100
                placeholder-gray-400 dark:placeholder-gray-500
                border border-gray-300 dark:border-gray-700
                focus:outline-none focus:ring-2 focus:ring-emerald-500"
            />
          </div>

          {/* LIST */}
          {filteredPlans.map((plan) => {
            const open = openId === plan.id;
            const isAdded = plan.is_favourite;
            const state = getPlanState(plan.id);

            return (
              <div
                key={plan.id}
                className="p-4 rounded-xl border bg-white dark:bg-[#111827]
                border-gray-200 dark:border-gray-800"
              >
                {/* HEADER */}
                <div className="flex justify-between items-start gap-3">

                  <div>
                    <p className="font-medium text-gray-900 dark:text-white">
                      {plan.product_plan_name}
                    </p>

                    <p className="text-[11px] text-gray-500 dark:text-gray-400">
                      API ID: {plan.api_id}
                    </p>

                    {isAdded && (
                      <div className="mt-2 space-y-1">
                        <p className="text-xs text-emerald-500 dark:text-emerald-400">
                          ✓ Active: {plan.favourite?.automation_name}
                        </p>

                        <div className="flex gap-2 flex-wrap">
                          <span className="text-[10px] px-2 py-1 rounded-md
                            bg-gray-100 dark:bg-gray-900
                            text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700">
                            Plan: {plan.favourite?.automation_plan_id}
                          </span>

                          <span className="text-[10px] px-2 py-1 rounded-md
                            bg-emerald-100 dark:bg-emerald-900
                            text-emerald-700 dark:text-emerald-300">
                            ACTIVE
                          </span>
                        </div>
                      </div>
                    )}
                  </div>

                  <button
                    onClick={() => {
                      setOpenId(open ? null : plan.id);

                      if (isAdded) {
                        setPlanState(plan.id, "user_automation_id", plan.favourite?.automation_id || "");
                        setPlanState(plan.id, "automation_product_plan_id", plan.favourite?.automation_plan_id || "");
                      }
                    }}
                    className={`text-xs px-3 py-1 rounded-md transition
                      ${isAdded
                        ? "text-yellow-500 hover:bg-yellow-500/10"
                        : "text-emerald-500 hover:bg-emerald-500/10"
                      }`}
                  >
                    {open ? "Close" : isAdded ? "Edit" : "Add"}
                  </button>
                </div>

                {/* FORM */}
                {open && (
                  <div className="mt-4 space-y-3">

                    {/* SWITCH QUICK SELECT */}
                    {plan.favourites?.length > 0 && (
                      <div>
                        <p className="text-xs text-gray-500 dark:text-gray-400 mb-2">
                          Switch saved automation
                        </p>

                        <div className="space-y-2">
                          {plan.favourites.map((fav) => (
                            <button
                              key={fav.id}
                              onClick={() => {
                                setPlanState(plan.id, "user_automation_id", fav.automation_id);
                                setPlanState(plan.id, "automation_product_plan_id", fav.automation_plan_id);
                              }}
                              className={`w-full text-left p-2 rounded-md text-xs border transition
                                ${fav.status === 1
                                  ? "bg-emerald-500/10 border-emerald-500 text-emerald-500"
                                  : "bg-gray-100 dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-gray-600 dark:text-gray-300"
                                }`}
                            >
                              {fav.automation_name} → {fav.automation_plan_id}
                              {fav.status === 1 && " (Active)"}
                            </button>
                          ))}
                        </div>
                      </div>
                    )}

                    {/* AUTOMATION SELECT */}
                    <select
                      value={state.user_automation_id}
                      onChange={(e) =>
                        setPlanState(plan.id, "user_automation_id", e.target.value)
                      }
                      className="w-full p-2 rounded-md text-sm
                        bg-white dark:bg-gray-900
                        text-gray-900 dark:text-gray-100
                        border border-gray-300 dark:border-gray-700
                        focus:ring-2 focus:ring-emerald-500"
                    >
                      <option value="">Select automation</option>
                      {userAutomations.map((ua) => (
                        <option key={ua.id} value={ua.id}>
                          {ua.automation_name}
                        </option>
                      ))}
                    </select>

                    {/* PLAN ID */}
                    <input
                      value={state.automation_product_plan_id}
                      onChange={(e) =>
                        setPlanState(plan.id, "automation_product_plan_id", e.target.value)
                      }
                      placeholder="Provider plan ID"
                      className="w-full p-2 rounded-md text-sm
                        bg-white dark:bg-gray-900
                        text-gray-900 dark:text-gray-100
                        border border-gray-300 dark:border-gray-700
                        focus:ring-2 focus:ring-emerald-500"
                    />

                    {/* SAVE */}
                    <button
                      disabled={loading}
                      onClick={() => submitFavourite(plan.id)}
                      className="w-full p-2 rounded-md text-sm
                        bg-emerald-600 hover:bg-emerald-700
                        text-white transition"
                    >
                      {loading ? "Saving..." : "Save Favourite"}
                    </button>

                  </div>
                )}
              </div>
            );
          })}

        </div>
      </div>
    </DashboardLayout>
  );
}