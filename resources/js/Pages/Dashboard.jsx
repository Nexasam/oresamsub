// resources/js/Pages/Dashboard.jsx
import { useState } from "react";
import DashboardLayout from "@/Layouts/DashboardLayout";
import { Link, usePage } from "@inertiajs/react";
import ProductButtons from "@/Components/ProductButtons";
import InviteEarn from "@/Components/InviteEarn";
import CommunityCard from "@/Components/CommunityCard";
import WalletBalance from "@/Components/WalletBalance";
import Announcements from "@/Components/Announcements";
import BuyAgainModal from "@/Components/BuyAgainModal";
import axios from "axios";
import Swal from "sweetalert2";

export default function Dashboard({ transactions: initialTransactions }) {
  const { props } = usePage();
  const { auth, announcements,contacts, impersonator, commissionData = {} } = props;
  const { available = 0, pending = 0, total_earned = 0 } = commissionData;
  const user = auth.user;

  const commss = true;

  const transactions = initialTransactions ?? props.transactions ?? [];
  const [showTransferModal, setShowTransferModal] = useState(false);
  const [showBalance, setShowBalance] = useState(true);
  const [openTransactionId, setOpenTransactionId] = useState(null);
  const [loggingOut, setLoggingOut] = useState(false);
  const [isBuyAgainOpen, setIsBuyAgainOpen] = useState(false);


  const [inviteOpen, setInviteOpen] = useState(false);
  const [copied, setCopied] = useState(false);


  //commissions
  const [availableState, setAvailableState] = useState(available);
  const [pendingState, setPendingState] = useState(pending);
  const [loading, setLoading] = useState(false);


  const referralLink = `https://oresamsub.com/register?ref=${user.phone_number}`;


  


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

  const copyReferral = () => {
    navigator.clipboard.writeText(referralLink).then(() => {
      setCopied(true);
      setTimeout(() => setCopied(false), 2000);
    });
  };

  const handleTransferWithSwal = async () => {
    if (availableState <= 0) {
      Swal.fire("No Funds", "You don't have any available commission to transfer.", "warning");
      return;
    }
  
    const result = await Swal.fire({
      title: "Confirm Transfer",
      html: `
        Are you sure you want to transfer
        <b>₦${Number(availableState).toLocaleString()}</b>
        to your wallet?
      `,
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Yes, Transfer ✅",
      cancelButtonText: "Cancel ❌",
      confirmButtonColor: "#059669",
      cancelButtonColor: "#d33",
    });
  
    if (!result.isConfirmed) return;
  
    try {
      setLoading(true); // show loading state
  
      const response = await axios.post(route("commissions.transfer"));
  
      if (response.data.success) {
        await Swal.fire("✅ Success", response.data.message, "success");
  
        // Update frontend state
        setAvailableState(0);
        setPendingState(pendingState); // pending remains unchanged
      } else {
        Swal.fire("⚠️ Failed", response.data.message || "Transfer failed!", "error");
      }
    } catch (error) {
      console.error("Transfer error:", error);
      Swal.fire(
        "❌ Error",
        error.response?.data?.message || "Something went wrong. Please try again.",
        "error"
      );
    } finally {
      setLoading(false); // hide loading state
    }
  };
  

  return (
    <DashboardLayout title="Dashboard">
      {/* Wallet */}
      <WalletBalance user={user} />

{/* 
        <button
          onClick={() => setIsBuyAgainOpen(true)}
          className="px-4 py-2 mt-4 bg-blue-600 text-white rounded-lg"
        >
          Buy Again
        </button>

        <BuyAgainModal
          isOpen={isBuyAgainOpen}
          onClose={() => setIsBuyAgainOpen(false)}
          contacts={contacts}
        /> */}

         {/* Marketer/Admin Shortcut */}
         {(user.is_marketer === 1 || user.role?.role_name === "Admin") && (
        <a href={route("marketer.dashboard")}>
          <div className="bg-green-800 text-white p-2 rounded-xl my-4">
            <h1 className="text-center">Go to Marketer Dashboard</h1>
          </div>
        </a>
        )}

      {/* Announcements Slider */}
      <Announcements announcements={announcements} />


      {/* Commission Summary Cards */}
    {commss && (
      <div className="grid grid-cols-3 gap-3 mt-4">
        {/* Pending */}
        <div className="group bg-yellow-50 dark:bg-yellow-900 p-3 rounded-xl shadow-sm hover:shadow transition text-center">
          <p className="text-[10px] font-medium text-yellow-700 dark:text-yellow-300 uppercase">
            Pending
          </p>
          <h2 className="text-lg font-semibold text-yellow-800 dark:text-yellow-200 mt-1">
            ₦{Number(pendingState).toLocaleString()}
          </h2>
          <p className="text-[10px] text-gray-400 dark:text-gray-300 leading-tight">
            Awaiting approval
          </p>
        </div>

        {/* Available */}
        <div
          onClick={handleTransferWithSwal}
          className="bg-green-100 dark:bg-green-900 p-3 rounded-xl shadow-sm hover:shadow transition text-center cursor-pointer"
        >
          <p className="text-[10px] font-medium text-green-700 dark:text-green-300 uppercase">
            Available
          </p>
          <h2 className="text-lg font-semibold text-green-800 dark:text-green-200 mt-1">
            ₦{Number(availableState).toLocaleString()}
          </h2>
          <p className="text-[10px] font-medium text-green-600 dark:text-green-300 animate-pulse">
            Tap to transfer →
          </p>
        </div>

        {/* Total Earned */}
        <div className="group bg-blue-50 dark:bg-blue-900 p-3 rounded-xl shadow-sm hover:shadow transition text-center">
          <p className="text-[10px] font-medium text-blue-700 dark:text-blue-300 uppercase">
            Total
          </p>
          <h2 className="text-lg font-semibold text-blue-800 dark:text-blue-200 mt-1">
            ₦{Number(total_earned).toLocaleString()}
          </h2>
          <p className="text-[10px] text-gray-400 dark:text-gray-300 leading-tight">
            Lifetime earnings
          </p>
        </div>
      </div>
    )}



      {/* Invite & Earn */}
      <InviteEarn referralLink={referralLink} />

     
      {/* Product Buttons */}
      <ProductButtons loggingOut={loggingOut} setLoggingOut={setLoggingOut} />

      {/* Community Section */}
      <CommunityCard customerCategory={user.customer_category} />


      {/* Transactions Table */}
      <div className="bg-white dark:bg-gray-800 pmt-6 rounded-xl shadow overflow-hidden">
        <div className="p-4 border-b border-gray-200 dark:border-gray-700 font-semibold text-gray-700 dark:text-gray-200">
          Recent Transactions
        </div>

        <div className="relative max-h-[400px] overflow-y-auto divide-y divide-gray-200 dark:divide-gray-700 text-sm scrollbar-thin scrollbar-thumb-emerald-500 scrollbar-track-gray-200 dark:scrollbar-track-gray-900">
          {transactions.map((tx) => {
            const status = getStatus(tx.status);
            const time = new Date(tx.created_at).toLocaleString();

            return (
              <div key={tx.id} className="relative">
                <div
                  onClick={() => setOpenTransactionId(tx.id)}
                  className="px-4 py-3 flex justify-between items-center bg-gray-50 dark:bg-gray-900 border-b border-gray-100 dark:border-gray-700 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 transition rounded"
                >
                  <div>
                    <div className="font-semibold text-xs text-gray-800 dark:text-gray-100">
                      {tx.transaction_category?.toUpperCase()}
                    </div>
                    <div className="text-xs text-gray-500 dark:text-gray-400">{time}</div>
                  </div>

                  <div className="text-right">
                    <div className={`font-bold ${status.color}`}>
                      ₦{Number(tx.discounted_amount ?? tx.amount).toFixed(2)}
                    </div>
                    <div className={`text-xs ${status.color2}`}>{status.text}</div>
                  </div>
                </div>

                {/* Transaction Details Modal */}
                {openTransactionId === tx.id && (
                  <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                    <div className="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-sm w-full p-6">
                      <h2 className="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">
                        Transaction Details
                      </h2>

                      <div className="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                        <div className="flex justify-between">
                          <span>Plan:</span>
                          <span className="font-semibold">{tx.product_plan?.product_plan_name ?? "—"}</span>
                        </div>

                        <div className="flex justify-between">
                          <span>Phone:</span>
                          <span className="font-semibold">{tx.phone_number ?? "—"}</span>
                        </div>

                        <div className="flex justify-between">
                          <span>Discounted Amount:</span>
                          <span className="font-semibold">
                            ₦{Number(tx.discounted_amount ?? tx.amount).toFixed(2)}
                          </span>
                        </div>

                        <div className="flex justify-between">
                          <span>Amount:</span>
                          <span className="font-semibold">₦{Number(tx.amount).toFixed(2)}</span>
                        </div>

                        <div className="flex justify-between">
                          <span>Status:</span>
                          <span className={status.color2}>{status.text}</span>
                        </div>

                        <div className="flex justify-between">
                          <span>Date:</span>
                          <span>{time}</span>
                        </div>

                        <div className="flex justify-between">
                          <span>Category:</span>
                          <span>{tx.transaction_category?.toUpperCase()}</span>
                        </div>
                      </div>

                      <div className="mt-6 text-center">
                        <button
                          onClick={() => setOpenTransactionId(null)}
                          className="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded text-sm"
                        >
                          Close
                        </button>
                      </div>
                    </div>
                  </div>
                )}
              </div>
            );
          })}

          <div className="sticky bottom-0 text-center text-[11px] text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-900 py-1 border-t border-gray-200 dark:border-gray-700">
            Scroll to view more ⬇️
          </div>
        </div>
      </div>
    </DashboardLayout>
  );
}
