import { useState } from "react";
import { FaWhatsapp, FaFacebookF, FaInstagram, FaTiktok } from "react-icons/fa";

export default function InviteEarn({ referralLink }) {
  const [inviteOpen, setInviteOpen] = useState(true);
  const [copied, setCopied] = useState(false);

  const copyReferral = () => {
    navigator.clipboard.writeText(referralLink).then(() => {
      setCopied(true);
      setTimeout(() => setCopied(false), 2000);
    });
  };

  return (
    <div className="mt-3 border border-emerald-400 dark:border-emerald-600 rounded-lg shadow-sm overflow-hidden">
    {/* Accordion Toggle */}
    <button
      onClick={() => setInviteOpen(!inviteOpen)}
      className="w-full flex justify-between items-center bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600 text-white px-3 py-1.5 text-[11px] font-semibold hover:opacity-95 transition"
    >
      <span className="flex items-center gap-1.5">
        <span className="animate-pulse">🎉</span>
        <span>Invite & Earn</span>
      </span>
      <svg
        className={`w-3.5 h-3.5 transform transition-transform ${inviteOpen ? "rotate-180" : ""}`}
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>
  
    {/* Accordion Content */}
    {inviteOpen && (
      <div className="bg-white dark:bg-gray-800 px-3 py-2 space-y-2 text-[11px]">
        <p className="text-gray-600 dark:text-gray-400 leading-snug">
          Refer friends and earn on every transaction they make 💰
        </p>
  
        {/* Referral Link */}
        <div className="flex items-center bg-gray-100 dark:bg-gray-700 rounded overflow-hidden">
          <input
            type="text"
            readOnly
            value={referralLink}
            className="flex-grow px-2 py-1 bg-transparent text-[11px] text-gray-700 dark:text-gray-200 focus:outline-none"
          />
          <button
            onClick={copyReferral}
            className="px-2 py-1 bg-emerald-500 hover:bg-emerald-600 text-white text-[10px] font-medium"
          >
            {copied ? "✓" : "Copy"}
          </button>
        </div>
  
        {copied && (
          <span className="text-[10px] text-emerald-500 block">
            Link copied!
          </span>
        )}
  
        {/* Share Buttons */}
        <div className="flex gap-2 pt-1">
          <a
            href={`https://wa.me/?text=Join me on OresamSub 👉 ${referralLink}`}
            target="_blank"
            className="w-7 h-7 flex items-center justify-center bg-green-500 hover:bg-green-600 rounded-full text-white"
          >
            <FaWhatsapp size={14} />
          </a>
          <a
            href={`https://www.facebook.com/sharer/sharer.php?u=${referralLink}`}
            target="_blank"
            className="w-7 h-7 flex items-center justify-center bg-blue-600 hover:bg-blue-700 rounded-full text-white"
          >
            <FaFacebookF size={14} />
          </a>
          <a
            href={`https://www.instagram.com/?url=${referralLink}`}
            target="_blank"
            className="w-7 h-7 flex items-center justify-center bg-pink-500 hover:bg-pink-600 rounded-full text-white"
          >
            <FaInstagram size={14} />
          </a>
          <a
            href={`https://www.tiktok.com/share?url=${referralLink}`}
            target="_blank"
            className="w-7 h-7 flex items-center justify-center bg-black hover:bg-gray-800 rounded-full text-white"
          >
            <FaTiktok size={14} />
          </a>
        </div>
      </div>
    )}
    </div>
  
  );
}
