import { Link, usePage } from "@inertiajs/react";
import { useMemo, useState } from "react";
import DashboardLayout from "@/Layouts/DashboardLayout";

const formatMoney = (value) =>
  new Intl.NumberFormat("en-NG", {
    style: "currency",
    currency: "NGN",
    minimumFractionDigits: 0,
    maximumFractionDigits: 2,
  }).format(Number(value || 0));

const formatSize = (sizeMb) => {
  const value = Number(sizeMb || 0);
  if (value >= 1000 && value % 1000 === 0) return `${value / 1000}GB`;
  return `${value.toLocaleString("en-NG")}MB`;
};

const sourceLabel = {
  personal_override: "Your special price",
  salesperson: "Salesperson price",
  customer_level: "Plan-level price",
};

export default function Pricing() {
  const { plans = [], pricingProfile = {} } = usePage().props;
  const [search, setSearch] = useState("");
  const [network, setNetwork] = useState("all");

  const networks = useMemo(
    () => [...new Set(plans.map((plan) => plan.network).filter(Boolean))].sort(),
    [plans]
  );

  const filteredPlans = useMemo(() => {
    const term = search.trim().toLowerCase();
    return plans.filter((plan) => {
      const matchesNetwork = network === "all" || plan.network === network;
      const matchesSearch = !term || `${plan.name} ${plan.network} ${formatSize(plan.size_mb)}`.toLowerCase().includes(term);
      return matchesNetwork && matchesSearch;
    });
  }, [plans, search, network]);

  return (
    <DashboardLayout title="My Prices">
      <div className="mx-auto max-w-5xl space-y-4 pb-20">
        <section className="overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-700 to-green-500 p-5 text-white shadow-lg">
          <div className="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
              <Link href={route("dashboard")} className="text-xs font-medium text-emerald-50 hover:underline">
                ← Back to dashboard
              </Link>
              <h1 className="mt-3 text-2xl font-bold">My Data Prices</h1>
              <p className="mt-1 max-w-xl text-sm text-emerald-50">
                These are your current payable prices. The same prices are checked again when you complete a purchase.
              </p>
            </div>
            <div className="rounded-xl bg-white/15 px-4 py-3 backdrop-blur-sm">
              <p className="text-xs uppercase tracking-wide text-emerald-50">Your pricing profile</p>
              <p className="mt-1 font-semibold">{pricingProfile.plan_name}</p>
              <p className="text-xs text-emerald-50">Level {pricingProfile.plan_level}</p>
              {pricingProfile.is_salesperson && (
                <span className="mt-2 inline-flex rounded-full bg-yellow-300 px-2 py-1 text-[11px] font-bold text-emerald-950">
                  Salesperson pricing active
                </span>
              )}
            </div>
          </div>
        </section>

        <section className="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
          <div className="grid gap-3 sm:grid-cols-[1fr_200px]">
            <input
              type="search"
              value={search}
              onChange={(event) => setSearch(event.target.value)}
              placeholder="Search by plan, network or size..."
              className="rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
            />
            <select
              value={network}
              onChange={(event) => setNetwork(event.target.value)}
              className="rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none focus:border-emerald-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
            >
              <option value="all">All networks</option>
              {networks.map((name) => <option key={name} value={name}>{name}</option>)}
            </select>
          </div>
          <p className="mt-3 text-xs text-gray-500 dark:text-gray-400">
            Showing {filteredPlans.length} of {plans.length} available plans
          </p>
        </section>

        <section className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
          {filteredPlans.map((plan) => (
            <article key={plan.id} className="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-700 dark:bg-gray-800">
              <div className="flex items-start justify-between gap-3">
                <div className="min-w-0">
                  <span className="inline-flex rounded-full bg-emerald-50 px-2 py-1 text-[11px] font-bold text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">
                    {plan.network || "Data"}
                  </span>
                  <h2 className="mt-2 font-semibold leading-snug text-gray-900 dark:text-white">{plan.name}</h2>
                </div>
                <p className="whitespace-nowrap text-xl font-bold text-emerald-600 dark:text-emerald-400">{formatMoney(plan.price)}</p>
              </div>
              <div className="mt-4 flex items-center justify-between border-t border-gray-100 pt-3 text-xs text-gray-500 dark:border-gray-700 dark:text-gray-400">
                <span>{formatSize(plan.size_mb)} · {plan.validity_days} day{plan.validity_days === 1 ? "" : "s"}</span>
                <span className={plan.price_source === "customer_level" ? "" : "font-semibold text-amber-600 dark:text-amber-400"}>
                  {sourceLabel[plan.price_source] || "Your price"}
                </span>
              </div>
            </article>
          ))}
        </section>

        {filteredPlans.length === 0 && (
          <div className="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-12 text-center text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
            No plans match your search.
          </div>
        )}
      </div>
    </DashboardLayout>
  );
}
