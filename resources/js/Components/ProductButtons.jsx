import { router } from "@inertiajs/react";
import { Link } from "@inertiajs/react";

export default function ProductButtons({ loggingOut, setLoggingOut }) {
  
  return (
    <div className="grid grid-cols-4 gap-2 text-center mt-4">

      {/* Reusable Card */}
      {[
   

       {
          label: "Data",
          icon: "📶",
          href: route("inertia.data.index"),
        },
        {
          label: "Airtime",
          icon: "📞",
          href: route("inertia.airtime.index"),
        },
        {
          label: "Power",
          icon: "⚡",
          href: route("ore.electricity"),
        },
        {
          label: "Cable",
          icon: "📺",
          href: route("ore.cable"),
        },
      ].map((item, i) => (
        <Link
          key={i}
          href={item.href}
          className="group p-2 rounded-lg bg-white dark:bg-gray-800 border hover:shadow-sm transition flex flex-col items-center"
        >
          <div className="w-8 h-8 rounded-full bg-emerald-500 flex items-center justify-center text-white text-sm">
            {item.icon}
          </div>
          <div className="mt-1 text-[11px] font-medium text-gray-700 dark:text-gray-200">
            {item.label}
          </div>
        </Link>
      ))}

      {/* Transactions */}
      <button
        onClick={() => router.get(route("inertia.transactions.index"))}
        className="group p-2 rounded-lg bg-white dark:bg-gray-800 border hover:shadow-sm transition flex flex-col items-center"
      >
        <div className="w-8 h-8 rounded-full bg-emerald-500 flex items-center justify-center text-white text-sm">
          🧾
        </div>
        <div className="mt-1 text-[11px] font-medium text-gray-700 dark:text-gray-200">Transactions</div>
      </button>

      {/* API ACCESS */}
      <Link
        href={route("user.api_access.index")}
        className="group p-2 rounded-lg bg-white dark:bg-gray-800 border hover:shadow-sm transition flex flex-col items-center"
      >
        <div className="w-8 h-8 rounded-full bg-purple-500 flex items-center justify-center text-white text-sm">
          🔑
        </div>
        <div className="mt-1 text-[11px] font-medium text-gray-700 dark:text-gray-200">API</div>
      </Link>

      {/* Logout */}
      <button
        onClick={() => {
          if (!loggingOut) {
            setLoggingOut(true);
            router.post("/logout2", {}, { replace: true, preserveState: false });
          }
        }}
        className="group p-2 rounded-lg bg-red-500 text-white flex flex-col items-center"
      >
        <div className="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm">
          🚪
        </div>
        <div className="mt-1 text-[11px] font-medium">
          {loggingOut ? "..." : "Logout"}
        </div>
      </button>

    </div>
  );
}