import { useState } from "react";
import { useForm, usePage, Link } from "@inertiajs/react";
import DashboardLayout from "@/Layouts/DashboardLayout";
import axios from "axios";
import Swal from "sweetalert2";
import WalletBalance from "@/Components/WalletBalance";



export default function BuyAirtime() {
  const { props } = usePage();
  const { auth, networks } = props;
  const user = auth.user;

  const [showBalance, setShowBalance] = useState(true);
  const [plans, setPlans] = useState([]);
  const [loadingPlans, setLoadingPlans] = useState(false);
  const [submitting, setSubmitting] = useState(false);

  const { data, setData, post, errors } = useForm({
    phone_number: "",
    network_id: "",
    product_plan_id: "",
    amount: "",
    pin: "",
    product_slug: "airtime",
    wallet_category: "main_wallet",
    validatephonenetwork:1
  });

 // When network changes
  const handleNetworkChange = (networkId) => {
    setData("network_id", networkId);
    setData("product_plan_id", ""); // reset plan
    fetchPlans(networkId, data.amount);
  };
  
  // When amount changess
  const handleAmountChange = (amount) => {
    setData("amount", amount);
    fetchPlans(data.network_id, amount);
  };




  const fetchPlans = async (networkId, amount = "") => {
    if (!networkId) return;
  
    setLoadingPlans(true);
    try {
      const response = await axios.get(route("user.fetch_product_plans"), {
        params: {
          network_id: networkId,
          product_slug: "airtime",
          amount,
        },
      });
  
      // Ensure it's an array
      const dataList = Array.isArray(response.data.data)
        ? response.data.data
        : Object.values(response.data.data || {});
  
      const mappedPlans = dataList.map((plan) => {
        if (amount) {
          return {
            ...plan,
            label: `${plan.product_plan_name} - You are buying for: ₦${plan.selling_price}`,
            selected: true,
          };
        } else {
          return {
            ...plan,
            label: plan.product_plan_name,
            selected: false,
          };
        }
      });
  
      setPlans(mappedPlans);
  
      // Auto-select first plan if amount provided
      if (amount && mappedPlans.length > 0) {
        setData("product_plan_id", mappedPlans[0].product_plan_id);
      }
    } catch (err) {
      console.error("Error fetching airtime plans:", err);
      setPlans([]);
    } finally {
      setLoadingPlans(false);
    }
  };
  
  

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!data.product_plan_id) {
      Swal.fire("No Plan Selected", "Please select a plan first.", "warning");
      return;
    }

    const selectedPlan = plans.find((p) => p.product_plan_id === data.product_plan_id);

    const result = await Swal.fire({
      title: "Confirm Transaction",
      html: `
        You are about to purchase <b>${selectedPlan.product_plan_name}</b><br/>
        Amount: <b>₦${Number(selectedPlan.selling_price).toLocaleString("en-NG")}</b><br/>
        Recipient: <b>${data.phone_number}</b><br/><br/>
        Please confirm to continue.
      `,
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Confirm",
      cancelButtonText: "Cancel",
      confirmButtonColor: "#059669",
      cancelButtonColor: "#d33",
    });

    if (!result.isConfirmed) return;

    try {
      setSubmitting(true);
      const response = await axios.post(route("user.airtime.buy_airtime_action2"), data);
      if (response.data.status === 1) {
        await Swal.fire("✅ Transaction Completed", response.data.message, "success");
       
        
        // 🔹 Clear form on success
        // setPlans([]);
        window.location.reload(); // reload the page

      } else {
        Swal.fire("⚠️ Failed", response.data.message, "error");
      }
    } catch (error) {
      console.error(error);
      Swal.fire("❌ Error", "Something went wrong. Please try again.", "error");
    } finally {
      setSubmitting(false);
    }
  };

  const networkColors = {
    MTN: "bg-[#FFCC00] text-black border-[#FFCC00]",
    GLO: "bg-[#008751] text-white border-[#008751]",
    AIRTEL: "bg-gradient-to-r from-[#EE1C25] to-[#B50000] text-white border-[#B50000]",
    "9MOBILE": "bg-[#A6CE39] text-black border-[#A6CE39]",
  };

  return (
    <DashboardLayout  title="Airtime Purchase">
      {/* Wallet */}
      <WalletBalance user={user} />

      <Link
        href={route("dashboard")}
        className="inline-flex items-center px-4 py-2 mb-2 mt-4 rounded-lg bg-gradient-to-r from-emerald-600 to-emerald-500 text-white text-sm font-medium shadow hover:from-emerald-700 hover:to-emerald-600 transition font-inter"
      >
        ← Back to Dashboard
      </Link>

      {/* Buy Airtime Card */}
      <div className="bg-white dark:bg-gray-800 text-gray-700 dark:text-white mt-6 pb-16 rounded-xl shadow overflow-hidden font-inter">
        <div className="p-4 border-b border-gray-200 dark:border-gray-700 font-semibold text-gray-700 dark:text-white">
          Airtime Purchase
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
            {errors.phone_number && <p className="text-xs text-red-500 mt-1">{errors.phone_number}</p>}
          </div>

            {/* Network Cards */}
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

          

          {/* Amount */}
          <div>
            <label className="block text-sm mb-1">Amount (₦)</label>
            <input
              type="number"
              className="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500"
              placeholder="e.g. 100"
              min={50}
              value={data.amount}
              onChange={(e) => handleAmountChange(e.target.value)}
            />
          </div>

      

          {/* Product Plan */}
          <div>
            <label className="block text-sm mb-1">Product Plan</label>
            {loadingPlans ? (
              <p className="text-gray-500 text-sm">Fetching available options...</p>
            ) : plans.length === 0 ? (
              <p className="text-gray-500 text-sm">No available plans for the selected network.</p>
            ) : (
              <div className="max-h-64 overflow-y-auto pr-1 border-2 border-gray-400 rounded-xl p-3">
                <div className="grid grid-cols-2 sm:grid-cols-3 gap-3">
                  {plans.map((plan) => (
                    <div
                      key={plan.product_plan_id}
                      onClick={() => setData("product_plan_id", plan.product_plan_id)}
                      className={`border rounded-lg p-3 text-center cursor-pointer transition ${
                        data.product_plan_id === plan.product_plan_id ? "border-emerald-600 bg-emerald-50 dark:bg-emerald-900/30" : "hover:border-emerald-400"
                      }`}
                    >
                      <div className="font-semibold text-gray-800 dark:text-white">{plan.product_plan_name}</div>
                      <div className="text-emerald-600 dark:text-emerald-400 font-bold">
                        ₦{Number(plan.selling_price).toLocaleString("en-NG")}
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            )}
          </div>

          {/* Transaction PIN */}
          <div>
            <label className="block text-sm mb-1">Security PIN</label>
            <input
              type="password"
              maxLength={4}
              className="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500"
              placeholder="****"
              value={data.pin}
              onChange={(e) => setData("pin", e.target.value)}
            />

            <p className="text-xs text-gray-500 mt-1">
              This PIN is used to authorize transactions on your account.
            </p>
          </div>

          {/* Submit */}
          <button
            type="submit"
            disabled={submitting}
            className="w-full py-2 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition disabled:opacity-50"
          >
            {submitting ? "Processing..." : "🔌 Buy Airtime"}
          </button>
        </form>
      </div>
    </DashboardLayout>
  );
}
