<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Support\Facades\URL;

class ValidateSignature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $relative
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $relative = null)
    {
        if ($request->hasValidSignatureWhileIgnoring($request->query('signature'), $relative !== 'relative')) {
            return $next($request);
        }

        throw new InvalidSignatureException;
    }
}