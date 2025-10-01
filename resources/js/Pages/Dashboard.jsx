// resources/js/Pages/Dashboard.jsx
import { useState } from "react";
import DashboardLayout from "@/Layouts/DashboardLayout";
import { Link, usePage } from "@inertiajs/react";
import ProductButtons from "@/Components/ProductButtons";
import InviteEarn from "@/Components/InviteEarn";
import CommunityCard from "@/Components/CommunityCard";
import WalletBalance from "@/Components/WalletBalance";
import Announcements from "@/Components/Announcements";

export default function Dashboard({ transactions: initialTransactions }) {
  const { props } = usePage();
  const { auth, announcements, impersonator } = props;
  const user = auth.user;

  const transactions = initialTransactions ?? props.transactions ?? [];
  const [showBalance, setShowBalance] = useState(true);
  const [openTransactionId, setOpenTransactionId] = useState(null);
  const [loggingOut, setLoggingOut] = useState(false);

  const [inviteOpen, setInviteOpen] = useState(false);
  const [copied, setCopied] = useState(false);

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

  return (
    <DashboardLayout title="Dashboard">
      {/* Wallet */}
      <WalletBalance user={user} />

      {/* Marketer/Admin Shortcut */}
      {(user.is_marketer === 1 || user.role?.role_name === "Admin") && (
        <Link href={route("marketer.dashboard")}>
          <div className="bg-green-800 text-white p-2 rounded-xl mb-4">
            <h1 className="text-center">Go to Marketer Dashboard</h1>
          </div>
        </Link>
      )}

      {/* Announcements Slider */}
      <Announcements announcements={announcements} />

      {/* Invite & Earn */}
      <InviteEarn referralLink={referralLink} />

      {/* Product Buttons */}
      <ProductButtons loggingOut={loggingOut} setLoggingOut={setLoggingOut} />

      {/* Community Section */}
      <CommunityCard customerCategory={user.customer_category} />

      {/* Transactions Table */}
      {/* ... rest of your code unchanged ... */}
    </DashboardLayout>
  );
}
