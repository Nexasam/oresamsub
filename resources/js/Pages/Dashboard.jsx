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

      {/* Transactions */}
      <div className="bg-white dark:bg-gray-800 mt-6 rounded-xl shadow overflow-hidden">
        {/* <div className="p-4 font-semibold border-b"> */}
        <div className="text-lg font-semibold mt-1 text-[11px] text-gray-600 dark:text-gray-200">
          Recent Transactions
        </div>

        <div className="max-h-[400px] overflow-y-auto divide-y text-sm">
          {transactions.map((tx) => {
            const status = getStatus(tx.status);
            const time = new Date(tx.created_at).toLocaleString();

            return (
              <div key={tx.id} onClick={() => setOpenTransactionId(tx.id)}
                className="px-4 py-3 flex justify-between cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-900">

                <div>
                  <div className="font-semibold text-xs">
                    {tx.transaction_category?.toUpperCase()}
                  </div>
                  <div className="text-xs text-gray-500">{time}</div>
                </div>

                <div className="text-right">
                  <div className={`font-bold ${status.color}`}>
                    ₦{Number(tx.amount).toFixed(2)}
                  </div>
                  <div className={`text-xs ${status.color2}`}>
                    {status.text}
                  </div>
                </div>
              </div>
            );
          })}
        </div>
      </div>

    </DashboardLayout>
  );
}