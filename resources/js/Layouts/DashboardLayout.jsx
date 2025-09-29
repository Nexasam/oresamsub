import { useEffect, useState } from "react";
import { Link, usePage } from "@inertiajs/react";
import Announcements from "@/Components/Announcements";

const getInitialTheme = () => {
  if (typeof window !== "undefined") {
    const stored = localStorage.getItem("theme");
    if (stored) return stored === "dark";
    return window.matchMedia("(prefers-color-scheme: dark)").matches;
  }
  return false; // default light
};

export default function DashboardLayout({ children }) {
  const { auth, announcements, impersonator } = usePage().props;
  const user = auth.user;


  const [showBalance, setShowBalance] = useState(true);
  const [darkMode, setDarkMode] = useState(getInitialTheme());

  // Initialize + sync dark mode
  useEffect(() => {
    const stored = localStorage.getItem("theme");
    const isDark =
      stored === "dark" ||
      (!stored && window.matchMedia("(prefers-color-scheme: dark)").matches);
    setDarkMode(isDark);
    document.documentElement.classList.toggle("dark", isDark);
  }, []);

  useEffect(() => {
    document.documentElement.classList.toggle("dark", darkMode);
    localStorage.setItem("theme", darkMode ? "dark" : "light");
  }, [darkMode]);

  return (
    <div className="space-y-6 pt-2 px-3 sm:px-6 dark:bg-gray-900 min-h-screen">
      {/* Announcements */}
      <Announcements announcements={announcements} />

      {/* Impersonation banner */}
      {impersonator && (
        <a href={route("admin.exit_impersonate")}>
          <div className="bg-green-800 text-white p-2 rounded-xl">
            You are viewing <u>{user.first_name}</u> as Admin. Click to EXIT.
          </div>
        </a>
      )}

      {/* Greeting + Dark mode toggle */}
      <div className="flex items-center justify-between mt-2">
        <h1 className="text-lg font-bold text-gray-800 dark:text-gray-100">
          👋 Hi, {user.username}
        </h1>
        <button
          onClick={() => setDarkMode((prev) => !prev)}
          className="flex items-center justify-center w-9 h-9 rounded-xl bg-white dark:bg-gray-800 
                     ring-1 ring-green-200 dark:ring-green-700 shadow-md hover:shadow-xl hover:scale-[1.05] 
                     transition transform text-gray-700 dark:text-gray-200"
        >
          {darkMode ? "🌞" : "🌙"}
        </button>
      </div>


      

      {/* Page-specific content */}
      <div>{children}</div>

      {/* Bottom Navigation */}
      <nav className="fixed bottom-0 inset-x-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 shadow-lg z-50">
        <div className="max-w-md mx-auto flex justify-around py-2 text-xs font-medium text-gray-700 dark:text-gray-200">
          {[
            { label: "Dashboard", icon: "🏠", route: "inertia.dashboard.index", inertia: true },
            { label: "Data", icon: "📶", route: "inertia.data.index", inertia: true },
            { label: "Airtime", icon: "📞", route: "inertia.airtime.index", inertia: true },
            { label: "Cable", icon: "📺", route: "ore.cable", inertia: false },
            { label: "Electricity", icon: "⚡", route: "ore.electricity", inertia: false },
          ].map((item) =>
            item.inertia ? (
              <Link
                key={item.label}
                href={route(item.route)}
                className="flex flex-col items-center hover:text-blue-600 dark:hover:text-blue-400"
              >
                <div className="text-xl">{item.icon}</div>
                <span>{item.label}</span>
              </Link>
            ) : (
              <a
                key={item.label}
                href={route(item.route)}
                className="flex flex-col items-center hover:text-blue-600 dark:hover:text-blue-400"
              >
                <div className="text-xl">{item.icon}</div>
                <span>{item.label}</span>
              </a>
            )
          )}
        </div>
      </nav>


    </div>
  );
}
