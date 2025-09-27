// resources/js/Pages/Dashboard.jsx
import { useState } from "react";
import DashboardLayout from "@/Layouts/DashboardLayout";
import { Link, usePage } from "@inertiajs/react";
import ProductButtons from "@/Components/ProductButtons";


export default function Dashboard({ transactions: initialTransactions }) {
  // Inertia might inject transactions either via props param or via usePage
  const { props } = usePage();
  const { auth, announcements, impersonator } = props;
  const user = auth.user;


  const transactions = initialTransactions ?? props.transactions ?? [];
  const [showBalance, setShowBalance] = useState(true);


  // Page-local state for transactions interactions
  const [openTransactionId, setOpenTransactionId] = useState(null);
  const [loggingOut, setLoggingOut] = useState(false);

  
  

  // Convert status to readable text + tailwind color classes
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

  return (
    <DashboardLayout>


      {/* Wallet */}
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

       {/* Referral Section */}
       <div className="bg-emerald-50 dark:bg-gray-800 p-4 rounded-xl shadow-sm mt-4">
        <h2 className="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
          Invite a Friend
        </h2>
        <p className="text-xs text-gray-600 dark:text-gray-400 mb-2">
          Share your referral link and earn rewards when they sign up.
        </p>
        <div className="flex items-center justify-between">
          <input
            type="text"
            readOnly
            value={`https://oresamsub.com/register?ref=${user.phone_number}`}
            className="flex-1 rounded-lg px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200"
          />
          <button
            onClick={() =>
              navigator.clipboard.writeText(
                `https://oresamsub.com/register?ref=${user.phone_number}`
              )
            }
            className="ml-2 px-3 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition"
          >
            Copy
          </button>
        </div>
      </div>

      {/* Community Section */}
      <div className="bg-emerald-50 dark:bg-gray-800 p-4 rounded-xl shadow-sm mt-4">
        <h2 className="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
          Community
        </h2>
        <p className="text-xs text-gray-600 dark:text-gray-400 mb-2">
          Join our community to connect with other users, share tips, and stay updated.
        </p>
        <Link
          // href={route("community.index")}
          className="inline-block text-emerald-600 dark:text-emerald-400 text-sm font-medium hover:underline"
        >
          Go to Community
        </Link>
      </div>

      {/* Product Buttons */}
      <ProductButtons loggingOut={loggingOut} setLoggingOut={setLoggingOut} />

      {/* Transactions Table */}
      <div className="bg-white dark:bg-gray-800 mt-6 rounded-xl shadow overflow-hidden">
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
