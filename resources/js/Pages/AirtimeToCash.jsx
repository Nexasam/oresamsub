import { useMemo, useState } from "react";
import { Link, router, useForm, usePage } from "@inertiajs/react";
import DashboardLayout from "@/Layouts/DashboardLayout";
import WalletBalance from "@/Components/WalletBalance";
import Swal from "sweetalert2";

export default function AirtimeToCash() {
  const { props } = usePage();
  const { auth, networks = [], requests = [], settings = {}, flash = {} } = props;
  const user = auth.user;
  const [selectedRequest, setSelectedRequest] = useState(null);

  const { data, setData, post, processing, errors, reset } = useForm({
    network_id: "",
    airtime_amount: "",
    sender_phone: "",
    payout_bank_name: "",
    payout_account_name: "",
    payout_account_number: "",
    customer_transfer_reference: "",
    customer_note: "",
    fraud_disclaimer_accepted: false,
  });

  const enabled = settings.enabled !== false;
  const rate = Number(settings.rate_per_100 ?? 90);
  const fraudDisclaimerText =
    settings.fraud_disclaimer_text ||
    "I confirm that the airtime I am selling is legitimately owned by me or I am authorized to sell it. I understand that fraudulent, stolen, borrowed, reversed, disputed, or unauthorized airtime may cause this request to be rejected, payout to be delayed or reversed, my account to be restricted, and the matter to be reported where necessary. I agree that OresamSub will manually verify the airtime before payment.";
  const estimatedCash = useMemo(() => {
    const amount = Number(data.airtime_amount || 0);
    return amount > 0 ? (amount * rate) / 100 : 0;
  }, [data.airtime_amount, rate]);

  const statusStyles = {
    pending: "bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-200",
    processing: "bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200",
    paid: "bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-200",
    rejected: "bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200",
  };

  const supportLink = (request = null) => {
    const phone = String(settings.support_whatsapp || "").replace(/\D/g, "");
    const message = request
      ? `Hello OresamSub Support, I need help with my airtime-to-cash request ${request.reference}.`
      : "Hello OresamSub Support, I need help with airtime-to-cash.";

    return `https://wa.me/${phone || "234xxxxxxxxxx"}?text=${encodeURIComponent(message)}`;
  };

  const handleSubmit = async (event) => {
    event.preventDefault();

    const result = await Swal.fire({
      title: "Submit Airtime-to-Cash Request?",
      html: `
        You are selling airtime worth <b>₦${Number(data.airtime_amount || 0).toLocaleString("en-NG")}</b>.<br/>
        Estimated cash payout: <b>₦${Number(estimatedCash).toLocaleString("en-NG")}</b><br/>
        Rate: <b>₦${rate.toLocaleString("en-NG")} per ₦100 airtime</b><br/><br/>
        Admin will verify manually before payment.
      `,
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Submit Request",
      cancelButtonText: "Cancel",
      confirmButtonColor: "#059669",
    });

    if (!result.isConfirmed) return;

    post(route("airtime-to-cash.store"), {
      preserveScroll: true,
      onSuccess: () => {
        reset();
        Swal.fire("Request Submitted", "Your airtime-to-cash request is now pending admin review.", "success");
      },
      onError: () => {
        Swal.fire("Check Form", "Please review the highlighted fields and try again.", "error");
      },
    });
  };

  return (
    <DashboardLayout title="Airtime to Cash">
      <WalletBalance user={user} />

      <div className="flex items-center justify-between mt-4">
        <Link
          href={route("dashboard")}
          className="inline-flex items-center px-4 py-2 rounded-lg bg-gradient-to-r from-emerald-600 to-emerald-500 text-white text-sm font-medium shadow"
        >
          ← Back
        </Link>

        <a
          href={supportLink()}
          target="_blank"
          className="px-4 py-2 rounded-lg bg-green-100 text-green-800 text-sm font-semibold dark:bg-green-900 dark:text-green-100"
        >
          💬 Support
        </a>
      </div>

      {flash?.success && (
        <div className="mt-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 p-3 text-sm">
          {flash.success}
        </div>
      )}

      {flash?.failure && (
        <div className="mt-4 rounded-xl bg-red-50 border border-red-200 text-red-700 p-3 text-sm">
          {flash.failure}
        </div>
      )}

      <div className="mt-4 rounded-2xl bg-gradient-to-br from-emerald-600 to-green-700 p-4 text-white shadow">
        <p className="text-xs uppercase opacity-80">Current rate</p>
        <h1 className="text-2xl font-bold">₦{rate.toLocaleString("en-NG")} cash for every ₦100 airtime</h1>
        <p className="mt-2 text-sm opacity-90">
          {enabled
            ? "Submit your request here. Our admin verifies the airtime manually and pays into your bank account."
            : "This feature is temporarily disabled. You can still view previous requests or contact support."}
        </p>
      </div>

      <div className="bg-white dark:bg-gray-800 text-gray-700 dark:text-white mt-4 rounded-xl shadow overflow-hidden font-inter">
        <div className="p-4 border-b border-gray-200 dark:border-gray-700 font-semibold">
          Sell Airtime for Cash
        </div>

        {!enabled && (
          <div className="m-4 rounded-xl bg-yellow-50 dark:bg-yellow-950 border border-yellow-200 dark:border-yellow-900 text-yellow-800 dark:text-yellow-100 p-3 text-sm">
            Airtime-to-cash submissions are currently disabled by admin. Please contact support if you need help.
          </div>
        )}

        <form onSubmit={handleSubmit} className="p-4 space-y-4">
          <div>
            <label className="block text-sm mb-1">Network</label>
            <select
              className="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm"
              value={data.network_id}
              onChange={(event) => setData("network_id", event.target.value)}
            >
              <option value="">Select network</option>
              {networks.map((network) => (
                <option key={network.id} value={network.id}>
                  {network.network_name}
                </option>
              ))}
            </select>
            {errors.network_id && <p className="text-xs text-red-500 mt-1">{errors.network_id}</p>}
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label className="block text-sm mb-1">Airtime Amount</label>
              <input
                type="number"
                min="100"
                className="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm"
                placeholder="e.g. 5000"
                value={data.airtime_amount}
                onChange={(event) => setData("airtime_amount", event.target.value)}
              />
              {errors.airtime_amount && <p className="text-xs text-red-500 mt-1">{errors.airtime_amount}</p>}
            </div>

            <div>
              <label className="block text-sm mb-1">Phone Sending Airtime</label>
              <input
                type="tel"
                className="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm"
                placeholder="e.g. 08012345678"
                value={data.sender_phone}
                onChange={(event) => setData("sender_phone", event.target.value)}
              />
              {errors.sender_phone && <p className="text-xs text-red-500 mt-1">{errors.sender_phone}</p>}
            </div>
          </div>

          <div className="rounded-xl bg-emerald-50 dark:bg-emerald-950 p-3 text-sm text-emerald-800 dark:text-emerald-100">
            Estimated payout: <b>₦{Number(estimatedCash).toLocaleString("en-NG")}</b>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label className="block text-sm mb-1">Bank Name</label>
              <input
                className="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm"
                placeholder="e.g. Opay, Kuda, GTBank"
                value={data.payout_bank_name}
                onChange={(event) => setData("payout_bank_name", event.target.value)}
              />
              {errors.payout_bank_name && <p className="text-xs text-red-500 mt-1">{errors.payout_bank_name}</p>}
            </div>

            <div>
              <label className="block text-sm mb-1">Account Number</label>
              <input
                className="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm"
                placeholder="e.g. 1234567890"
                value={data.payout_account_number}
                onChange={(event) => setData("payout_account_number", event.target.value)}
              />
              {errors.payout_account_number && <p className="text-xs text-red-500 mt-1">{errors.payout_account_number}</p>}
            </div>
          </div>

          <div>
            <label className="block text-sm mb-1">Account Name</label>
            <input
              className="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm"
              placeholder="Name on bank account"
              value={data.payout_account_name}
              onChange={(event) => setData("payout_account_name", event.target.value)}
            />
            {errors.payout_account_name && <p className="text-xs text-red-500 mt-1">{errors.payout_account_name}</p>}
          </div>

          <div>
            <label className="block text-sm mb-1">Transfer Reference / Notes to Admin</label>
            <input
              className="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm mb-2"
              placeholder="Optional transfer code/reference"
              value={data.customer_transfer_reference}
              onChange={(event) => setData("customer_transfer_reference", event.target.value)}
            />
            <textarea
              className="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm"
              rows="3"
              placeholder="Any extra information for admin"
              value={data.customer_note}
              onChange={(event) => setData("customer_note", event.target.value)}
            />
          </div>

          <div className="rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-100">
            <p className="font-semibold">Fraud prevention disclaimer</p>
            <p className="mt-1 leading-relaxed">{fraudDisclaimerText}</p>
            <label className="mt-3 flex items-start gap-2 text-sm font-medium">
              <input
                type="checkbox"
                className="mt-1 rounded border-amber-300"
                checked={data.fraud_disclaimer_accepted}
                onChange={(event) => setData("fraud_disclaimer_accepted", event.target.checked)}
              />
              <span>I have read and agree to this disclaimer.</span>
            </label>
            {errors.fraud_disclaimer_accepted && (
              <p className="text-xs text-red-500 mt-1">{errors.fraud_disclaimer_accepted}</p>
            )}
          </div>

          <button
            type="submit"
            disabled={processing || !enabled || !data.fraud_disclaimer_accepted}
            className="w-full py-3 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold disabled:opacity-60"
          >
            {!enabled
              ? "Submissions Disabled"
              : processing
                ? "Submitting..."
                : !data.fraud_disclaimer_accepted
                  ? "Accept Disclaimer to Continue"
                  : "Submit Airtime-to-Cash Request"}
          </button>
        </form>
      </div>

      <div className="bg-white dark:bg-gray-800 text-gray-700 dark:text-white mt-4 mb-20 rounded-xl shadow overflow-hidden font-inter">
        <div className="p-4 border-b border-gray-200 dark:border-gray-700 font-semibold">
          My Airtime-to-Cash Requests
        </div>

        <div className="p-4 space-y-3 max-h-[650px] overflow-y-auto">
          {requests.length > 0 ? requests.map((request) => (
            <div
              key={request.id}
              onClick={() => setSelectedRequest(request)}
              className="p-3 bg-white dark:bg-gray-900 rounded-lg border border-gray-100 dark:border-gray-800 shadow-sm cursor-pointer"
            >
              <div className="flex justify-between gap-3">
                <div>
                  <div className="text-xs font-bold">{request.reference}</div>
                  <div className="text-xs text-gray-500">{request.network_name} · ₦{Number(request.airtime_amount).toLocaleString("en-NG")} airtime</div>
                  <div className="text-xs text-gray-500">{new Date(request.created_at).toLocaleString("en-NG")}</div>
                </div>
                <div className="text-right">
                  <div className="font-bold text-emerald-600">₦{Number(request.cash_amount).toLocaleString("en-NG")}</div>
                  <span className={`inline-block mt-1 px-2 py-1 rounded-full text-[10px] font-bold ${statusStyles[request.status] || "bg-gray-100 text-gray-600"}`}>
                    {request.status.toUpperCase()}
                  </span>
                </div>
              </div>
            </div>
          )) : (
            <p className="text-center text-sm text-gray-500">No airtime-to-cash requests yet.</p>
          )}
        </div>
      </div>

      {selectedRequest && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
          <div className="bg-white dark:bg-gray-800 rounded-xl shadow-lg max-w-sm w-full p-6 font-inter">
            <h2 className="text-lg font-bold mb-4">Request Details</h2>
            <div className="space-y-2 text-sm">
              <div className="flex justify-between"><span>Reference</span><b>{selectedRequest.reference}</b></div>
              <div className="flex justify-between"><span>Status</span><b>{selectedRequest.status.toUpperCase()}</b></div>
              <div className="flex justify-between"><span>Network</span><b>{selectedRequest.network_name}</b></div>
              <div className="flex justify-between"><span>Airtime</span><b>₦{Number(selectedRequest.airtime_amount).toLocaleString("en-NG")}</b></div>
              <div className="flex justify-between"><span>Cash payout</span><b>₦{Number(selectedRequest.cash_amount).toLocaleString("en-NG")}</b></div>
              {selectedRequest.fraud_disclaimer_accepted_at && (
                <div className="flex justify-between gap-3">
                  <span>Disclaimer</span>
                  <b>{new Date(selectedRequest.fraud_disclaimer_accepted_at).toLocaleString("en-NG")}</b>
                </div>
              )}
              <div className="flex justify-between"><span>Bank</span><b>{selectedRequest.payout_bank_name}</b></div>
              <div className="flex justify-between"><span>Account</span><b>{selectedRequest.payout_account_number_masked}</b></div>
              {selectedRequest.payout_reference && <div className="flex justify-between"><span>Payout ref</span><b>{selectedRequest.payout_reference}</b></div>}
              {selectedRequest.admin_note && <div><span>Admin note</span><p className="font-semibold">{selectedRequest.admin_note}</p></div>}
            </div>

            <div className="grid grid-cols-2 gap-2 mt-6">
              <a
                href={supportLink(selectedRequest)}
                target="_blank"
                className="px-4 py-2 bg-green-600 text-white rounded-md text-sm text-center"
              >
                Support
              </a>
              <button
                onClick={() => setSelectedRequest(null)}
                className="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-md text-sm"
              >
                Close
              </button>
            </div>
          </div>
        </div>
      )}
    </DashboardLayout>
  );
}
