<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuditLogMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (Auth::check()) {
            $method = $request->method();
            $action = match (true) {
                $method === 'POST' => 'create',
                in_array($method, ['PUT', 'PATCH']) => 'update',
                $method === 'DELETE' => 'delete',
                default => null,
            };

            if ($action && str_starts_with($request->path(), 'admin/')) {
                AuditLog::create([
                    'user_id' => Auth::id(),
                    'action' => $action,
                    'model_type' => $request->route()?->getAction('controller') ?? $request->path(),
                    'model_id' => $request->route('record'),
                    'old_values' => null,
                    'new_values' => $request->except(['_token', '_method', 'password', 'password_confirmation']),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
        }

        return $response;
    }
}
