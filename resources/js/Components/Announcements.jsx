import { useState } from "react";

export default function Announcements({ announcements = [] }) {
  if (!announcements || announcements.length === 0) return null;

  const [visible, setVisible] = useState(true);

  if (!visible) return null;

  return (
    <div
      className={`fixed bottom-4 right-4 z-50 
                  transition-all duration-500 ease-in-out ${
                    visible ? "opacity-100 translate-y-0" : "opacity-0 translate-y-10"
                  }`}
    >
      <div className="bg-emerald-300 dark:bg-emerald-900 text-emerald-900 dark:text-emerald-100 
                      rounded-xl shadow-lg w-80 max-h-64 overflow-y-auto p-3 space-y-3 
                      scrollbar-thin scrollbar-thumb-emerald-300 dark:scrollbar-thumb-emerald-700 
                      scrollbar-track-transparent">
        
        {/* Close button */}
        <div className="flex justify-end">
          <button
            onClick={() => setVisible(false)}
            className="text-emerald-600 dark:text-emerald-200 hover:opacity-70 text-sm"
          >
            ✖ Close
          </button>
        </div>

        {/* Heading */}
        <h3 className="text-lg font-bold text-emerald-800 dark:text-emerald-200 border-b border-emerald-200 dark:border-emerald-700 pb-2">
          📢 Notifications
        </h3>

        {/* Announcements list */}
        {announcements.map((a, i) => (
          <div
            key={i}
            className="bg-emerald-400 dark:bg-emerald-800 p-3 rounded-lg text-sm"
          >
            <span className="font-semibold">{a.title ?? "Announcement"}:</span>{" "}
            {a.description}
          </div>
        ))}
      </div>
    </div>
  );
}
