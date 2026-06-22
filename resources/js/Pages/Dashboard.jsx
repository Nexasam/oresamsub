// resources/js/Pages/Dashboard.jsx
import { useState } from "react";
import DashboardLayout from "@/Layouts/DashboardLayout";
import { usePage } from "@inertiajs/react";
import ProductButtons from "@/Components/ProductButtons";
import InviteEarn from "@/Components/InviteEarn";
import CommunityCard from "@/Components/CommunityCard";
import WalletBalance from "@/Components/WalletBalance";
import Announcements from "@/Components/Announcements";
import axios from "axios";
import Swal from "sweetalert2";

export default function Dashboard({ transactions: initialTransactions }) {

  const { props } = usePage();
  const { auth, announcements, contacts, commissionData = {} } = props;

  const { available = 0, pending = 0, total_earned = 0 } = commissionData;
  const user = auth.user;

  // const transactions = initialTransactions ?? props.transactions ?? [];

  const [transactions, setTransactions] = useState(
    initialTransactions ?? props.transactions ?? []
  );

  const [openTransactionId, setOpenTransactionId] = useState(null);
  const [selectedTx, setSelectedTx] = useState(null);
  const [loggingOut, setLoggingOut] = useState(false);

  const [availableState, setAvailableState] = useState(available);
  const [pendingState, setPendingState] = useState(pending);
  const [loading, setLoading] = useState(false);

  const [showPopularPlans, setShowPopularPlans] = useState(false);

  const { popular_plans = [] } = usePage().props;

  const [popularModal, setPopularModal] = useState(false);

    const [search, setSearch] = useState("");
    const [buyModal, setBuyModal] = useState(null);
    const [phone, setPhone] = useState("");
    const [pin, setPin] = useState("");
    const [buying, setBuying] = useState(false);

  const referralLink = `https://oresamsub.com/register?ref=${user.referral_code ?? user.phone_number}`;

  const getStatus = (status) => {
    const s = String(status);
    switch (s) {
      case "1":
        return { text: "Success", color: "text-green-500", color2: "text-green-600" };
      case "0":
        return { text: "Pending", color: "text-yellow-500", color2: "text-yellow-600" };
      case "-1":
        return { text: "Unsuccessful", color: "text-red-500", color2: "text-red-600" };
      case "2":
        return { text: "Refunded", color: "text-blue-500", color2: "text-blue-600" };
      default:
        return { text: "Unknown", color: "text-gray-500", color2: "text-gray-600" };
    }
  };

  const handleTransferWithSwal = async () => {
    if (availableState <= 0) {
      Swal.fire("No Available Balance", "You have no withdrawable commission at the moment.", "warning");
      return;
    }

    const result = await Swal.fire({
      title: "Confirm Withdrawal",
      html: `
        You are about to withdraw
        <b>₦${Number(availableState).toLocaleString()}</b>
        to your wallet.
        <br/><br/>
        <small>This only includes approved commissions.</small>
      `,
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Yes, Withdraw",
      cancelButtonText: "Cancel",
      confirmButtonColor: "#059669",
      cancelButtonColor: "#d33",
    });

    if (!result.isConfirmed) return;

    try {
      setLoading(true);

      const response = await axios.post(route("commissions.transfer"));

      if (response.data.success) {
        await Swal.fire("Success", response.data.message, "success");
        setAvailableState(0);
      } else {
        Swal.fire("Failed", response.data.message || "Withdrawal failed!", "error");
      }
    } catch (error) {
      Swal.fire("Error", "Something went wrong. Please try again.", "error");
    } finally {
      setLoading(false);
    }
  };

  const filteredTransactions = transactions.filter((tx) => {
    const plan = tx.product_plan?.product_plan_name?.toLowerCase() || "";
    return plan.includes(search.toLowerCase());
  });

  const uniquePlans = Object.values(
    transactions
      .filter(tx => tx.transaction_category === "data" && tx.status === "1")
      .reduce((acc, tx) => {
        const key = tx.product_plan_id;
  
        if (!acc[key]) {
          acc[key] = tx; // keep first occurrence
        }
  
        return acc;
      }, {})
  );

  return (
    <DashboardLayout title="Dashboard">

      {/* Wallet */}
      <WalletBalance user={user} />

      {/* Admin / Marketer shortcut */}
      {(user.is_marketer === 1 || user.role?.role_name === "Admin") && (
        <a href={route("marketer.dashboard")}>
          <div className="bg-green-800 text-white p-2 rounded-xl my-4">
            <h1 className="text-center">Go to Marketer Dashboard</h1>
          </div>
        </a>
      )}

      {/* Announcements */}
      <Announcements announcements={announcements} />


      {/* <button
        onClick={() => setShowPopularPlans(true)}
        className="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-2 rounded-lg"
      >
        Popular Data Plans
      </button> */}

      <div className="grid grid-cols-1 lg:grid-cols-4  gap-2 mt-4">
        <button
          onClick={() => setShowPopularPlans(true)}
          className="group p-2 rounded-lg bg-white dark:bg-gray-800 border border-emerald-100 dark:border-emerald-900/40 hover:shadow-md transition flex flex-col items-center"
        >
          <div className="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center text-white text-sm shadow-sm group-hover:shadow-emerald-400/40 group-hover:scale-105 transition">
            📊
          </div>

          <div className="mt-1 text-[11px] font-medium text-emerald-700 dark:text-emerald-300 text-center leading-tight">
            Favourite Data Plans
          </div>
        </button>
      </div>

      {/* <button
        onClick={() => setShowPopularPlans(true)}
        className="group flex flex-col items-center p-2 rounded-lg bg-white dark:bg-gray-800 border hover:shadow-sm transition"
      >
        <div className="w-8 h-8 flex items-center justify-center rounded-full bg-emerald-500 text-white text-sm">
          📊
        </div>

        <div className="mt-1 text-[11px] font-medium text-gray-700 dark:text-gray-200 text-center leading-tight">
          Popular
        </div>
      </button> */}

      {/* Product Actions */}
      <ProductButtons loggingOut={loggingOut} setLoggingOut={setLoggingOut} />

      {/* Commission Summary */}
      <div className="grid grid-cols-3 gap-3 mt-4">

        {/* Pending */}
        <div className="bg-yellow-50 dark:bg-yellow-900 p-3 rounded-xl text-center">
          <p className="text-[10px] uppercase text-yellow-700 dark:text-yellow-300">
            Pending Earnings
          </p>
          <h2 className="text-lg font-semibold mt-1 text-[11px] text-gray-700 dark:text-gray-200">
            ₦{Number(pendingState).toLocaleString()}
          </h2>
          <p className="text-[10px] text-gray-900 dark:text-gray-200">
            Awaiting approval
          </p>
        </div>

        {/* Available */}
        <div
          onClick={handleTransferWithSwal}
          className="bg-green-100 dark:bg-green-900 p-3 rounded-xl text-center cursor-pointer"
        >
          <p className="text-[10px] uppercase text-green-700 dark:text-green-300">
            Withdrawable Balance
          </p>
          <h2 className="text-lg font-semibold mt-1 text-[11px] text-gray-700 dark:text-gray-200">
            ₦{Number(availableState).toLocaleString()}
          </h2>
          <p className="text-[10px] text-green-700 dark:text-green-300 animate-pulse">
            Withdraw to wallet →
          </p>
        </div>

        {/* Total */}
        <div className="bg-blue-50 dark:bg-blue-900 p-3 rounded-xl text-center">
          <p className="text-[10px] uppercase text-blue-700 dark:text-blue-300">
            Total Earnings
          </p>
          <h2 className="text-lg font-semibold mt-1 text-[11px] text-gray-600 dark:text-gray-200">
            ₦{Number(total_earned).toLocaleString()}
          </h2>
          <p className="text-[10px] text-gray-500">
            Lifetime commissions
          </p>
        </div>
      </div>

      {/* Transparency note */}
      <div className="mt-3 text-[11px] text-gray-500 text-center">
        Commissions are earned from successful transactions and approved referrals.
      </div>

      {/* Referral */}
      <InviteEarn referralLink={referralLink} title="Referral Rewards" />

      {/* Community */}
      <CommunityCard customerCategory={user.customer_category} />


    
      {/* Recent Transactions */}
      {/* Recent Transactions */}
    <div className="bg-white dark:bg-gray-800 mt-6 rounded-xl shadow overflow-hidden">

    <div className="p-4 border-b border-gray-200 dark:border-gray-700 font-semibold text-gray-700 dark:text-white">
      Recent Transactions
    </div>

    <input
  type="text"
  placeholder="Search plan e.g 1GB MTN"
  value={search}
  onChange={(e) => setSearch(e.target.value)}
  className="w-full px-3 py-2 text-sm rounded-lg border 
             bg-white text-gray-800 placeholder-gray-400
             dark:bg-gray-900 dark:text-white dark:placeholder-gray-500 dark:border-gray-700"
/>

    {filteredTransactions.length > 0 ? (
  filteredTransactions.map((tx) => {
    const status = getStatus(tx.status);

    return (
      <div
      key={tx.id}
      onClick={() => setSelectedTx(tx)}
     className="px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-900 transition cursor-pointer active:scale-[0.99] border-b border-gray-200 dark:border-gray-700">
        <div className="flex justify-between">
          <div
            onClick={() => setSelectedTx(tx)}
            className="cursor-pointer"
          >
         <div className="font-semibold text-xs text-gray-800 dark:text-white">
              {tx.product_plan?.product_plan_name?.toUpperCase() || "N/A"}
            </div>
            <div className="text-xs text-gray-500 dark:text-gray-300">
              {new Date(tx.created_at).toLocaleString("en-NG")}
            </div>
          </div>

          <div className="text-right">
            <div className={`font-bold ${status.color}`}>
              ₦{Number(tx.amount).toLocaleString("en-NG")}
            </div>
            <div className={`text-xs ${status.color2}`}>
              {status.text}
            </div>
          </div>
        </div>

        {/* BUY AGAIN BUTTON */}
        {/* {tx.transaction_category === "data" &&
          status.text === "Success" &&
          Number(tx.service_charge || 0) === 0 && (
            <div className="mt-2 text-right">
              <button
               onClick={async (e) => {
                e.stopPropagation();
              
                const result = await Swal.fire({
                  title: "Buy Again?",
                  text: "You are about to start a new purchase using this plan.",
                  icon: "question",
                  showCancelButton: true,
                  confirmButtonText: "Yes, Continue",
                  cancelButtonText: "Cancel",
                  confirmButtonColor: "#059669",
                });
              
                if (!result.isConfirmed) return;
              
                setBuyModal(tx);
                setPhone(""); // IMPORTANT: force fresh input
                setPin("");   // reset PIN too
              }}
                className="text-xs px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md"
              >
                Buy Again
              </button>
            </div>
          )} */}
      </div>
    );
  })
) : (
  <p className="p-4 text-center text-gray-500 text-sm">
    No matching transactions
  </p>
)}

    {/* <div className="max-h-[400px] overflow-y-auto divide-y divide-gray-200 dark:divide-gray-700 text-sm">
      {transactions.length > 0 ? (
        transactions.map((tx) => {
          const status = getStatus(tx.status);

          return (
            <div
              key={tx.id}
              onClick={() => setSelectedTx(tx)}
              className="px-4 py-3 flex justify-between cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-900 transition"
            >
              <div>
                <div className="font-semibold text-xs text-gray-800 dark:text-gray-200">
                  {tx.transaction_category?.toUpperCase()}
                </div>
                <div className="text-xs text-gray-500 dark:text-gray-400">
                  {new Date(tx.created_at).toLocaleString("en-NG")}
                </div>
              </div>

              <div className="text-right">
                <div className={`font-bold ${status.color}`}>
                  ₦{Number(tx.amount).toLocaleString("en-NG")}
                </div>
                <div className={`text-xs ${status.color2}`}>
                  {status.text}
                </div>
              </div>
            </div>
          );
        })
      ) : (
        <p className="p-4 text-center text-gray-500 text-sm">
          No transactions yet
        </p>
      )}
    </div>
     */}
    </div>



    {/* Transaction Modal */}
      {selectedTx && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
          <div className="bg-white dark:bg-gray-800 rounded-xl shadow-lg max-w-sm w-full p-6">

            <h2 className="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">
              Transaction Details
            </h2>

            <div className="space-y-2 text-sm text-gray-700 dark:text-gray-300">

              {selectedTx.status === 2 && selectedTx.refund_reason && (
                <div className="flex justify-between">
                  <span>Refund reason:</span>
                  <span className="font-semibold">{selectedTx.refund_reason}</span>
                </div>
              )}

              <div className="flex justify-between">
                <span>Plan:</span>
                <span className="font-semibold">
                  {selectedTx.product_plan?.product_plan_name ?? "nil"}
                </span>
              </div>

              <div className="flex justify-between mb-2">
                <span>Phone:</span>
                <span className="font-semibold">{selectedTx.phone_number}</span>
              </div>


           

              <div className="flex justify-between">
                <span>Amount:</span>
                <span className="font-semibold">
                  ₦{Number(selectedTx.amount).toLocaleString("en-NG")}
                </span>
              </div>

              {Number(selectedTx.service_charge) !== 0 && (
                <div className="flex justify-between mb-2">
                  <span>Service Charge:</span>
                  <span className="font-semibold">
                    {selectedTx.service_charge}
                  </span>
                </div>
              )}

              <div className="flex justify-between">
                <span>Status:</span>
                <span className={getStatus(selectedTx.status).color2}>
                  {getStatus(selectedTx.status).text}
                </span>
              </div>

              <div className="flex justify-between">
                <span>Date:</span>
                <span>
                  {new Date(selectedTx.created_at).toLocaleString("en-NG")}
                </span>
              </div>

           

            </div>

            <div className="my-2 text-sm text-gray-700 dark:text-gray-300">
              <span>Message:</span> <br />

              <span className="font-semibold">
                {selectedTx.status !== 1
                  ? "Transaction was not successful"
                  : selectedTx.user_screen_message}
              </span>
            </div>

            <div className="mt-6 text-center">
              <button
                onClick={() => setSelectedTx(null)}
                className="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md text-sm"
              >
                Close
              </button>
            </div>

          </div>
        </div>
    )}

    {buyModal && (
      <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div className="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-xl shadow-lg max-w-sm w-full p-6">

          <h2 className="text-lg font-bold mb-4">
            Buy Again
          </h2>

          <div className="space-y-3 text-sm">

            <div>
              <label className="block text-xs mb-1 text-gray-600 dark:text-gray-300">
                Plan
              </label>
              <div className="font-semibold">
                {/* {buyModal.product_plan?.product_plan_name} */}
                {buyModal.product_plan_name}
              </div>
            </div>

             {/* Amount */}
            <div>
              <label className="block text-xs mb-1 text-gray-600 dark:text-gray-300">
                Amount
              </label>
              <div className="font-bold text-emerald-600 dark:text-emerald-400">
              
                ₦{Number(buyModal.current_price).toLocaleString()}
              </div>
            </div>

            <div>
              <label className="block text-xs mb-1 text-gray-600 dark:text-gray-300">
                Phone Number
              </label>
              <input
              value={phone}
              placeholder="Enter phone number"
              onChange={(e) => setPhone(e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
              />
            </div>

            <div>
              <label className="block text-xs mb-1 text-gray-600 dark:text-gray-300">
                PIN
              </label>
              <input
                type="password"
                value={pin}
                onChange={(e) => setPin(e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
              />
            </div>

          </div>

          <div className="mt-5 flex justify-between gap-2">
            <button
              onClick={() => setBuyModal(null)}
              className="w-full py-2 bg-gray-300 dark:bg-gray-700 dark:text-white rounded-lg"
            >
              Cancel
            </button>

            <button
              disabled={buying}
              onClick={async () => {
                if (!phone || !pin) {
                  Swal.fire("Error", "Enter phone and PIN", "warning");
                  return;
                }
              
                const confirm = await Swal.fire({
                  title: "Confirm Purchase",
                  html: `
                    <div style="text-align:left; line-height:1.6">
               
                      <div><b>Phone:</b> ${phone}</div>
                      <div><b>Plan:</b> ${buyModal.product_plan_name}</div>
                      <div><b>Amount:</b> ₦${Number(buyModal.current_price).toLocaleString()}</div>
                     
                    </div>
                  `,
                  icon: "question",
                  showCancelButton: true,
                  confirmButtonText: "Proceed",
                  cancelButtonText: "Cancel",
                  confirmButtonColor: "#059669",
                });
              
                if (!confirm.isConfirmed) return;
              
                try {
                  setBuying(true);
              
                  const res = await axios.post(route("user.data.buy_again_data_action"), {
                    product_plan_id: buyModal.product_plan_id,
                    phone_number: phone,
                    pin: pin,
                  });
              
                  const data = res?.data;
              
                  if (Number(data?.status) === 1) {
                    Swal.fire("Success", data.message || "Purchase successful", "success");
              
                    setBuyModal(null);
                    setPhone("");
                    setPin("");
              
                  } else {
                    Swal.fire("Failed", data?.message || "Transaction failed", "error");
                  }
              
                } catch (e) {
                  const message =
                    e?.response?.data?.message || "Something went wrong";
              
                  Swal.fire("Error", message, "error");
              
                } finally {
                  setBuying(false);
                }
              }}
              className="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg disabled:opacity-50"
            >
              {buying ? "Processing..." : "Buy Now"}
            </button>
          </div>

        </div>
      </div>
    )}

    {showPopularPlans && (
      <div className="fixed inset-0 z-50 bg-black/60 flex items-center justify-center px-3">

        <div className="bg-white dark:bg-gray-900 w-full max-w-2xl rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 p-4 max-h-[85vh] overflow-y-auto">

          {/* Header */}
          <div className="flex justify-between items-center mb-4">
            <h2 className="text-lg font-bold text-gray-900 dark:text-white">
              Favourite Data Plans
            </h2>

            <button
              onClick={() => setShowPopularPlans(false)}
              className="text-sm text-red-500 hover:text-red-600 dark:hover:text-red-400"
            >
              Close
            </button>
          </div>

          {/* SECTION 1 */}
          <div className="mb-6">
            <h3 className="text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">
              Most Used Plans
            </h3>

       
       
        

            <div className="border rounded-lg border-gray-200 dark:border-gray-700 max-h-60 overflow-y-auto">

            <div className="grid grid-cols-2 gap-2">
                 

                  {popular_plans.map((plan) => (
                    <button
                      key={plan.product_plan_id}
                      onClick={() => {
                        setBuyModal(plan);
                        setPhone(plan.phone_number);
                        setShowPopularPlans(false);
                      }}
                      className="p-3 text-left rounded-lg border
                                border-gray-200 dark:border-gray-700
                                bg-white dark:bg-gray-800
                                hover:bg-emerald-50 dark:hover:bg-gray-700
                                transition"
                    >
                      <div className="text-xs font-semibold text-gray-900 dark:text-white">
                        {plan.product_plan_name}
                      </div>

                      <div className="text-[10px] text-gray-500 dark:text-gray-400 mt-1">
                        ₦{Number(plan.current_price).toLocaleString()}
                      </div>
                    </button>
                  ))}
            </div>

            </div>
          </div>

        </div>
      </div>
    )}



    </DashboardLayout>
  );
}