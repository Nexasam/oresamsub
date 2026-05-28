import { useState, useEffect } from "react";
import { usePage, router } from "@inertiajs/react";
import axios from "axios";
import Swal from "sweetalert2";

export default function Edit() {
  const { automation } = usePage().props;

  const [apiKey, setApiKey] = useState("");
  const [apiSecret, setApiSecret] = useState("");
  const [loading, setLoading] = useState(false);

  // 🌙 theme state
  const [darkMode, setDarkMode] = useState(
    () => localStorage.getItem("theme") === "dark"
  );

  useEffect(() => {
    setApiKey(automation.api_key || "");
    setApiSecret(automation.api_secret || "");
  }, [automation]);

  // apply theme
  useEffect(() => {
    if (darkMode) {
      document.documentElement.classList.add("dark");
      localStorage.setItem("theme", "dark");
    } else {
      document.documentElement.classList.remove("dark");
      localStorage.setItem("theme", "light");
    }
  }, [darkMode]);

  const toggleTheme = () => setDarkMode((prev) => !prev);

  const save = async () => {
    setLoading(true);

    try {
      await axios.post(route("automation.key.update"), {
        user_automation_id: automation.user_automation_id,
        api_key: apiKey,
        api_secret: apiSecret,
      });

      Swal.fire("Saved", "Integration keys updated successfully", "success");
      router.visit(route("dashboard"));
    } catch (e) {
      Swal.fire("Error", "Update failed", "error");
    }

    setLoading(false);
  };

  return (
    <div className="min-h-screen bg-gray-50 dark:bg-gray-950 flex items-center justify-center p-4">

      <div className="w-full max-w-xl bg-white dark:bg-gray-900 shadow rounded-xl p-6 space-y-5">

        {/* HEADER + THEME TOGGLE */}
        <div className="flex justify-between items-start">
          <div>
            <h1 className="text-xl font-bold text-gray-900 dark:text-white">
              Editing: {automation.name}
            </h1>

            <p className="text-sm text-gray-500 dark:text-gray-400">
              Securely update your API configuration
            </p>

            {automation.domain_url && (
              <a
                href={automation.domain_url}
                target="_blank"
                rel="noreferrer"
                className="text-sm text-emerald-600 underline inline-block mt-2"
              >
                Open Platform →
              </a>
            )}
          </div>

          {/* 🌙 TOGGLE */}
          <button
            onClick={toggleTheme}
            className="text-xs px-3 py-1 rounded bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-200"
          >
            {darkMode ? "Light" : "Dark"}
          </button>
        </div>

        {/* API KEY */}
        <div className="space-y-1">
          <label className="text-sm font-medium text-gray-700 dark:text-gray-300">
            API Key
          </label>
          <input
            className="w-full p-3 border rounded dark:bg-gray-800"
            value={apiKey}
            onChange={(e) => setApiKey(e.target.value)}
          />
        </div>

        {/* API SECRET */}
        <div className="space-y-1">
          <label className="text-sm font-medium text-gray-700 dark:text-gray-300">
            API Secret
          </label>
          <input
            className="w-full p-3 border rounded dark:bg-gray-800"
            value={apiSecret}
            onChange={(e) => setApiSecret(e.target.value)}
          />
        </div>

        {/* SAVE */}
        <button
          onClick={save}
          disabled={loading}
          className="w-full bg-emerald-600 hover:bg-emerald-700 text-white p-3 rounded"
        >
          {loading ? "Saving..." : "Save Changes"}
        </button>

      </div>
    </div>
  );
}