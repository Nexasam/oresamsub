import { useEffect, useState } from "react";
import { Download, ShieldCheck, Smartphone, X } from "lucide-react";

const dismissedKey = (version) => `oresamsub-mobile-preview-dismissed:${version}`;

export default function MobileAppPreviewPopup({ preview }) {
  const [open, setOpen] = useState(false);
  const [ready, setReady] = useState(false);

  useEffect(() => {
    if (!preview?.available || !preview?.download_url) {
      setOpen(false);
      setReady(true);
      return;
    }

    try {
      setOpen(localStorage.getItem(dismissedKey(preview.version)) !== "1");
    } catch {
      setOpen(true);
    }
    setReady(true);
  }, [preview?.available, preview?.download_url, preview?.version]);

  if (!ready || !preview?.available || !preview?.download_url) return null;

  const close = () => {
    try {
      localStorage.setItem(dismissedKey(preview.version), "1");
    } catch {
      // The launcher button still allows reopening when storage is unavailable.
    }
    setOpen(false);
  };

  if (!open) {
    return (
      <button
        type="button"
        onClick={() => setOpen(true)}
        className="fixed bottom-20 right-4 z-[100] flex items-center gap-2 rounded-full bg-emerald-700 px-4 py-3 text-xs font-extrabold text-white shadow-xl transition hover:bg-emerald-800 focus:outline-none focus:ring-4 focus:ring-emerald-300 dark:focus:ring-emerald-900"
        aria-label="Open mobile app preview"
      >
        <Smartphone className="h-4 w-4" /> Mobile App Preview
      </button>
    );
  }

  return (
    <div className="fixed inset-0 z-[110] flex items-center justify-center bg-slate-950/70 px-4 py-8 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="mobile-preview-title">
      <div className="relative w-full max-w-md overflow-hidden rounded-3xl bg-white shadow-2xl dark:bg-gray-900">
        <div className="bg-gradient-to-br from-emerald-800 via-emerald-700 to-green-500 px-6 pb-8 pt-6 text-white">
          <div className="flex items-start justify-between">
            <div className="grid h-14 w-14 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/25">
              <Smartphone className="h-7 w-7" />
            </div>
            <button type="button" onClick={close} className="rounded-full bg-white/10 p-2 transition hover:bg-white/20" aria-label="Close mobile app invitation">
              <X className="h-5 w-5" />
            </button>
          </div>
          <p className="mt-6 text-[10px] font-bold uppercase tracking-[0.2em] text-emerald-100">Private tester access</p>
          <h2 id="mobile-preview-title" className="mt-2 text-2xl font-extrabold leading-tight">Test our new mobile app</h2>
          <p className="mt-2 text-sm leading-6 text-emerald-50">You have early access to the OresamSub Android preview.</p>
        </div>

        <div className="px-6 py-6">
          <div className="flex gap-3 rounded-2xl bg-emerald-50 p-4 text-emerald-950 dark:bg-emerald-950 dark:text-emerald-100">
            <ShieldCheck className="mt-0.5 h-5 w-5 shrink-0" />
            <p className="text-xs leading-5">Install it on an Android phone, sign in with your tester account, and test registration, OTP, purchases, wallet and transaction history.</p>
          </div>
          <p className="mt-4 text-xs leading-5 text-gray-500 dark:text-gray-400">Preview version {preview.version}. Android may ask you to allow installation from your browser.</p>
          <a href={preview.download_url} className="mt-5 flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3.5 text-sm font-extrabold text-white transition hover:bg-emerald-700">
            <Download className="h-4 w-4" /> Download Android preview
          </a>
          <button type="button" onClick={close} className="mt-3 w-full rounded-xl px-4 py-3 text-xs font-bold text-gray-500 transition hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800">Maybe later</button>
        </div>
      </div>
    </div>
  );
}
