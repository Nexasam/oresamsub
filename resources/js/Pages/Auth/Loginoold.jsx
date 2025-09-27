import { useForm } from "@inertiajs/react";

export default function Login() {
  const { data, setData, post, processing, errors } = useForm({
    email: "",
    password: "",
    remember: false,
  });

  const submit = (e) => {
    e.preventDefault();
    // post(route("login.inertia.store"));
    post("/login2");
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-gray-100">
      <form
        onSubmit={submit}
        className="w-full max-w-md bg-white shadow-md rounded-lg p-6 space-y-4"
      >
        <h1 className="text-xl font-bold">Login</h1>

        {/* Email */}
        <div>
          <label className="block text-sm font-medium">Email</label>
          <input
            type="email"
            value={data.email}
            onChange={(e) => setData("email", e.target.value)}
            className="mt-1 block w-full border rounded p-2"
          />
          {errors.email && (
            <div className="text-red-500 text-sm">{errors.email}</div>
          )}
        </div>

        {/* Password */}
        <div>
          <label className="block text-sm font-medium">Password</label>
          <input
            type="password"
            value={data.password}
            onChange={(e) => setData("password", e.target.value)}
            className="mt-1 block w-full border rounded p-2"
          />
          {errors.password && (
            <div className="text-red-500 text-sm">{errors.password}</div>
          )}
        </div>

        {/* Remember Me */}
        <label className="inline-flex items-center">
          <input
            type="checkbox"
            checked={data.remember}
            onChange={(e) => setData("remember", e.target.checked)}
            className="mr-2"
          />
          Remember Me
        </label>

        {/* Submit */}
        <button
          type="submit"
          disabled={processing}
          className="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700"
        >
          {processing ? "Logging in..." : "Login"}
        </button>
      </form>
    </div>
  );
}
