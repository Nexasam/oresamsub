// resources/js/Pages/BuyData.jsx
import { useState } from "react";
import { useForm, usePage, Link } from "@inertiajs/react";
import DashboardLayout from "@/Layouts/DashboardLayout";
import axios from "axios";

export default function BuyData() {
  const { props } = usePage();
  const { auth, networks } = props;
  const user = auth.user;

  const [showBalance, setShowBalance] = useState(true);

  // Plans state
  const [plans, setPlans] = useState([]);
  const [loadingPlans, setLoadingPlans] = useState(false);

  // Inertia form
  const { data, setData, post, processing, errors } = useForm({
    phone_number: "",
    network_id: "",
    product_plan_id: "",
    pin: "",
    product_slug: "data",
    wallet_category: "main_wallet",
  });

  // Handle form submit
  const handleSubmit = (e) => {
    e.preventDefault();
    post(route("ore.data.submit"));
  };

  // 🔹 Fetch plans when a network is chosen
  const handleNetworkChange = async (networkId) => {
    setData("network_id", networkId);
    setData("product_plan_id", ""); // reset plan
    if (!networkId) {
      setPlans([]);
      return;
    }

    setLoadingPlans(true);
    try {
      const response = await axios.get(route("user.fetch_product_plans"), {
        params: {
          network_id: networkId,
          product_slug: "data",
        },
      });
      setPlans(response.data.data || []);
    } catch (err) {
      console.error("Error fetching plans:", err);
      setPlans([]);
    } finally {
      setLoadingPlans(false);
    }
  };

  return (
    <DashboardLayout>
      {/* Wallet card */}
      <div className="bg-emerald-600 dark:bg-emerald-700 text-white p-4 rounded-xl shadow-md flex items-center justify-between">
        <div>
          <p className="text-xs text-white/70 font-medium">Wallet Balance</p>
          <div className="flex items-center space-x-1 text-xl font-bold">
            {showBalance ? (
              <span>₦{Number(user.main_wallet).toFixed(2)}</span>
            ) : (
              <span className="tracking-widest">•••••</span>
            )}
            <button
              onClick={() => setShowBalance((prev) => !prev)}
              className="ml-2 hover:text-white/90 transition"
            >
              {showBalance ? "🙈" : "👁️"}
            </button>
          </div>
        </div>
        <Link
          href={route("ore.virtual_accounts")}
          className="text-sm font-semibold underline hover:text-white/90 transition"
        >
          + Top Up
        </Link>
      </div>

      {/* Buy Data Card */}
      <div className="bg-white dark:bg-gray-800 text-gray-700 dark:text-white mt-6 rounded-xl shadow overflow-hidden">
        <div className="p-4 border-b border-gray-200 dark:border-gray-700 font-semibold text-gray-700 dark:text-white">
          Buy Data
        </div>

        <form onSubmit={handleSubmit} className="p-4 space-y-4">
          {/* Phone Number */}
          <div>
            <label className="block text-sm mb-1">Phone Number</label>
            <input
              type="tel"
              className="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500"
              placeholder="e.g. 08012345678"
              value={data.phone_number}
              onChange={(e) => setData("phone_number", e.target.value)}
            />
            {errors.phone_number && (
              <p className="text-xs text-red-500 mt-1">{errors.phone_number}</p>
            )}
          </div>

          {/* Networks */}
          <div>
            <label className="block text-sm mb-1">Network</label>
            <select
              className="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500"
              value={data.network_id}
              onChange={(e) => handleNetworkChange(e.target.value)}
            >
              <option value="">Select Network</option>
              {networks.map((n) => (
                <option key={n.id} value={n.id}>
                  {n.network_name}
                </option>
              ))}
            </select>
            {errors.network_id && (
              <p className="text-xs text-red-500 mt-1">{errors.network_id}</p>
            )}
          </div>

          {/* Plans */}
          <div>
            <label className="block text-sm mb-1">Plan</label>
            {loadingPlans ? (
              <p className="text-gray-500 text-sm">Loading plans...</p>
            ) : plans.length === 0 ? (
              <p className="text-gray-500 text-sm">Select a network to view plans.</p>
            ) : (
              <div className="grid grid-cols-2 sm:grid-cols-3 gap-3">
                {plans.map((plan) => (
                  <div
                    key={plan.product_plan_id}
                    onClick={() => setData("product_plan_id", plan.product_plan_id)}
                    className={`border rounded-lg p-3 text-center cursor-pointer transition ${
                      data.product_plan_id === plan.product_plan_id
                        ? "border-emerald-600 bg-emerald-50 dark:bg-emerald-900/30"
                        : "hover:border-emerald-400"
                    }`}
                  >
                    <div className="font-semibold text-gray-800 dark:text-white">
                      {plan.product_plan_name}
                    </div>
                    <div className="text-emerald-600 dark:text-emerald-400 font-bold">
                      ₦{Number(plan.selling_price).toLocaleString("en-NG")}
                    </div>
                  </div>
                ))}
              </div>
            )}
            {errors.product_plan_id && (
              <p className="text-xs text-red-500 mt-1">{errors.product_plan_id}</p>
            )}
          </div>

          {/* PIN */}
          <div>
            <label className="block text-sm mb-1">Transaction PIN</label>
            <input
              type="password"
              maxLength={4}
              className="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500"
              placeholder="****"
              value={data.pin}
              onChange={(e) => setData("pin", e.target.value)}
            />
            {errors.pin && (
              <p className="text-xs text-red-500 mt-1">{errors.pin}</p>
            )}
          </div>

          <button
            type="submit"
            disabled={processing}
            className="w-full py-2 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition disabled:opacity-50"
          >
            {processing ? "Processing..." : "📶 Buy Data"}
          </button>
        </form>
      </div>
    </DashboardLayout>
  );
}
