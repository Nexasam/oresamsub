import { useEffect, useState } from "react";

export default function PWAInstallButton() {
    const [deferredPrompt, setDeferredPrompt] = useState(null);
    const [showInstall, setShowInstall] = useState(false);

    useEffect(() => {
        const handler = (e) => {
            e.preventDefault();

            setDeferredPrompt(e);
            setShowInstall(true);
        };

        window.addEventListener("beforeinstallprompt", handler);

        return () => {
            window.removeEventListener(
                "beforeinstallprompt",
                handler
            );
        };
    }, []);

    const installApp = async () => {
        if (!deferredPrompt) return;

        deferredPrompt.prompt();

        const choice = await deferredPrompt.userChoice;

        if (choice.outcome === "accepted") {
            setShowInstall(false);
        }

        setDeferredPrompt(null);
    };

    if (!showInstall) return null;


    // console.log(showInstall);

    // return (
    //     <button
    //         onClick={installApp}
    //         className="bg-green-600 text-white p-3 rounded"
    //     >
    //         Install App
    //     </button>
    // );

    return (
        <div className="mb-4">
            <div className="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
    
                <div className="font-semibold text-emerald-700">
                    📲 Install OresamSub
                </div>
    
                <div className="text-xs text-gray-600 mt-1">
                    Install OresamSub on your phone for faster access,
                    push notifications and a better experience.
                </div>
    
                <button
                    onClick={installApp}
                    className="
                        mt-3
                        bg-emerald-600
                        hover:bg-emerald-700
                        text-white
                        px-4
                        py-2
                        rounded-lg
                        text-sm
                    "
                >
                    Install App
                </button>
    
            </div>
        </div>
    );
}