import { useEffect, useState } from "react";
import { Link, usePage } from "@inertiajs/react";
import Announcements from "@/Components/Announcements";
import { Head } from "@inertiajs/react";
import PwaInstallPopup from "@/Components/PwaInstallPopup";
import { useRef } from "react";


const getInitialTheme = () => {
  if (typeof window !== "undefined") {
    const stored = localStorage.getItem("theme");
    if (stored) return stored === "dark";
    return window.matchMedia("(prefers-color-scheme: dark)").matches;
  }
  return false; // default light
};

export default function DashboardLayout({ children , title}) {
  const { auth, announcements, impersonator } = usePage().props;
  const user = auth.user;



  const [showBalance, setShowBalance] = useState(true);
  const [darkMode, setDarkMode] = useState(getInitialTheme());
  const [showInstall, setShowInstall] = useState(false);
  const installRef = useRef();

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
      <Head title={`Oresamsub | ${title}`} />
      {/* Announcements */}
      {/* <Announcements announcements={announcements} /> */}

          {/* PWA Install Popup */}
          {/* <PwaInstallPopup visible={showInstall} setVisible={setShowInstall} /> */}
          <PwaInstallPopup ref={installRef} />

    
        {/* Impersonation Banner */}
        {impersonator && (
          <a href={impersonator.exitUrl}>
            <div className="bg-green-800 text-white p-3 mt-2 rounded-xl shadow-md animate-pulse">
              <h1>
                You are now viewing{" "}
                <u>
                  {impersonator.username} {impersonator.fname} ({impersonator.pin})
                </u>{" "}
                as an Administrator.
              </h1>
              <div className="text-sm font-bold">
                👉 Click here to EXIT User Account
              </div>
            </div>
          </a>
        )}

      {/* Greeting + Dark mode toggle */}
      <div className="flex items-center justify-between mt-2">

        

        <div className="flex items-center space-x-4">     
            <h1 className="text-lg font-bold text-gray-800 dark:text-gray-100">
                👋 Hi, {user.username}
            </h1>

            {/* Install App Button */}
            {/* <button
              id="installAppBtn"
              className="px-4 py-2 bg-gradient-to-r from-emerald-400 to-green-500 hover:from-emerald-500 hover:to-green-600 
                        text-white font-bold rounded-xl shadow-lg animate-pulse transition transform hover:scale-105"
              onClick={() => {
                if (window.deferredPrompt) {
                  window.deferredPrompt.prompt();
                  window.deferredPrompt.userChoice.then((choiceResult) => {
                    console.log("User choice:", choiceResult.outcome);
                    window.deferredPrompt = null;
                  });
                } else {
                  alert("App installation prompt not available. Try visiting this site on supported devices.");
                }
              }}
            >
              🚀 Install App
            </button> */}

              {/* Install App Button (Visible Always, triggers popup) */}
              {/* <button
                onClick={() => setShowInstall(true)}
                className="px-4 py-2 bg-gradient-to-r from-emerald-400 to-green-500 
                          hover:from-emerald-500 hover:to-green-600 text-white font-bold 
                          rounded-xl shadow-lg animate-pulse transition transform hover:scale-105"
              >
                🚀 Install App
              </button> */}

              <a
              href="https://wa.me/2349163128718?text=Hello%20OresamSub%20Support%2C%20I%20need%20help%20on%20your%20website"
              target="_blank"
              className="flex items-center px-3 py-1 text-xs font-bold text-emerald-900 dark:text-white bg-gradient-to-r from-emerald-100 to-emerald-200 dark:from-emerald-700 dark:to-emerald-800 hover:brightness-110 dark:hover:brightness-125 rounded-full transition duration-300 ease-in-out shadow-sm"
              >
              <svg className="w-3.5 h-3.5 mr-1 fill-current" viewBox="0 0 24 24">
              <path d="M20.52 3.48A11.86 11.86 0 0012.02 0C5.39 0 0 5.38 0 12a11.89 11.89 0 001.65 6L0 24l6.42-1.68A11.84 11.84 0 0012 24c6.63 0 12-5.38 12-12a11.86 11.86 0 00-3.48-8.52zM12 22.06a10.1 10.1 0 01-5.17-1.42l-.37-.22-3.81 1 1-3.7-.24-.38A10.07 10.07 0 011.94 12c0-5.57 4.5-10.06 10.06-10.06 2.69 0 5.21 1.05 7.11 2.95a10.06 10.06 0 01-7.11 17.17zM17 14.41l-2.17-.62a1.33 1.33 0 00-1.25.34l-.6.61a9.55 9.55 0 01-4.51-4.5l.6-.61a1.33 1.33 0 00.34-1.25l-.62-2.17A1.33 1.33 0 007.12 6H5.65a1.33 1.33 0 00-1.33 1.33A9.7 9.7 0 0015.67 18a1.33 1.33 0 001.33-1.33v-1.47a1.33 1.33 0 00-.95-1.26z"/>
              </svg>
              Support
              </a>
        </div>
        
        <button
          onClick={() => setDarkMode((prev) => !prev)}
          className="flex items-center justify-center w-9 h-9 rounded-xl bg-white dark:bg-gray-800 
                     ring-1 ring-green-200 dark:ring-green-700 shadow-md hover:shadow-xl hover:scale-[1.05] 
                     transition transform text-gray-700 dark:text-gray-200"
        >
          {darkMode ? "🌞" : "🌙"}
        </button>
      </div>

      {/* QUICK AUTOMATIONS BAR */}
{user.automations?.length > 0 && (
  <div className="mt-2 bg-white dark:bg-gray-800 p-3 rounded-xl shadow-sm">

    <div className="flex items-center justify-between mb-2">
      <p className="text-xs font-semibold text-gray-500 uppercase">
        Your Automations
      </p>

      <Link
        href={route("user.api_access.index")}
        className="text-xs text-emerald-600 hover:underline"
      >
        View all →
      </Link>
    </div>

    <div className="flex flex-wrap gap-2">

      {user.automations.slice(0, 6).map((a) => (
        <span
          key={a.id}
          className="px-2 py-1 text-xs rounded-full 
                     bg-emerald-100 text-emerald-700 
                     dark:bg-emerald-900 dark:text-emerald-200"
        >
          {a.automation_name}
        </span>
      ))}

      {user.automations.length > 6 && (
        <span className="text-xs text-gray-400">
          +{user.automations.length - 6} more
        </span>
      )}

    </div>

  </div>
)}

     


      

      {/* Page-specific content */}
      <div>{children}</div>

      {/* Bottom Navigation */}
      <nav className="fixed bottom-0 inset-x-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 shadow-lg z-50">
        <div className="max-w-md mx-auto flex justify-around py-2 text-xs font-medium text-gray-700 dark:text-gray-200">
          {[
            { label: "Dashboard", icon: "🏠", route: "dashboard", inertia: true },
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
