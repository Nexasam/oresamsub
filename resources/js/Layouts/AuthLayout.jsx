import { useState, useEffect } from "react";
import { usePage } from "@inertiajs/react";
import { Head } from "@inertiajs/react";



// Read theme outside component to avoid flash
const getInitialTheme = () => {
  if (typeof window !== "undefined") {
    const stored = localStorage.getItem("theme");
    if (stored) return stored === "dark";
    return window.matchMedia("(prefers-color-scheme: dark)").matches;
  }
  return false; // default light
};

export default function AuthLayout({ children, title }) {
  const { props } = usePage();
  const { auth } = props;

  const [darkMode, setDarkMode] = useState(getInitialTheme());
  const [showLoader, setShowLoader] = useState(false);
  const [checkingAuth, setCheckingAuth] = useState(true); // new flag

  

  useEffect(() => {
    // If authenticated, redirect immediately
    if (auth?.user) {
      window.location.href = "/dashboard"; // full redirect
      // window.location.href = route("inertia.dashboard.index"); // full redirect
    } else {
      setCheckingAuth(false); // allow rendering login form
    }
  }, [auth]);

  // Initialize dark mode
  useEffect(() => {
    const stored = localStorage.getItem("theme");
    if (
      stored === "dark" ||
      (!stored && window.matchMedia("(prefers-color-scheme: dark)").matches)
    ) {
      setDarkMode(true);
      document.documentElement.classList.add("dark");
    } else {
      setDarkMode(false);
      document.documentElement.classList.remove("dark");
    }
  }, []);

  // Sync toggle
  useEffect(() => {
    document.documentElement.classList.toggle("dark", darkMode);
    localStorage.setItem("theme", darkMode ? "dark" : "light");
  }, [darkMode]);

  // Prevent flashing login page
  if (checkingAuth) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900">
        <div className="animate-spin h-12 w-12 border-4 border-blue-500 border-t-transparent rounded-full"></div>
      </div>
    );
  }

  return (
    <div className="min-h-screen text-gray-800 dark:text-gray-100 bg-gray-100 dark:bg-gray-900">
    <Head title={`Oresamsub | ${title}`} />
      <div className="max-w-full mx-auto border border-gray-300 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden bg-white dark:bg-gray-900">
        {/* Header */}
        <div className="p-4 flex justify-between items-center border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
          <button onClick={() => setDarkMode(!darkMode)} className="text-xl">
            {darkMode ? "☀️" : "🌙"}
          </button>
        </div>

        {/* Main Content */}
        <main className="px-4 min-h-[calc(100vh-96px)]">{children}</main>
      </div>

      {/* Global Loader */}
      {showLoader && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-white dark:bg-gray-900 bg-opacity-80">
          <div className="animate-spin h-12 w-12 border-4 border-blue-500 border-t-transparent rounded-full"></div>
        </div>
      )}
    </div>
  );
}
