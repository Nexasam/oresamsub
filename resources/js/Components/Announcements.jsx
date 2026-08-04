import { useEffect, useState } from "react";
import { Bell, ChevronLeft, ChevronRight, X } from "lucide-react";

const SNOOZE_KEY = "oresamsub-announcements-snoozed-until";
const SNOOZE_DURATION = 24 * 60 * 60 * 1000;

export default function Announcements({ announcements = [] }) {
  const [index, setIndex] = useState(0);
  const [open, setOpen] = useState(false);
  const [snooze, setSnooze] = useState(false);

  useEffect(() => {
    if (announcements.length === 0) return;

    const snoozedUntil = Number(localStorage.getItem(SNOOZE_KEY) || 0);
    if (snoozedUntil <= Date.now()) {
      localStorage.removeItem(SNOOZE_KEY);
      setOpen(true);
    }
  }, [announcements.length]);

  if (announcements.length === 0) return null;

  const current = announcements[index];
  const move = (direction) => {
    setIndex((previous) =>
      (previous + direction + announcements.length) % announcements.length
    );
  };
  const close = () => {
    if (snooze) {
      localStorage.setItem(SNOOZE_KEY, String(Date.now() + SNOOZE_DURATION));
    }
    setOpen(false);
    setSnooze(false);
  };

  return (
    <>
      <button
        type="button"
        onClick={() => setOpen(true)}
        className="my-2 flex w-full items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-left text-emerald-900 transition hover:border-emerald-300 hover:shadow-sm dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-100"
      >
        <span className="flex items-center gap-3">
          <span className="relative grid h-9 w-9 place-items-center rounded-full bg-emerald-600 text-white">
            <Bell className="h-4 w-4" />
            <span className="absolute -right-0.5 -top-0.5 h-2.5 w-2.5 rounded-full border-2 border-white bg-amber-400" />
          </span>
          <span>
            <strong className="block text-sm">Latest announcements</strong>
            <span className="text-xs text-emerald-700 dark:text-emerald-300">Tap to view important updates</span>
          </span>
        </span>
        <span className="rounded-full bg-white px-2.5 py-1 text-[10px] font-bold text-emerald-700 shadow-sm dark:bg-emerald-900 dark:text-emerald-200">
          {announcements.length}
        </span>
      </button>

      {open && (
        <div className="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/65 px-4 py-8 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="announcement-title">
          <div className="relative w-full max-w-lg overflow-hidden rounded-3xl bg-white shadow-2xl dark:bg-gray-900">
            <div className="bg-gradient-to-br from-emerald-700 to-emerald-500 px-6 pb-8 pt-6 text-white">
              <div className="flex items-start justify-between gap-4">
                <div className="grid h-12 w-12 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/25">
                  <Bell className="h-6 w-6" />
                </div>
                <button type="button" onClick={close} className="rounded-full bg-white/10 p-2 transition hover:bg-white/20" aria-label="Close announcements">
                  <X className="h-5 w-5" />
                </button>
              </div>
              <p className="mt-6 text-[10px] font-bold uppercase tracking-[0.2em] text-emerald-100">OresamSub update</p>
              <h2 id="announcement-title" className="mt-2 text-2xl font-extrabold leading-tight">{current.title || "Important announcement"}</h2>
            </div>

            <div className="px-6 py-6">
              <div className="prose prose-sm max-w-none text-gray-600 dark:prose-invert dark:text-gray-300" dangerouslySetInnerHTML={{ __html: current.description }} />

              {announcements.length > 1 && (
                <div className="mt-6 flex items-center justify-between border-t border-gray-100 pt-4 dark:border-gray-800">
                  <button type="button" onClick={() => move(-1)} className="flex items-center gap-1 rounded-lg px-3 py-2 text-xs font-bold text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"><ChevronLeft className="h-4 w-4" /> Previous</button>
                  <span className="text-xs font-semibold text-gray-400">{index + 1} of {announcements.length}</span>
                  <button type="button" onClick={() => move(1)} className="flex items-center gap-1 rounded-lg px-3 py-2 text-xs font-bold text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800">Next <ChevronRight className="h-4 w-4" /></button>
                </div>
              )}

              <label className="mt-5 flex cursor-pointer items-center gap-3 rounded-xl bg-gray-50 p-3 text-xs text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                <input type="checkbox" checked={snooze} onChange={(event) => setSnooze(event.target.checked)} className="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" />
                Don&apos;t automatically show announcements for 24 hours
              </label>
              <button type="button" onClick={close} className="mt-4 w-full rounded-xl bg-emerald-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-emerald-700">Got it</button>
            </div>
          </div>
        </div>
      )}
    </>
  );
}
