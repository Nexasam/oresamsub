import { useState } from "react";
import { router } from "@inertiajs/react";
import axios from "axios";
import Swal from "sweetalert2";

export default function BonusWallet({ bonus = {} }) {
  const [balance, setBalance] = useState(Number(bonus.balance || 0));
  const [transferring, setTransferring] = useState(false);
  const activeRewards = bonus.active_rewards?.length || 0;

  const transferToMainWallet = async () => {
    if (balance <= 0 || transferring) return;

    const confirmation = await Swal.fire({
      title: "Move bonus to main wallet?",
      html: `Your available bonus of <b>₦${balance.toLocaleString("en-NG", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      })}</b> will be added to your main wallet.`,
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Yes, move bonus",
      cancelButtonText: "Cancel",
      confirmButtonColor: "#7c3aed",
    });

    if (!confirmation.isConfirmed) return;

    try {
      setTransferring(true);
      const { data } = await axios.post(route("bonuses.convert"));
      setBalance(Number(data.bonus_wallet_balance || 0));
      await Swal.fire("Bonus transferred", data.message, "success");
      router.reload({ preserveScroll: true });
    } catch (error) {
      Swal.fire(
        "Unable to transfer bonus",
        error?.response?.data?.message || "Please try again.",
        "error"
      );
    } finally {
      setTransferring(false);
    }
  };

  return (
    <div className="relative overflow-hidden rounded-xl bg-gradient-to-br from-violet-600 via-purple-600 to-fuchsia-600 p-4 text-white shadow-lg">
      <div className="absolute -right-8 -top-10 h-28 w-28 rounded-full border-[20px] border-white/10" />
      <div className="relative flex items-center justify-between gap-4">
        <div>
          <div className="flex items-center gap-2 text-xs font-semibold text-white/75">
            <span>🎁</span>
            <span>Campaign Bonus Wallet</span>
          </div>
          <div className="mt-1 text-2xl font-bold">
            ₦{balance.toLocaleString("en-NG", {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2,
            })}
          </div>
          <p className="mt-1 text-[11px] text-white/75">
            {activeRewards > 0
              ? `${activeRewards} active campaign reward${activeRewards === 1 ? "" : "s"}`
              : "Campaign rewards will appear here"}
          </p>
        </div>

        <button
          type="button"
          onClick={transferToMainWallet}
          disabled={balance <= 0 || transferring}
          className="rounded-lg bg-white px-3 py-2 text-xs font-bold text-purple-700 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md disabled:cursor-not-allowed disabled:opacity-50"
        >
          {transferring ? "Moving..." : "Move to main wallet"}
        </button>
      </div>
    </div>
  );
}
