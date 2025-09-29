// resources/js/Pages/BuyData.jsx
import { useState } from "react";
import { useForm, usePage, Link } from "@inertiajs/react";
import DashboardLayout from "@/Layouts/DashboardLayout";
import axios from "axios";
import Swal from "sweetalert2";
import WalletBalance from "@/Components/WalletBalance";

// inside your map(tx)
// const telcoColor = getTelcoColor(tx.product_plan?.product_plan_name);


export default function BuyData() {
  const { props } = usePage();
  const { auth, networks } = props;
  const user = auth.user;

  const [showBalance, setShowBalance] = useState(true);

  // Plans + Sizes state
  const [plans, setPlans] = useState([]);
  const [sizes, setSizes] = useState([]);
  const [activeSize, setActiveSize] = useState("all");
  const [loadingPlans, setLoadingPlans] = useState(false);
  const [submitting, setSubmitting] = useState(false);


  // Inertia form
  const { data, setData, post, processing, errors } = useForm({
    phone_number: "",
    network_id: "",
    product_plan_id: "",
    pin: "",
    product_slug: "data",
    wallet_category: "main_wallet",
    validatephonenetwork:0
  });

const handleSubmit = async (e) => {
  e.preventDefault();

  const selectedPlan = plans.find((p) => p.product_plan_id === data.product_plan_id);
  if (!selectedPlan) {
    Swal.fire("No Plan Selected", "Please select a plan first.", "warning");
    return;
  }

  const result = await Swal.fire({
    title: "Confirm Purchase",
    html: `
      Are you sure you want to purchase 
      <b>${selectedPlan.product_plan_name}</b> 
      for <b>₦${Number(selectedPlan.selling_price).toLocaleString("en-NG")}</b>?
    `,
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Yes, Buy Now ✅",
    cancelButtonText: "Cancel ❌",
    confirmButtonColor: "#059669",
    cancelButtonColor: "#d33",
  });

  if (!result.isConfirmed) return;

  try {
    setSubmitting(true); // 🔹 disable button + show processing
    const response = await axios.post(route("user.data.buy_data_action2"), data);

    if (response.data.status === 1) {
      await Swal.fire("✅ Success", response.data.message, "success");
      // window.location.href = route("dashboard");

        // 🔹 Clear form on success
        // setPlans([]);
        window.location.reload(); // reload the page

    } else {
      Swal.fire("⚠️ Failed", response.data.message, "error");
    }
  } catch (error) {
    console.error("Error submitting form:", error);
    Swal.fire("❌ Error", "Something went wrong. Please try again.", "error");
  } finally {
    setSubmitting(false); // 🔹 re-enable button
  }
};


  

  // 🔹 Fetch plans when a network is chosen
  const handleNetworkChange = async (networkId) => {
    setData("network_id", networkId);
    setData("product_plan_id", ""); // reset plan
    if (!networkId) {
      setPlans([]);
      setSizes([]);
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
      setSizes(response.data.sizes || []);
      setActiveSize("all");
    } catch (err) {
      console.error("Error fetching plans:", err);
      setPlans([]);
      setSizes([]);
    } finally {
      setLoadingPlans(false);
    }
  };

  // 🔹 Filter plans by size
  const filteredPlans =
    activeSize === "all"
      ? plans
      : plans.filter((p) => p.data_size_in_mb === activeSize);

  return (
    <DashboardLayout  title="Buy Data">
      {/* Wallet card */}
      <WalletBalance user={user} />

      <Link
        href={route("dashboard")}
        className="inline-flex items-center px-4 py-2 mb-2 mt-4 rounded-lg bg-gradient-to-r from-emerald-600 to-emerald-500 text-white text-sm font-medium shadow hover:from-emerald-700 hover:to-emerald-600 transition"
      >
        ← Back to Dashboard
      </Link>

      {/* Buy Data Card */}
      <div className="bg-white dark:bg-gray-800 text-gray-700 dark:text-white mt-6 pb-16  rounded-xl shadow overflow-hidden">
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

          {/* <div>
            <label className="block text-sm mb-2">Network</label>
            <div className="grid grid-cols-2 sm:grid-cols-4 gap-3">
              {networks.map((n) => {
                // assign light theme colors for each provider
                const networkColors = {
                  MTN: "bg-yellow-100 text-yellow-800 border-yellow-300",
                  GLO: "bg-green-100 text-green-800 border-green-300",
                  Airtel: "bg-red-100 text-red-800 border-red-300",
                  "9mobile": "bg-blue-100 text-blue-800 border-blue-300",
                };

                const colorClass =
                  networkColors[n.network_name] || "bg-gray-100 text-gray-700 border-gray-300";

                return (
                  <div
                    key={n.id}
                    onClick={() => handleNetworkChange(n.id)}
                    className={`cursor-pointer rounded-lg border p-3 text-center font-semibold transition
                      ${data.network_id === n.id
                        ? `${colorClass} ring-2 ring-emerald-500`
                        : `${colorClass} hover:shadow-md`}
                    `}
                  >
                    {n.network_name}
                  </div>
                );
              })}
            </div>
            {errors.network_id && (
              <p className="text-xs text-red-500 mt-1">{errors.network_id}</p>
            )}
          </div> */}


          {/* Networks */}
          <div>
            <label className="block text-sm mb-2">Network</label>
            <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
              {networks.map((n) => {
                // Exact brand colors
                const networkColors = {
                  MTN: "bg-[#FFCC00] text-black border-[#FFCC00]",   
                  GLO: "bg-[#008751] text-white border-[#008751]",   
                  AIRTEL: "bg-gradient-to-r from-[#EE1C25] to-[#B50000] text-white border-[#B50000]",
                  "9MOBILE": "bg-[#A6CE39] text-black border-[#A6CE39]" 
                };
                
                const colorClass =
                  networkColors[n.network_name] ||
                  "bg-gray-200 text-gray-700 border-gray-300";

                return (
                  <div
                    key={n.id}
                    onClick={() => handleNetworkChange(n.id)}
                    className={`cursor-pointer rounded-lg border p-1 text-center font-semibold transition
                      ${data.network_id === n.id
                        ? `${colorClass} ring-2 ring-emerald-500 scale-105`
                        : `${colorClass} hover:shadow-md`}
                    `}
                  >
                    {n.network_name}
                  </div>
                );
              })}
            </div>
            {errors.network_id && (
              <p className="text-xs text-red-500 mt-1">{errors.network_id}</p>
            )}
          </div>



          {/* Sizes Filter */}
          {sizes.length > 0 && (
            <div className="flex flex-wrap gap-2 mb-2">
              <button
                type="button"
                onClick={() => setActiveSize("all")}
                className={`px-3 py-1 rounded-full border text-sm transition ${
                  activeSize === "all"
                    ? "bg-emerald-600 text-white border-emerald-600"
                    : "bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-emerald-100"
                }`}
              >
                All
              </button>
              {sizes.map((s) => {
                const label = s >= 1000 ? `${s / 1000}GB` : `${s}MB`;
                return (
                  <button
                    key={s}
                    type="button"
                    onClick={() => setActiveSize(s)}
                    className={`px-3 py-1 rounded-full border text-sm transition ${
                      activeSize === s
                        ? "bg-emerald-600 text-white border-emerald-600"
                        : "bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-emerald-100"
                    }`}
                  >
                    {label}
                  </button>
                );
              })}
            </div>
          )}

          {/* Plans */}
          <div>
            <label className="block text-sm mb-1">Plan</label>
            {loadingPlans ? (
              <p className="text-gray-500 text-sm">Loading plans...</p>
            ) : filteredPlans.length === 0 ? (
              <p className="text-gray-500 text-sm">No plans available.</p>
            ) : (
              <div className="max-h-64 overflow-y-auto pr-1 border-2 border-gray-400 border-rounded-xl p-3">
                <div className="grid grid-cols-2 sm:grid-cols-3 gap-3">
                  {filteredPlans.map((plan) => (
                    <div
                      key={plan.product_plan_id}
                      onClick={() =>
                        setData("product_plan_id", plan.product_plan_id)
                      }
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
              </div>
            )}
            {errors.product_plan_id && (
              <p className="text-xs text-red-500 mt-1">{errors.product_plan_id}</p>
            )}
          </div>

          {/* PIN */}
          {/* <PinInput className="flex-start"
            value={data.pin}
            onChange={(val) => setData("pin", val)}
          /> */}

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

          {/* <button
            type="submit"
            disabled={processing}
            className="w-full py-2 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition disabled:opacity-50"
          >
            {processing ? "Processing..." : "📶 Buy Data"}
          </button> */}

          <button
          type="submit"
          disabled={submitting}
          className="w-full py-2 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition disabled:opacity-50"
          >
          {submitting ? "Processing..." : "📶 Buy Data"}
          </button>




        </form>
      </div>
    </DashboardLayout>
  );
}
