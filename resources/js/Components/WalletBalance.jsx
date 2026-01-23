// resources/js/Components/WalletBalance.jsx
import { useState } from "react";
import { Link } from "@inertiajs/react";

export default function WalletBalance({ user }) {
  const [showBalance, setShowBalance] = useState(true);

  return (
    <div className="relative bg-emerald-600 dark:bg-emerald-700 text-white p-4 rounded-xl shadow-md flex items-center justify-between">
      <div>
        <p className="text-xs text-white/70 font-medium">Wallet Balanceeee</p>
        <div className="flex items-center space-x-1 text-xl font-bold">
          {showBalance ? (
            <span>
              ₦
              {Number(user.main_wallet).toLocaleString("en-NG", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
              })}
            </span>
          ) : (
            <span className="tracking-widest">•••••</span>
          )}
          <button
            onClick={() => setShowBalance((prev) => !prev)}
            className="ml-2 hover:text-white/90 transition"
            title="Toggle Balance"
          >
            {showBalance ? "🙈" : "👁️"}
          </button>
        </div>
      </div>
      <Link
        href={route("inertia.virtual_accounts.index")}
        className="text-sm font-semibold underline hover:text-white/90 transition"
      >
        + Fund Wallet
      </Link>
    </div>
  );
}
