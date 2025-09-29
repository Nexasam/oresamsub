import { useState } from "react";

export default function Announcements({ announcements = [] }) {
  const [open, setOpen] = useState(announcements.length > 0);

  if (!announcements || announcements.length === 0) return null;

  return (
    <>
      {open && (
        <div
          className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
          onClick={() => setOpen(false)}
        >
          <div
            className="bg-white dark:bg-gray-900 rounded-xl shadow-lg w-full max-w-md p-6 space-y-4 text-gray-800 dark:text-gray-100"
            onClick={(e) => e.stopPropagation()} // Prevent closing when clicking inside
          >
            {/* Header */}
            <div className="flex justify-between items-center">
              <h2 className="text-lg font-bold flex items-center gap-2">
                🎉 Announcements
              </h2>
              <button
                onClick={() => setOpen(false)}
                className="text-gray-900 dark:text-gray-100 hover:text-red-500 text-xl font-bold"
              >
                &times;
              </button>
            </div>

            {/* Announcements List */}
            <div className="space-y-3 max-h-[60vh] overflow-y-auto pr-2">
              {announcements.map((ann) => (
                <div
                  key={ann.id}
                  className="p-3 rounded-lg border border-emerald-200 dark:border-emerald-600 bg-emerald-50 dark:bg-emerald-900 text-emerald-900 dark:text-emerald-200 text-sm"
                >
                  <h3 className="font-extrabold underline text-emerald-700 dark:text-emerald-300 mb-1">
                    {ann.title}
                  </h3>
                  <div
                    className="text-gray-700 dark:text-gray-200"
                    dangerouslySetInnerHTML={{ __html: ann.description }}
                  />
                </div>
              ))}
            </div>

            {/* Footer Close Button */}
            <div className="flex justify-center mt-3">
              <button
                onClick={() => setOpen(false)}
                className="px-4 py-2 bg-gradient-to-r from-emerald-500 to-green-500 hover:from-emerald-600 hover:to-green-600 text-white font-medium rounded-lg shadow-sm transition transform hover:scale-[1.03] text-sm"
              >
                Close
              </button>
            </div>
          </div>
        </div>
      )}
    </>
  );
}
