import DashboardLayout from "@/Layouts/DashboardLayout";
import { useState } from "react";
import { usePage, Link } from "@inertiajs/react";
import axios from "axios";
import Swal from "sweetalert2";

export default function Index() {
  const { user, allAutomations } = usePage().props;

  const [openId, setOpenId] = useState(null);
  const [search, setSearch] = useState("");
  const [loading, setLoading] = useState(false);

  // check subscription (NEW STRUCTURE)
  const isSubscribed = (automationId) => {
    return user.automations?.some(
      (ua) => ua.automation_id === automationId
    );
  };

  // get user automation record
  const getUserAutomation = (automationId) => {
    return user.automations?.find(
      (ua) => ua.automation_id === automationId
    );
  };

  const filteredAutomations = allAutomations.filter((auto) =>
    auto.automation_name.toLowerCase().includes(search.toLowerCase())
  );

  return (
    <DashboardLayout title="My API Access">

      <div className="max-w-3xl mx-auto space-y-4 text-gray-900 dark:text-gray-100 pb-32">

        {/* BACK */}
        <div className="flex justify-start">
          <Link
            href={route("dashboard")}
            className="inline-flex items-center gap-2 text-sm px-3 py-2 rounded-lg 
            bg-gray-100 dark:bg-gray-800 
            hover:bg-gray-200 dark:hover:bg-gray-700 transition"
          >
            ← Return to Dashboard
          </Link>
        </div>

        {/* TOKEN */}
        <div className="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 p-4 rounded-xl shadow">
          <p className="text-xs text-gray-500 mb-1">Your API Token</p>

          <code className="block bg-gray-100 dark:bg-gray-800 p-3 rounded text-xs break-all">
            {user.api_token}
          </code>
        </div>

        {/* SALES */}
        <div className="bg-gradient-to-r from-emerald-600 to-emerald-500 text-white p-4 rounded-xl">
          🚀 Automate your business with our API across Nigeria.
        </div>

        {/* SUBSCRIPTIONS */}
        <div className="bg-white dark:bg-gray-900 p-4 rounded-xl shadow">
          <h2 className="font-semibold mb-2">Your Providers</h2>

          <div className="flex flex-wrap gap-2">
            {user.automations?.map((ua) => (
              <span
                key={ua.id}
                className="px-2 py-1 text-xs bg-emerald-100 dark:bg-emerald-900 rounded"
              >
                {ua.automation?.automation_name}
              </span>
            ))}
          </div>


          <Link
            href={route("product_plans.index")}
            className="text-xs px-3 py-1 rounded-full
            bg-emerald-100 text-emerald-700
            dark:bg-emerald-900 dark:text-emerald-300
            hover:opacity-90 transition whitespace-nowrap mt-4 inline-block"
          >
            ⚙️ Manage Plans
          </Link>

        </div>

        {/* ALL AUTOMATIONS */}
        <div className="bg-white dark:bg-gray-900 p-4 rounded-xl shadow">

        <div className="flex items-start justify-between mb-3 gap-3">

            <div>
                <h2 className="font-semibold">
                Available Providers
                </h2>

                <p className="text-xs text-gray-500 dark:text-gray-400 mt-1">
                We’re on a mission to integrate at least <span className="text-emerald-500 font-medium">100 providers</span> before the end of 2026.
                </p>
            </div>

            <a
                href="https://wa.me/2348168509044?text=Hi%20team%2C%20I%20want%20you%20to%20add%20a%20new%20automation%20provider"
                target="_blank"
                rel="noreferrer"
                className="text-xs px-3 py-1 rounded-full
                bg-green-100 text-green-700
                dark:bg-green-900 dark:text-green-300
                hover:opacity-90 transition whitespace-nowrap"
            >
                💬 Suggest Provider
            </a>

            </div>

          {/* SEARCH */}
          <input
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            placeholder="Search automations..."
            className="w-full mb-3 px-3 py-2 rounded border dark:bg-gray-800"
          />

          {/* LIST */}
          <div className="max-h-96 overflow-y-auto space-y-2">

            {filteredAutomations.map((auto) => {

              const open = openId === auto.id;
              const subscribed = isSubscribed(auto.id);
              const userAutomation = getUserAutomation(auto.id);

              const baseUrl = (auto.domain_url || "").replace(/\/$/, "");

              return (
                <div
                  key={auto.id}
                  className="border rounded-lg p-3 dark:border-gray-700"
                >

                  {/* HEADER */}
                  <div
                    className="flex justify-between cursor-pointer"
                    onClick={() => setOpenId(open ? null : auto.id)}
                  >
                    <div>
                      <p className="font-medium">{auto.automation_name}</p>

                      {subscribed && (
                        <span className="text-xs text-green-500">
                          Active
                        </span>
                      )}
                    </div>

                    <span>{open ? "▲" : "▼"}</span>
                  </div>

                  {/* DETAILS */}
                  {open && (
                    <div className="mt-3 space-y-2 text-sm">

                      {/* DOMAIN */}
                      <div className="text-xs bg-gray-50 dark:bg-gray-800 p-2 rounded space-y-1">

                        <p>
                          <b>Domain:</b>{" "}
                          <a
                            href={baseUrl}
                            target="_blank"
                            rel="noreferrer"
                            className="text-emerald-500 underline"
                          >
                            Open Platform →
                          </a>
                        </p>

                      

                      </div>

                      <p className="text-gray-600 dark:text-gray-300">
                        {auto.description || "No description yet"}
                      </p>

                      {/* API KEY SECTION */}
                      {subscribed && userAutomation && (
                        <div className="space-y-2">

                          {userAutomation.api_key ? (
                            <p className="text-xs text-gray-500">
                              API Key is already configured
                            </p>
                          ) : (
                            <p className="text-xs text-amber-500">
                              No API key set yet
                            </p>
                          )}

                    <button
                    disabled={loading}
                    onClick={() => {
                        setLoading(true);

                        axios.post(route("automation.request_key_edit"), {
                        user_automation_id: userAutomation.id,
                        })
                        .then(() => {
                        Swal.fire(
                            "Check your email",
                            "We sent a secure link valid for 10 minutes to manage your API key",
                            "success"
                        );
                        })
                        .catch(() => {
                        Swal.fire("Error", "Something went wrong", "error");
                        })
                        .finally(() => {
                        setLoading(false);
                        });
                    }}
                    className="text-sm text-emerald-600 dark:text-emerald-400 disabled:opacity-50"
                    >
                    {loading ? "Sending..." : "Update API Key (Secure)"}
                    </button>

                        </div>
                      )}

                      {!subscribed && (
                        <p className="text-xs text-amber-500">
                          Not subscribed yet
                        </p>
                      )}

                    </div>
                  )}

                </div>
              );
            })}

          </div>
        </div>

        <div className="h-12"></div>
      </div>

    </DashboardLayout>
  );
}