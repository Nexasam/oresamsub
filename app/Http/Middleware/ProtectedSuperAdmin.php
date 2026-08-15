<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class ProtectedSuperAdmin {
    public function handle(Request $request, Closure $next) {
        abort_unless(strcasecmp((string) $request->user()?->email, 'adebsholey4real@gmail.com') === 0, 403);
        return $next($request);
    }
}
