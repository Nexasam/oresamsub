import { useState, useEffect, forwardRef, useImperativeHandle } from "react";

const PwaInstallPopup = forwardRef((props, ref) => {
  const [deferredPrompt, setDeferredPrompt] = useState(null);
  const [visible, setVisible] = useState(false);

  const cooldownDays = 2;
  const cooldownMs = cooldownDays * 24 * 60 * 60 * 1000;

  useEffect(() => {
    if ("serviceWorker" in navigator) {
      window.addEventListener("load", () => {
        navigator.serviceWorker
          .register("/service-worker.js")
          .then((reg) => console.log("SW registered:", reg))
          .catch((err) => console.log("SW failed:", err));
      });
    }
  }, []);

  useEffect(() => {
    const handleBeforeInstallPrompt = (e) => {
      const dismissedAt = localStorage.getItem("installDismissedAt");
      if (dismissedAt && Date.now() - dismissedAt < cooldownMs) return;

      e.preventDefault();
      setDeferredPrompt(e);
      setVisible(true);
    };
    window.addEventListener("beforeinstallprompt", handleBeforeInstallPrompt);
    return () => window.removeEventListener("beforeinstallprompt", handleBeforeInstallPrompt);
  }, []);

  useImperativeHandle(ref, () => ({
    promptInstall: async () => {
      if (deferredPrompt) {
        deferredPrompt.prompt();
        const { outcome } = await deferredPrompt.userChoice;
        console.log("User choice:", outcome);
        setDeferredPrompt(null);
        setVisible(false);
      }
    },
  }));

  const handleDismiss = () => {
    localStorage.setItem("installDismissedAt", Date.now());
    setVisible(false);
  };

  if (!visible) return null;

  return (
    <div className="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50">
      <div className="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-sm w-full p-6 text-center">
        <h2 className="text-xl font-semibold text-gray-900 dark:text-white mb-3">
          Install OresamSub
        </h2>
        <p className="text-gray-600 dark:text-gray-300 mb-6">
          Add this app to your home screen for a faster, app-like experience.
        </p>
        <div className="flex gap-3 justify-center">
          <button
            onClick={handleDismiss}
            className="px-4 py-2 rounded-xl bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-600"
          >
            Not now
          </button>
          <button
            onClick={() => ref.current.promptInstall()}
            className="px-4 py-2 rounded-xl bg-emerald-600 text-white font-medium shadow hover:bg-emerald-700"
          >
            Install
          </button>
        </div>
      </div>
    </div>
  );
});

export default PwaInstallPopup;
