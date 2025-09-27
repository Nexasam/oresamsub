import { useEffect } from "react";

export default function Modal({ isOpen, title, children, onClose }) {
  // Prevent scrolling when modal is open
  useEffect(() => {
    document.body.style.overflow = isOpen ? "hidden" : "auto";
    return () => (document.body.style.overflow = "auto");
  }, [isOpen]);

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div className="bg-white dark:bg-gray-800 rounded-xl shadow-lg max-w-sm w-full p-6 relative">
        <h2 className="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">{title}</h2>
        <div className="space-y-4">{children}</div>
        <button
          onClick={onClose}
          className="absolute top-3 right-3 text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-100 text-xl font-bold"
        >
          &times;
        </button>
      </div>
    </div>
  );
}
