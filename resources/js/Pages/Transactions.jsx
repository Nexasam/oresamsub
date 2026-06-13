import { useState } from "react";
import { usePage, Link } from "@inertiajs/react";
import DashboardLayout from "@/Layouts/DashboardLayout";
import WalletBalance from "@/Components/WalletBalance";


export default function Transactions() {
  const { props } = usePage();
  const { auth, transactions } = props;
  const user = auth.user;

  const [selectedTx, setSelectedTx] = useState(null);
  const [showBalance, setShowBalance] = useState(true);

  const getStatus = (status) => {
    switch (status) {
      case 1:
        return { text: "Success", color: "text-green-500", color2: "text-green-600" };
      case 0:
        return { text: "Pending", color: "text-yellow-500", color2: "text-yellow-600" };
      case -1:
        return { text: "Unsuccessful", color: "text-red-500", color2: "text-red-600" };
      case 2:
        return { text: "Refunded", color: "text-blue-500", color2: "text-blue-600" };
      default:
        return { text: "Unknown", color: "text-gray-500", color2: "text-gray-600" };
    }
  };

  return (
    <DashboardLayout  title="Transactions">
      {/* Wallet Balance + Top Up */}
      <WalletBalance user={user} />

      {/* Back Navigation */}
      <Link
        href={route("dashboard")}
        className="inline-flex items-center px-4 py-2 mb-4 mt-4 rounded-lg bg-gradient-to-r from-emerald-600 to-emerald-500 text-white text-sm font-medium shadow hover:from-emerald-700 hover:to-emerald-600 transition font-inter"
      >
        ← Back to Dashboard
      </Link>

      {/* Transactions Card */}
      <div className="bg-white dark:bg-gray-800 text-gray-700 dark:text-white mt-4 pb-16 rounded-xl shadow overflow-hidden font-inter">
        <div className="p-4 border-b border-gray-200 dark:border-gray-700 font-semibold text-gray-700 dark:text-white">
          Transactions
        </div>

        <div className="p-4 space-y-3 max-h-[650px] overflow-y-auto scrollbar-thin scrollbar-thumb-emerald-500 scrollbar-track-transparent">
          {transactions && transactions.length > 0 ? (
            transactions.map((tx, idx) => {
              const status = getStatus(Number(tx.status));
              return (
                <div
                  key={idx}
                  onClick={() => setSelectedTx(tx)}
                  className="p-3 bg-white dark:bg-gray-900 rounded-lg border border-gray-100 dark:border-gray-800 shadow-sm cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition"
                >
                  <div className="flex justify-between items-center">
                    <div>
                      <div className="text-xs font-semibold text-gray-800 dark:text-gray-200">
                        {tx.transaction_category?.toUpperCase()}
                      </div>
                      <div className="text-xs text-gray-500 dark:text-gray-400">
                        {new Date(tx.created_at).toLocaleString("en-NG", {
                          month: "short",
                          day: "numeric",
                          hour: "numeric",
                          minute: "numeric",
                        })}
                      </div>
                    </div>
                    <div className="text-right">
                      <div className={`font-bold ${status.color}`}>
                        ₦{Number(tx.discounted_amount ?? tx.amount).toLocaleString("en-NG")}
                      </div>
                      <div className={`text-xs ${status.color2}`}>{status.text}</div>
                    </div>
                  </div>
                </div>
              );
            })
          ) : (
            <p className="text-center text-sm text-gray-500">
              No transactions available yet.
            </p>
          )}
        </div>
      </div>

      {/* Transaction Modal */}
      {selectedTx && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
          <div className="bg-white dark:bg-gray-800 rounded-xl shadow-lg max-w-sm w-full p-6 font-inter">
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
              <div className="flex justify-between">
                <span>Phone Recharged:</span>
                <span className="font-semibold">{selectedTx.phone_number}</span>
              </div>
              <div className="flex justify-between">
                <span>Amount:</span>
                <span className="font-semibold">
                  ₦{Number(selectedTx.discounted_amount ?? selectedTx.amount).toLocaleString("en-NG")}
                </span>
              </div>
              {/* <div className="flex justify-between">
                <span>Amount:</span>
                <span className="font-semibold">
                  ₦{Number(selectedTx.amount).toLocaleString("en-NG")}
                </span>
              </div> */}


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
                <span className={getStatus(Number(selectedTx.status)).color2}>
                  {getStatus(Number(selectedTx.status)).text}
                </span>
              </div>
              <div className="flex justify-between">
                <span>Date:</span>
                <span>
                  {new Date(selectedTx.created_at).toLocaleString("en-NG", {
                    month: "short",
                    day: "numeric",
                    hour: "numeric",
                    minute: "numeric",
                  })}
                </span>
              </div>
              <div className="flex justify-between">
                <span>Category:</span>
                <span>{selectedTx.transaction_category?.toUpperCase()}</span>
              </div>
            </div>

            <div className="my-2 text-sm text-gray-700 dark:text-gray-300">
                <span>Message:</span> <br />
                <span className="font-semibold">{selectedTx.user_screen_message}</span>
              </div>

            <div className="mt-6 text-center">
              <button
                onClick={() => setSelectedTx(null)}
                className="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md text-sm shadow"
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
