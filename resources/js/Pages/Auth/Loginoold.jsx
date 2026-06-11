import { useState } from "react";
import { Link, useForm, usePage } from "@inertiajs/react";
import AuthLayout from "@/Layouts/AuthLayout";

export default function Login() {
  const { props } = usePage();
  const [showPassword, setShowPassword] = useState(false);

  const { data, setData, post, processing, errors } = useForm({
    email: "",
    password: "",
  });

  function submit(e) {
    e.preventDefault();
    // post(route("login")); // Laravel route('login')
    // post('/login2'); // Laravel route('login')
    post(route('inertia.login.store')); // Laravel route('login')
  }

  return (
    <AuthLayout title="Login">
      <div className="relative min-h-screen w-full flex items-center justify-center overflow-hidden">
        <p>{props.user}</p>
        {/* Background pattern */}
        <div className="absolute inset-0 z-0 pointer-events-none">
          <div className="w-full h-full bg-gray-50 dark:bg-gray-900 bg-[radial-gradient(circle,_rgba(0,0,0,0.05)_1px,_transparent_1px)] dark:bg-[radial-gradient(circle,_rgba(255,255,255,0.05)_1px,_transparent_1px)] [background-size:22px_22px]" />
        </div>

        {/* Login Card */}
        <div className="relative z-10 pt-10 pb-6 max-w-full w-full mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
          <div className="flex justify-center mb-4">
            <img
              src="/assets/logo_imgs/oresamsublogo.jpeg"
              alt="OresamSub Logo"
              className="h-20 w-20 rounded-full shadow-md"
            />
          </div>
           
          <h2 className="text-2xl mt-3 font-bold text-center mb-6 text-gray-900 dark:text-white">
            Login to OresamSub
          </h2>

          {/* Alerts from backend */}
          {props.flash?.error && (
            <div className="mb-4 text-red-600 text-sm text-center">{props.flash.error}</div>
          )}
          {props.flash?.success && (
            <div className="bg-green-100 border border-green-300 text-green-700 text-sm p-2 rounded mb-4 text-center">
              {props.flash.success}
            </div>
          )}
          {props.flash?.failure && (
            <div className="bg-red-100 border border-red-300 text-red-700 text-sm p-2 rounded mb-4 text-center">
              {props.flash.failure}
            </div>
          )}

          {/* Form */}
          <form onSubmit={submit}>
            {/* Email */}
            <div className="mb-4">
              <label htmlFor="email" className="block text-sm mb-1">
                Email
              </label>
              <input
                type="text"
                id="email"
                value={data.email}
                onChange={(e) => setData("email", e.target.value)}
                required
                placeholder="Email or Username or Phone"
                className="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
              {errors.email && <div className="text-red-500 text-sm mt-1">{errors.email}</div>}
            </div>

            {/* Password */}
            <div className="mb-0">
              <label htmlFor="password" className="block text-sm mb-1">
                Password
              </label>
              <div className="relative">
                <input
                  type={showPassword ? "text" : "password"}
                  id="password"
                  value={data.password}
                  onChange={(e) => setData("password", e.target.value)}
                  required
                  className="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
                <button
                  type="button"
                  onClick={() => setShowPassword(!showPassword)}
                  className="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-100"
                >
                  {showPassword ? "🙈" : "👁️"}
                </button>
              </div>
              {errors.password && (
                <div className="text-red-500 text-sm mt-1">{errors.password}</div>
              )}
            </div>

            {/* Forgot password */}
            <div className="mb-2 text-right">
              <Link
                href={route("password.request")}
                className="text-xs text-blue-600 dark:text-blue-400 hover:underline"
              >
                Forgot your password?
              </Link>
            </div>

            {/* Submit */}
            <div className="mt-6">
              <button
                type="submit"
                disabled={processing}
                className="w-full py-2 px-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50"
              >
                {processing ? (
                  <span className="flex items-center justify-center gap-2">
                    <svg className="animate-spin h-4 w-4" viewBox="0 0 24 24">
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
                    Logging in...
                  </span>
                ) : (
                  "🔐 Login"
                )}
              </button>
            </div>
          </form>

          {/* Register link */}
          <p className="text-xs text-center mt-6 text-gray-500 dark:text-gray-400">
            Don&apos;t have an account?{" "}
            <a href={route("register")} className="text-blue-600 dark:text-blue-400 font-semibold">
              Register
            </a>
          </p>
        </div>
      </div>
    </AuthLayout>
  );
}
