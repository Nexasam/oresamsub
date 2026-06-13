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

  const transactions = initialTransactions ?? props.transactions ?? [];

  const [openTransactionId, setOpenTransactionId] = useState(null);
  const [selectedTx, setSelectedTx] = useState(null);
  const [loggingOut, setLoggingOut] = useState(false);

  const [availableState, setAvailableState] = useState(available);
  const [pendingState, setPendingState] = useState(pending);
  const [loading, setLoading] = useState(false);

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

    <div className="max-h-[400px] overflow-y-auto divide-y divide-gray-200 dark:divide-gray-700 text-sm">
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
                <span className="font-semibold">{selectedTx.user_screen_message}</span>
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

    </DashboardLayout>
  );
}