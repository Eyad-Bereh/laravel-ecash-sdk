<?php

namespace IXCoders\LaravelEcash\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VerifyResponseToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $rules = config('laravel-ecash-sdk.callback_validation_rules');
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid data has been supplied.',
                'errors' => $validator->errors(),
                'timestamp' => now(),
            ], 422);
        }

        $instance = app('ecash.laravel');
        $is_valid = $instance->checkVerificationToken($request->Token, $request->TransactionNo, $request->Amount, $request->OrderRef);
        if (! $is_valid) {
            return response()->json([
                'message' => 'Invalid token has been supplied.',
                'timestamp' => now(),
                'data' => $request->only(['Token', 'TransactionNo', 'Amount', 'OrderRef']),
            ], 422);
        }

        return $next($request);
    }
}
