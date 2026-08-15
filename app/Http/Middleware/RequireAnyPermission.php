<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class RequireAnyPermission {
    public function handle(Request $request, Closure $next, string ...$permissions) {
        abort_unless($request->user() && collect($permissions)->contains(fn ($permission) => $request->user()->hasPermission($permission)), 403);
        return $next($request);
    }
}
