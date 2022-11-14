<?php

namespace IXCoders\LaravelEcash\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyRemoteHostForCallback
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();
        if ($ip !== '91.144.16.79') {
            $callback_route = config('laravel-ecash-sdk.callback_route');
            $callback_url = route($callback_route);
            return response()->json([
                "callback_url" => $callback_url,
                "message"   => "The following IP address ($ip) isn't authorized to make requests to the callback URL ($callback_url).",
                "timestamp" =>  now()
            ], 403);
        }

        return $next($request);
    }
}
