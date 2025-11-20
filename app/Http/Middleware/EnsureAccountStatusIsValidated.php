<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountStatusIsValidated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user(); 


        if (!$user || ($user->status ?? null) !== 'validated') {
            return response()->json(['error' => 'Unauthorized. Validated users only.','status' => $user->status], 403);
        }
        return $next($request);
    }
}
