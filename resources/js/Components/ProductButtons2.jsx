import { router } from "@inertiajs/react";
import { Link } from "@inertiajs/react";


export default function ProductButtons2({ loggingOut, setLoggingOut }) {
  return (
    <div className="grid grid-cols-3 sm:grid-cols-4 gap-3 text-center text-sm md:text-base mt-4">

      {/* Airtime */}
      <button
        onClick={() => router.get(route("ore.airtime"))}
        className="group p-3 rounded-xl shadow hover:shadow-md transition transform hover:scale-[1.05] bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200"
      >
        <div className="w-10 h-10 mx-auto rounded-full bg-gradient-to-r from-emerald-500 to-green-500 flex items-center justify-center text-white text-xl shadow-sm">
          📞
        </div>
        <div className="mt-2 font-medium text-[13px] md:text-sm">Airtime</div>
      </button>

      {/* Data */}
      {/* <button
        onClick={() => router.get(route("inertia.data.index"))}
        className="group p-3 rounded-xl shadow hover:shadow-md transition transform hover:scale-[1.05] bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200"
      >
        <div className="w-10 h-10 mx-auto rounded-full bg-gradient-to-r from-emerald-500 to-green-500 flex items-center justify-center text-white text-xl shadow-sm">
          📶
        </div>
        <div className="mt-2 font-medium text-[13px] md:text-sm">Data</div>
      </button> */}

        <Link
        href="/data2"
        className="group p-3 rounded-xl shadow hover:shadow-md transition transform hover:scale-[1.05] bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 flex flex-col items-center"
        >
        <div className="w-10 h-10 rounded-full bg-gradient-to-r from-emerald-500 to-green-500 flex items-center justify-center text-white text-xl shadow-sm">
            📶
        </div>
        <div className="mt-2 font-medium text-[13px] md:text-sm">Data</div>
        </Link>


      {/* Power */}
      <button
        onClick={() => router.get(route("ore.electricity"))}
        className="group p-3 rounded-xl shadow hover:shadow-md transition transform hover:scale-[1.05] bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200"
      >
        <div className="w-10 h-10 mx-auto rounded-full bg-gradient-to-r from-emerald-500 to-green-500 flex items-center justify-center text-white text-xl shadow-sm">
          ⚡
        </div>
        <div className="mt-2 font-medium text-[13px] md:text-sm">Power</div>
      </button>

      {/* Cable */}
      <button
        onClick={() => router.get(route("ore.cable"))}
        className="group p-3 rounded-xl shadow hover:shadow-md transition transform hover:scale-[1.05] bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200"
      >
        <div className="w-10 h-10 mx-auto rounded-full bg-gradient-to-r from-emerald-500 to-green-500 flex items-center justify-center text-white text-xl shadow-sm">
          📺
        </div>
        <div className="mt-2 font-medium text-[13px] md:text-sm">Cable</div>
      </button>

      {/* Transactions */}
      <button
        onClick={() => router.get(route("ore.transactions"))}
        className="group p-3 rounded-xl shadow hover:shadow-md transition transform hover:scale-[1.05] bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200"
      >
        <div className="w-10 h-10 mx-auto rounded-full bg-gradient-to-r from-emerald-500 to-green-500 flex items-center justify-center text-white text-xl shadow-sm">
          🧾
        </div>
        <div className="mt-2 font-medium text-[13px] md:text-sm">Transactions</div>
      </button>

      {/* Logout */}
      <button
        onClick={() => {
          if (!loggingOut) {
            setLoggingOut(true);
            router.post("/logout2", {}, { replace: true, preserveState: false });
          }
        }}
        className="group p-3 rounded-xl shadow hover:shadow-md transition transform hover:scale-[1.05] bg-red-500 text-white"
      >
        <div className="w-10 h-10 mx-auto rounded-full bg-gradient-to-r from-emerald-500 to-green-500 flex items-center justify-center text-white text-xl shadow-sm">
          🚪
        </div>
        <div className="mt-2 font-medium text-[13px] md:text-sm">
          {loggingOut ? "Logging out…" : "Logout"}
        </div>
      </button>

    </div>
  );
}
