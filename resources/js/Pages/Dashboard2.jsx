import { useState, useEffect } from "react";
import { Link, usePage, router } from "@inertiajs/react";
import Announcements from "../Components/Announcements";
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faSun, faMoon } from '@fortawesome/free-solid-svg-icons';
import { route as ziggyRoute } from 'ziggy-js';
import ProductButtons from '../Components/ProductButtons'

const getInitialTheme = () => {
  if (typeof window !== "undefined") {
    const stored = localStorage.getItem("theme");
    if (stored) return stored === "dark";
    return window.matchMedia("(prefers-color-scheme: dark)").matches;
  }
  return false; // default light
};

export default function Dashboard() {
  const { props } = usePage();
  const { auth, transactions, announcements, csrf_token } = props;
  const user = auth.user;

  const [showBalance, setShowBalance] = useState(true);
  const [copied, setCopied] = useState(false);
  const [openTransactionId, setOpenTransactionId] = useState(null);
  const [darkMode, setDarkMode] = useState(getInitialTheme());
  const [loggingOut, setLoggingOut] = useState(false);

  // Read theme outside component to avoid flash
 

  // Immediately set document class so no flash
  useEffect(() => {
    document.documentElement.classList.toggle("dark", darkMode);
    localStorage.setItem("theme", darkMode ? "dark" : "light");
  }, [darkMode]);


  // Initialize dark mode
  useEffect(() => {
    const stored = localStorage.getItem("theme");
    if (stored === "dark" || (!stored && window.matchMedia("(prefers-color-scheme: dark)").matches)) {
      setDarkMode(true);
      document.documentElement.classList.add("dark");
    } else {
      setDarkMode(false);
      document.documentElement.classList.remove("dark");
    }
  }, []);

  // Sync dark mode toggle
  useEffect(() => {
    document.documentElement.classList.toggle("dark", darkMode);
    localStorage.setItem("theme", darkMode ? "dark" : "light");
  }, [darkMode]);

  const referralLink = `${window.location.origin}/register?ref=${user.phone_number}`;

  const copyReferral = () => {
    navigator.clipboard.writeText(referralLink);
    setCopied(true);
    setTimeout(() => setCopied(false), 2000);
  };


  // const actionButtons = [
  //   { label: "Airtime", icon: "📞", route: "ore.airtime" },
  //   { label: "Data", icon: "📶", route: 'inertia.data.index' },
  //   { label: "Power", icon: "⚡", route: "ore.electricity" },
  //   { label: "Cable", icon: "📺", route: "ore.cable" },
  //   { label: "Transactions", icon: "🧾", route: "ore.transactions" },
  //   {
  //     label: loggingOut ? "Logging out…" : "Logout",
  //     icon: "🚪",
  //     color: "red",
  //     action: () => {
  //       if (!loggingOut) {
  //         setLoggingOut(true);

  //         router.post("/logout2", {
  //           // optional: you can include CSRF token if needed
  //         }, {
  //           replace: true,      // replaces the current history entry
  //           preserveState: false // clears Inertia state
  //         });
  //       }
  //     }
  //   },
  // ];



  
  // Helper function to safely get a Ziggy route
  const getRoute = (name) => {
    try {
      return ziggyRoute(name);
    } catch (e) {
      console.warn(`Ziggy route "${name}" not found.`);
      return "#"; // fallback so button still renders
    }
  };

  

  const socialPlatforms = ["whatsapp", "facebook-f", "instagram", "tiktok"];

  const getStatus = (status) => {
    switch (status) {
      case '1': return { text: "Success", color: "text-green-500", color2: "text-green-600" };
      case '0': return { text: "Pending", color: "text-yellow-500", color2: "text-yellow-600" };
      case '-1': return { text: "Unsuccessful", color: "text-red-500", color2: "text-red-600" };
      case '2': return { text: "Refunded", color: "text-blue-500", color2: "text-blue-600" };
      default: return { text: "Unknown", color: "text-gray-500", color2: "text-gray-600" };
    }
  };

  return (
    <div className="space-y-6 pt-2 px-3 sm:px-6 dark:bg-gray-900 min-h-screen">
      {/* Announcements */}
      <Announcements announcements={announcements} />

      {/* Impersonation */}
      {props.impersonator && (
        <a href={route("admin.exit_impersonate")}>
          <div className="bg-green-800 text-white p-2 rounded-xl">
            <h1>
              You are now viewing <u>{user.first_name} {user.pin}</u> as an Administrator.
            </h1>
            <div className="text-lg font-semibold">Click to EXIT User Account</div>
          </div>
        </a>
      )}

      {/* Marketer Link */}
      {(user.is_marketer === 1 || user.role?.role_name === "Admin") && (
        <a href={route("marketer.dashboard")}>
          <div className="bg-green-800 text-white p-2 rounded-xl text-center">
            Go to Marketer Dashboard
          </div>
        </a>
      )}

      {/* Greeting + Dark Mode Toggle */}
      <div className="flex items-center justify-between mt-2">
        <h1 className="text-lg font-bold text-gray-800 dark:text-gray-100">
          👋 Hi, {user.username}
        </h1>
        <button
          onClick={() => setDarkMode(prev => !prev)}
          className="flex items-center justify-center w-9 h-9 rounded-xl bg-white dark:bg-gray-800 
                    ring-1 ring-green-200 dark:ring-green-700 shadow-md hover:shadow-xl hover:scale-[1.05] 
                    transition transform text-gray-700 dark:text-gray-200"
        >
          <span className="text-sm">{darkMode ? "🌞" : "🌙"}</span>
        </button>
      </div>

      {/* Wallet Balance */}
      <WalletBalance user={user} />

      {/* Invite & Earn */}
      <div className="border border-emerald-400 dark:border-emerald-600 rounded-xl shadow-md overflow-hidden">
        <button
          onClick={() => setCopied(prev => !prev)}
          className="w-full flex justify-between items-center bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600
                     dark:from-emerald-600 dark:via-teal-600 dark:to-emerald-700 text-white px-3 py-2 text-xs font-semibold rounded-md"
        >
          🎉 Invite & Earn
          <svg
            className={`w-4 h-4 transform transition-transform ${copied ? "rotate-180" : ""}`}
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>

        {copied && (
          <div className="bg-gray-50 dark:bg-gray-800 px-4 py-3 text-sm space-y-2 rounded-b-md">
            <p className="text-gray-700 dark:text-gray-300">
              Buy airtime, data, and pay bills at affordable rates — get started now! 🚀
            </p>
            <div className="flex items-center bg-gray-100 dark:bg-gray-700 rounded-md overflow-hidden">
              <input
                type="text"
                readOnly
                value={referralLink}
                className="flex-grow px-2 py-1 text-sm bg-transparent border-none focus:outline-none text-gray-800 dark:text-gray-200"
              />
              <button
                onClick={copyReferral}
                className="px-2 py-1 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-medium"
              >
                {copied ? "✅" : "📋"}
              </button>
            </div>

            <div className="flex space-x-2 mt-2">
              {socialPlatforms.map((platform) => (
                <a
                  key={platform}
                  href="#"
                  className={`flex items-center justify-center w-8 h-8 rounded-full text-white ${
                    platform === "whatsapp" ? "bg-green-500 hover:bg-green-600" :
                    platform === "facebook-f" ? "bg-blue-600 hover:bg-blue-700" :
                    platform === "instagram" ? "bg-pink-500 hover:bg-pink-600" :
                    "bg-black hover:bg-gray-800"
                  }`}
                >
                  <i className={`fab fa-${platform === "tiktok" ? "tiktok" : platform}`}></i>
                </a>
              ))}
            </div>
          </div>
        )}
      </div>


      <ProductButtons loggingOut={loggingOut} setLoggingOut={setLoggingOut} />
    


      {/* <div className="grid grid-cols-3 sm:grid-cols-4 gap-3 text-center text-sm md:text-base mt-4">
        {actionButtons.map((item) => (
          item.action ? (
            <button
              key={item.label}
              onClick={item.action}
              className={`group p-3 rounded-xl shadow hover:shadow-md transition transform hover:scale-[1.05] ${
                item.color === "red" ? "bg-red-500 text-white" : "bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200"
              }`}
            >
              <div className="w-10 h-10 mx-auto rounded-full bg-gradient-to-r from-emerald-500 to-green-500 flex items-center justify-center text-white text-xl shadow-sm">
                {item.icon}
              </div>
              <div className="mt-2 font-medium text-[13px] md:text-sm">
                {item.label}
              </div>
            </button>
          ) : (
            <Link
              key={item.label}
              href={route(item.route)}
              className={`group p-3 rounded-xl shadow hover:shadow-md transition transform hover:scale-[1.05] ${
                item.color === "red" ? "bg-red-500 text-white" : "bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200"
              }`}
            >
              <div className="w-10 h-10 mx-auto rounded-full bg-gradient-to-r from-emerald-500 to-green-500 flex items-center justify-center text-white text-xl shadow-sm">
                {item.icon}
              </div>
              <div className="mt-2 font-medium text-[13px] md:text-sm">
                {item.label}
              </div>
            </Link>
          )
        ))}
      </div> */}

   


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
                        {tx.transaction_category.toUpperCase()}
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
                            <span className="font-semibold">{tx.product_plan?.product_plan_name}</span>
                          </div>
                          <div className="flex justify-between">
                            <span>Phone:</span>
                            <span className="font-semibold">{tx.phone_number}</span>
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
                            <span>{tx.transaction_category.toUpperCase()}</span>
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


        {/* Bottom Navigation */}
      <nav className="fixed bottom-0 inset-x-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 shadow-lg z-50">
        <div className="max-w-md mx-auto flex justify-around py-2 text-xs font-medium text-gray-700 dark:text-gray-200">
          {[
            { label: "Dashboard", icon: "🏠", route: "dashboard" },
            { label: "Data", icon: "📶", route: "ore.data" },
            { label: "Airtime", icon: "📞", route: "ore.airtime" },
            { label: "Cable", icon: "📺", route: "ore.cable" },
            { label: "Electricity", icon: "⚡", route: "ore.electricity" },
            { label: "Profile", icon: "👤", route: "dashboard" }, // replace with actual profile route
          ].map((item) => (
            <Link
              key={item.label}
              href={route(item.route)}
              className="flex flex-col items-center hover:text-blue-600 dark:hover:text-blue-400"
              onClick={() => {
                // optional loader before navigation
                window.location.href = route(item.route);
              }}
            >
              <div className="text-xl">{item.icon}</div>
              <span>{item.label}</span>
            </Link>
          ))}
        </div>
      </nav>




    </div>
  );
}
