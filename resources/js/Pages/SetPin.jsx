import { useMemo, useState } from "react";
import { Head, Link, useForm, usePage } from "@inertiajs/react";
import { Eye, EyeOff, Headphones, LockKeyhole, ShieldCheck } from "lucide-react";

const weakPins = new Set([
  "0000",
  "1111",
  "1234",
  "2222",
  "3333",
  "4444",
  "5555",
  "6666",
  "7777",
  "8888",
  "9999",
]);

function onlyDigits(value) {
  return String(value || "").replace(/\D/g, "").slice(0, 4);
}

export default function SetPin() {
  const { props } = usePage();
  const flash = props.flash || {};
  const [showPin, setShowPin] = useState(false);
  const [showConfirmPin, setShowConfirmPin] = useState(false);

  const { data, setData, post, processing, errors } = useForm({
    pin: "",
    confirm_pin: "",
  });

  const pinReady = data.pin.length === 4;
  const confirmReady = data.confirm_pin.length === 4;
  const weakPin = weakPins.has(data.pin);
  const pinsMatch = confirmReady && data.pin === data.confirm_pin;
  const canSubmit = pinReady && pinsMatch && !weakPin && !processing;

  const status = useMemo(() => {
    if (!data.pin && !data.confirm_pin) {
      return {
        text: "Create a 4-digit PIN to protect your purchases.",
        className: "bg-gray-50 text-gray-600 border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700",
      };
    }

    if (weakPin) {
      return {
        text: "This PIN is too easy to guess. Please choose another one.",
        className: "bg-red-50 text-red-700 border-red-200 dark:bg-red-950 dark:text-red-200 dark:border-red-900",
      };
    }

    if (data.confirm_pin && !pinsMatch) {
      return {
        text: "The two PIN entries do not match yet.",
        className: "bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950 dark:text-amber-100 dark:border-amber-900",
      };
    }

    if (pinsMatch) {
      return {
        text: "PIN looks good. You can continue.",
        className: "bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950 dark:text-emerald-100 dark:border-emerald-900",
      };
    }

    return {
      text: "Enter the same 4 digits twice.",
      className: "bg-gray-50 text-gray-600 border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700",
    };
  }, [data.pin, data.confirm_pin, pinsMatch, weakPin]);

  const submit = (event) => {
    event.preventDefault();
    if (!canSubmit) return;

    post(route("user.settings.store_set_pin"), {
      preserveScroll: true,
    });
  };

  return (
    <div className="min-h-screen bg-gray-50 px-4 py-5 text-gray-900 dark:bg-gray-950 dark:text-white">
      <Head title="Set Transaction PIN" />

      <div className="mx-auto flex min-h-[calc(100vh-40px)] w-full max-w-md flex-col">
        <div className="mb-4 flex items-center justify-between gap-3">
          <Link href={route("dashboard")} className="text-lg font-black tracking-tight text-emerald-700 dark:text-emerald-300">
            OresamSub
          </Link>

          <a
            href="https://wa.me/2349163128718?text=Hello%20OresamSub%20Support%2C%20I%20need%20help%20setting%20my%20transaction%20PIN."
            target="_blank"
            rel="noreferrer"
            className="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-bold text-emerald-800 shadow-sm dark:bg-emerald-900 dark:text-emerald-100"
          >
            <Headphones className="h-3.5 w-3.5" />
            Support
          </a>
        </div>

        <div className="flex flex-1 items-center">
          <div className="w-full rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div className="text-center">
              <div className="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-200">
                <LockKeyhole className="h-7 w-7" />
              </div>

              <h1 className="text-xl font-black">Set Your Transaction PIN</h1>
              <p className="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">
                This PIN confirms purchases, wallet actions, and sensitive account changes.
              </p>
            </div>

            {flash.success && (
              <div className="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                {flash.success}
              </div>
            )}

            {(flash.failure || flash.error) && (
              <div className="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                {flash.failure || flash.error}
              </div>
            )}

            <div className={`mt-4 rounded-xl border px-4 py-3 text-sm ${status.className}`}>
              {status.text}
            </div>

            <form onSubmit={submit} className="mt-5 space-y-4">
              <div>
                <div className="mb-1 flex items-center justify-between gap-3">
                  <label htmlFor="pin" className="text-sm font-bold">
                    New PIN
                  </label>
                  <span className="text-xs font-semibold text-gray-400">{data.pin.length}/4</span>
                </div>

                <div className="relative">
                  <input
                    id="pin"
                    name="pin"
                    type={showPin ? "text" : "password"}
                    inputMode="numeric"
                    autoComplete="new-password"
                    value={data.pin}
                    onChange={(event) => setData("pin", onlyDigits(event.target.value))}
                    placeholder="Enter 4 numbers"
                    className={`w-full rounded-xl border bg-white px-4 py-3 pr-14 text-lg tracking-[0.35em] outline-none transition focus:ring-2 dark:bg-gray-950 ${
                      errors.pin || (data.pin && (!pinReady || weakPin))
                        ? "border-red-300 focus:border-red-500 focus:ring-red-100"
                        : "border-gray-300 focus:border-emerald-500 focus:ring-emerald-100 dark:border-gray-700"
                    }`}
                  />

                  <button
                    type="button"
                    onClick={() => setShowPin((value) => !value)}
                    className="absolute inset-y-0 right-3 inline-flex items-center text-gray-500"
                    aria-label={showPin ? "Hide PIN" : "Show PIN"}
                  >
                    {showPin ? <EyeOff className="h-5 w-5" /> : <Eye className="h-5 w-5" />}
                  </button>
                </div>

                {errors.pin && <p className="mt-1 text-xs font-semibold text-red-600">{errors.pin}</p>}
              </div>

              <div>
                <div className="mb-1 flex items-center justify-between gap-3">
                  <label htmlFor="confirm_pin" className="text-sm font-bold">
                    Confirm PIN
                  </label>
                  <span className="text-xs font-semibold text-gray-400">{data.confirm_pin.length}/4</span>
                </div>

                <div className="relative">
                  <input
                    id="confirm_pin"
                    name="confirm_pin"
                    type={showConfirmPin ? "text" : "password"}
                    inputMode="numeric"
                    autoComplete="new-password"
                    value={data.confirm_pin}
                    onChange={(event) => setData("confirm_pin", onlyDigits(event.target.value))}
                    placeholder="Re-enter the same PIN"
                    className={`w-full rounded-xl border bg-white px-4 py-3 pr-14 text-lg tracking-[0.35em] outline-none transition focus:ring-2 dark:bg-gray-950 ${
                      errors.confirm_pin || (data.confirm_pin && !pinsMatch)
                        ? "border-red-300 focus:border-red-500 focus:ring-red-100"
                        : "border-gray-300 focus:border-emerald-500 focus:ring-emerald-100 dark:border-gray-700"
                    }`}
                  />

                  <button
                    type="button"
                    onClick={() => setShowConfirmPin((value) => !value)}
                    className="absolute inset-y-0 right-3 inline-flex items-center text-gray-500"
                    aria-label={showConfirmPin ? "Hide confirmation PIN" : "Show confirmation PIN"}
                  >
                    {showConfirmPin ? <EyeOff className="h-5 w-5" /> : <Eye className="h-5 w-5" />}
                  </button>
                </div>

                {errors.confirm_pin && <p className="mt-1 text-xs font-semibold text-red-600">{errors.confirm_pin}</p>}
              </div>

              <div className="rounded-xl bg-amber-50 px-4 py-3 text-xs leading-5 text-amber-800 dark:bg-amber-950 dark:text-amber-100">
                Do not use your ATM PIN, date of birth, or repeated numbers. OresamSub support will never ask for this PIN.
              </div>

              <button
                type="submit"
                disabled={!canSubmit}
                className="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 text-sm font-black text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50"
              >
                <ShieldCheck className="h-5 w-5" />
                {processing ? "Setting PIN..." : "Set Transaction PIN"}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  );
}
