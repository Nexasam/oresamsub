import { useState } from "react";
import { FaWhatsapp, FaFacebookF, FaInstagram, FaTiktok } from "react-icons/fa";

export default function InviteEarn({ referralLink }) {
  const [inviteOpen, setInviteOpen] = useState(false);
  const [copied, setCopied] = useState(false);

  const copyReferral = () => {
    navigator.clipboard.writeText(referralLink).then(() => {
      setCopied(true);
      setTimeout(() => setCopied(false), 2000);
    });
  };

  return (
    <div className="mt-4 border border-emerald-400 dark:border-emerald-600 rounded-xl shadow-md overflow-hidden">
      {/* Accordion Toggle */}
      <button
        onClick={() => setInviteOpen(!inviteOpen)}
        className="w-full flex justify-between items-center bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600 text-white px-3 py-2 text-xs font-semibold shadow-sm hover:shadow-md transition-all duration-300"
      >
        <span className="flex items-center space-x-2">
          <span className="animate-bounce text-sm">🎉</span>
          <span>Invite & Earn</span>
        </span>
        <svg
          className={`w-4 h-4 transform transition-transform ${inviteOpen ? "rotate-180" : ""}`}
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 9l-7 7-7-7" />
        </svg>
      </button>

      {/* Accordion Content */}
      {inviteOpen && (
        <div className="bg-white dark:bg-gray-800 px-4 py-3 space-y-3 text-sm">
          <p className="text-gray-600 dark:text-gray-400">
            Buy airtime, data, and pay bills at affordable rates — get started now! 🚀
          </p>

          {/* Referral Link */}
          <div className="flex items-center bg-gray-100 dark:bg-gray-700 rounded-md overflow-hidden">
            <input
              type="text"
              readOnly
              value={referralLink}
              className="flex-grow px-2 py-1 text-sm bg-transparent border-none focus:outline-none text-gray-700 dark:text-gray-200"
            />
            <button
              onClick={copyReferral}
              className="px-2 py-1 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-medium"
            >
              {copied ? "✅" : "Copy"}
            </button>
          </div>

          {copied && <span className="text-xs text-emerald-500 block">✅ Link copied!</span>}

          {/* Share Buttons */}
          {/* <div className="flex space-x-2">
            <a
              href={`https://wa.me/?text=Join me on OresamSub 👉 ${referralLink}`}
              target="_blank"
              className="flex items-center justify-center w-8 h-8 bg-green-500 hover:bg-green-600 rounded-full text-white"
            >
              <i className="fab fa-whatsapp"></i>
            </a>
            <a
              href={`https://www.facebook.com/sharer/sharer.php?u=${referralLink}`}
              target="_blank"
              className="flex items-center justify-center w-8 h-8 bg-blue-600 hover:bg-blue-700 rounded-full text-white"
            >
              <i className="fab fa-facebook-f"></i>
            </a>
            <a
              href={`https://www.instagram.com/?url=${referralLink}`}
              target="_blank"
              className="flex items-center justify-center w-8 h-8 bg-pink-500 hover:bg-pink-600 rounded-full text-white"
            >
              <i className="fab fa-instagram"></i>
            </a>
            <a
              href={`https://www.tiktok.com/share?url=${referralLink}`}
              target="_blank"
              className="flex items-center justify-center w-8 h-8 bg-black hover:bg-gray-800 rounded-full text-white"
            >
              <i className="fab fa-tiktok"></i>
            </a>
          </div> */}

          <div className="flex space-x-2">
            <a
              href={`https://wa.me/?text=Join me on OresamSub 👉 ${referralLink}`}
              target="_blank"
              className="flex items-center justify-center w-8 h-8 bg-green-500 hover:bg-green-600 rounded-full text-white"
            >
              <FaWhatsapp size={16} />
            </a>
            <a
              href={`https://www.facebook.com/sharer/sharer.php?u=${referralLink}`}
              target="_blank"
              className="flex items-center justify-center w-8 h-8 bg-blue-600 hover:bg-blue-700 rounded-full text-white"
            >
              <FaFacebookF size={16} />
            </a>
            <a
              href={`https://www.instagram.com/?url=${referralLink}`}
              target="_blank"
              className="flex items-center justify-center w-8 h-8 bg-pink-500 hover:bg-pink-600 rounded-full text-white"
            >
              <FaInstagram size={16} />
            </a>
            <a
              href={`https://www.tiktok.com/share?url=${referralLink}`}
              target="_blank"
              className="flex items-center justify-center w-8 h-8 bg-black hover:bg-gray-800 rounded-full text-white"
            >
              <FaTiktok size={16} />
            </a>
          </div>


        </div>
      )}
    </div>
  );
}
