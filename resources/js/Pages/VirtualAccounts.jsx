import { useState } from "react";
import { usePage, Link } from "@inertiajs/react";
import DashboardLayout from "@/Layouts/DashboardLayout";
import WalletBalance from "@/Components/WalletBalance";


export default function VirtualAccounts() {
  const { props } = usePage();
  const { auth, virtualccts } = props;
  const user = auth.user;

  const [copiedAcct, setCopiedAcct] = useState(null);
  const [showBalance, setShowBalance] = useState(true);
  const [amount, setAmount] = useState(""); // <-- NEW: state for amount

 // Message dynamically includes amount
 const message = `I just made a manual payment to OresamSub account.
 Please check and verify.
 
 Username: ${user.username}
 Email: ${user.email}
 Phone: ${user.phone_number}
 Amount Sent: ${amount || "N/A"}`;

  const handleCopy = (accountNumber) => {
    navigator.clipboard.writeText(accountNumber);
    setCopiedAcct(accountNumber);
    setTimeout(() => setCopiedAcct(null), 2000);
  };

  return (
    <DashboardLayout  title="Virtual Accounts">
      {/* Wallet Balance */}
      <WalletBalance user={user} />

      {/* Back Navigation */}
      <Link
        href={route("dashboard")}
        className="inline-flex items-center px-4 py-2 mb-4 mt-6 rounded-lg bg-gradient-to-r from-emerald-600 to-emerald-500 text-white text-sm font-medium shadow hover:from-emerald-700 hover:to-emerald-600 transition font-inter"
      >
        ← Back to Dashboard
      </Link>

      {/* Full Page Card */}
      <div className="bg-white dark:bg-gray-800 text-gray-700 dark:text-white mt-6 pb-16 rounded-xl shadow overflow-hidden font-inter">
        <div className="p-4 border-b border-gray-200 dark:border-gray-700 font-semibold text-gray-700 dark:text-white">
          Fund any of the accounts below:
        </div>

      



      {virtualccts && virtualccts.length > 0 ? (
        virtualccts.map((account, idx) => (
          <div
            key={idx}
            className="p-4 bg-white dark:bg-gray-900 rounded-xl shadow space-y-1 border border-emerald-100 dark:border-emerald-800"
          >
            <div className="flex items-center justify-between">
              <div className="font-semibold text-emerald-600 dark:text-emerald-400">
                {account.bank_name}
              </div>
              {copiedAcct === account.account_number && (
                <span className="text-xs text-emerald-500">Copied ✅</span>
              )}
            </div>

            {/* Account Name */}
            <div className="text-sm text-gray-700 dark:text-gray-300">
              Acct Name: {account.account_name}
            </div>

            {/* Bank Description */}
            {account.bank_description && (
              <div className="text-xs text-gray-500 dark:text-gray-400 mt-1">
                {account.bank_description}
              </div>
            )}

            {/* Account Number + Copy */}
            <div className="flex justify-between items-center mt-1">
              <div className="text-lg font-mono tracking-wide text-gray-900 dark:text-white">
                {account.account_number}
              </div>
              <button
                onClick={() => handleCopy(account.account_number)}
                className="text-xs px-3 py-1 rounded-md bg-emerald-600 hover:bg-emerald-700 text-white dark:bg-emerald-500 dark:hover:bg-emerald-600 transition"
              >
                Copy
              </button>
            </div>
          </div>
        ))
      ) : (
        <p className="text-center text-sm text-gray-500">
          No virtual accounts available yet.
        </p>
      )}


      </div>
    </DashboardLayout>
  );
}
