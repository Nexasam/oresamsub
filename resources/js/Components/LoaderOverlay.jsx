import { useEffect, useState, useRef } from 'react';
import { Inertia } from '@inertiajs/inertia';
import { InertiaProgress } from '@inertiajs/progress';

export default function LoaderOverlay() {
  const [loading, setLoading] = useState(false);
  const timerRef = useRef(null);

  useEffect(() => {
    InertiaProgress.init({ delay: 100, color: '#fff', showSpinner: false });

    const onStart = () => {
      // Only show loader if the request lasts longer than 1 second
      timerRef.current = setTimeout(() => setLoading(true), 1000);
    };

    const onFinish = () => {
      clearTimeout(timerRef.current);
      setLoading(false);
    };

    Inertia.on('start', onStart);
    Inertia.on('finish', onFinish);
    Inertia.on('error', onFinish);

    return () => {
      clearTimeout(timerRef.current);
      Inertia.off('start', onStart);
      Inertia.off('finish', onFinish);
      Inertia.off('error', onFinish);
    };
  }, []);

  if (!loading) return null;

  return (
    <div className="fixed inset-0 z-30 flex items-center justify-center bg-black/40 backdrop-blur-sm">
      <div className="flex flex-col items-center">
        <svg className="animate-spin h-12 w-12 text-white mb-2" viewBox="0 0 24 24">
          <circle
            className="opacity-25"
            cx="12"
            cy="12"
            r="10"
            stroke="currentColor"
            strokeWidth="4"
            fill="none"
          />
          <path
            className="opacity-75"
            fill="currentColor"
            d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8z"
          />
        </svg>
        <span className="text-white text-lg font-semibold">Loading...</span>
      </div>
    </div>
  );
}
