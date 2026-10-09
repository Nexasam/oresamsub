import { useEffect, useState } from "react";
import { Bell, CheckCircle2, ChevronLeft, ChevronRight, RadioTower, X } from "lucide-react";
import axios from "axios";

const SNOOZE_KEY = "oresamsub-announcements-snoozed-until";
const RESTORED_SEEN_PREFIX = "oresamsub-network-restored-seen:";
const SNOOZE_DURATION = 24 * 60 * 60 * 1000;

export default function Announcements({ announcements = [], networkNotices = {} }) {
  const [index, setIndex] = useState(0);
  const [open, setOpen] = useState(false);
  const [snooze, setSnooze] = useState(false);
  const [restoredSeenVersion, setRestoredSeenVersion] = useState(0);
  const [dismissedSignature, setDismissedSignature] = useState(null);

  const activeNetworkNotices = Array.isArray(networkNotices.active) ? networkNotices.active : [];
  const restoredNetworkNotices = Array.isArray(networkNotices.restored) ? networkNotices.restored : [];
  const visibleRestoredNotices = restoredNetworkNotices.filter((notice) => {
    if (typeof window === "undefined") return false;
    return !localStorage.getItem(`${RESTORED_SEEN_PREFIX}${notice.once_key}`);
  });

  const items = [
    ...activeNetworkNotices.map((notice) => ({
      ...notice,
      modalType: "network_issue",
      isHtml: false,
      description: notice.message,
    })),
    ...visibleRestoredNotices.map((notice) => ({
      ...notice,
      modalType: "network_restored",
      isHtml: false,
      description: notice.message,
    })),
    ...announcements.map((announcement) => ({
      ...announcement,
      modalType: "announcement",
      isHtml: true,
    })),
  ];
  const noticeSignature = items
    .map((item) => `${item.modalType}:${item.id}:${item.updated_at || item.restored_at || item.created_at || ""}`)
    .join("|");

  useEffect(() => {
    if (items.length === 0) return;
    if (noticeSignature && dismissedSignature === noticeSignature) return;

    if (index >= items.length) {
      setIndex(0);
    }

    const snoozedUntil = Number(localStorage.getItem(SNOOZE_KEY) || 0);
    const hasOperationalNotice = activeNetworkNotices.length > 0 || visibleRestoredNotices.length > 0;

    if (hasOperationalNotice || snoozedUntil <= Date.now()) {
      localStorage.removeItem(SNOOZE_KEY);
      setOpen(true);
    }
  }, [items.length, noticeSignature, dismissedSignature, restoredSeenVersion]);

  if (items.length === 0) return null;

  const current = items[index] || items[0];
  const isNetworkIssue = current.modalType === "network_issue";
  const isNetworkRestored = current.modalType === "network_restored";
  const move = (direction) => {
    setIndex((previous) =>
      (previous + direction + items.length) % items.length
    );
  };
  const close = () => {
    const restoredNoticeKeys = visibleRestoredNotices.map((notice) => notice.once_key);

    setDismissedSignature(noticeSignature);
    setOpen(false);
    setSnooze(false);
    setRestoredSeenVersion((version) => version + 1);

    visibleRestoredNotices.forEach((notice) => {
      localStorage.setItem(`${RESTORED_SEEN_PREFIX}${notice.once_key}`, "1");
    });

    if (snooze) {
      localStorage.setItem(SNOOZE_KEY, String(Date.now() + SNOOZE_DURATION));
    }

    if (restoredNoticeKeys.length > 0) {
      try {
        const markSeenUrl = typeof route === "function"
          ? route("dashboard.network_restored_notices.seen")
          : "/dashboard/network-restored-notices/seen";

        axios.post(markSeenUrl, {
          notice_keys: restoredNoticeKeys,
        }).catch(() => {});
      } catch (error) {
        // The modal has already closed; do not let a route/ajax issue block the customer.
      }
    }
  };

  const headerClass = isNetworkIssue
    ? "bg-gradient-to-br from-amber-600 to-orange-500"
    : isNetworkRestored
      ? "bg-gradient-to-br from-sky-700 to-emerald-500"
      : "bg-gradient-to-br from-emerald-700 to-emerald-500";
  const buttonClass = isNetworkIssue
    ? "border-amber-200 bg-amber-50 text-amber-950 hover:border-amber-300 dark:border-amber-800 dark:bg-amber-950 dark:text-amber-100"
    : "border-emerald-200 bg-emerald-50 text-emerald-900 hover:border-emerald-300 dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-100";
  const buttonTitle = activeNetworkNotices.length > 0
    ? "Network service notice"
    : visibleRestoredNotices.length > 0
      ? "Service restored update"
      : "Latest announcements";

  return (
    <>
      <button
        type="button"
        onClick={() => setOpen(true)}
        className={`my-2 flex w-full items-center justify-between rounded-xl border px-4 py-3 text-left transition hover:shadow-sm ${buttonClass}`}
      >
        <span className="flex items-center gap-3">
          <span className={`relative grid h-9 w-9 place-items-center rounded-full text-white ${isNetworkIssue ? "bg-amber-600" : "bg-emerald-600"}`}>
            {activeNetworkNotices.length > 0 ? <RadioTower className="h-4 w-4" /> : <Bell className="h-4 w-4" />}
            <span className="absolute -right-0.5 -top-0.5 h-2.5 w-2.5 rounded-full border-2 border-white bg-amber-400" />
          </span>
          <span>
            <strong className="block text-sm">{buttonTitle}</strong>
            <span className="text-xs opacity-80">Tap to view important updates</span>
          </span>
        </span>
        <span className="rounded-full bg-white px-2.5 py-1 text-[10px] font-bold text-emerald-700 shadow-sm dark:bg-emerald-900 dark:text-emerald-200">
          {items.length}
        </span>
      </button>

      {open && (
        <div className="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/65 px-4 py-8 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="announcement-title">
          <div className="relative w-full max-w-lg overflow-hidden rounded-3xl bg-white shadow-2xl dark:bg-gray-900">
            <div className={`${headerClass} px-6 pb-8 pt-6 text-white`}>
              <div className="flex items-start justify-between gap-4">
                <div className="grid h-12 w-12 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/25">
                  {isNetworkIssue ? <RadioTower className="h-6 w-6" /> : isNetworkRestored ? <CheckCircle2 className="h-6 w-6" /> : <Bell className="h-6 w-6" />}
                </div>
                <button type="button" onClick={close} className="rounded-full bg-white/10 p-2 transition hover:bg-white/20" aria-label="Close announcements">
                  <X className="h-5 w-5" />
                </button>
              </div>
              <p className="mt-6 text-[10px] font-bold uppercase tracking-[0.2em] text-white/80">
                {isNetworkIssue ? "Telco network issue" : isNetworkRestored ? "Service restored" : "OresamSub update"}
              </p>
              <h2 id="announcement-title" className="mt-2 text-2xl font-extrabold leading-tight">{current.title || "Important announcement"}</h2>
            </div>

            <div className="px-6 py-6">
              {current.isHtml ? (
                <div className="prose prose-sm max-w-none text-gray-600 dark:prose-invert dark:text-gray-300" dangerouslySetInnerHTML={{ __html: current.description }} />
              ) : (
                <p className="text-sm leading-7 text-gray-700 dark:text-gray-300">{current.description}</p>
              )}

              {items.length > 1 && (
                <div className="mt-6 flex items-center justify-between border-t border-gray-100 pt-4 dark:border-gray-800">
                  <button type="button" onClick={() => move(-1)} className="flex items-center gap-1 rounded-lg px-3 py-2 text-xs font-bold text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"><ChevronLeft className="h-4 w-4" /> Previous</button>
                  <span className="text-xs font-semibold text-gray-400">{index + 1} of {items.length}</span>
                  <button type="button" onClick={() => move(1)} className="flex items-center gap-1 rounded-lg px-3 py-2 text-xs font-bold text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800">Next <ChevronRight className="h-4 w-4" /></button>
                </div>
              )}

              {!isNetworkIssue && !isNetworkRestored && (
                <label className="mt-5 flex cursor-pointer items-center gap-3 rounded-xl bg-gray-50 p-3 text-xs text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                  <input type="checkbox" checked={snooze} onChange={(event) => setSnooze(event.target.checked)} className="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" />
                  Don&apos;t automatically show announcements for 24 hours
                </label>
              )}
              <button type="button" onClick={close} className="mt-4 w-full rounded-xl bg-emerald-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-emerald-700">Got it</button>
            </div>
          </div>
        </div>
      )}
    </>
  );
}
