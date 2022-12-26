<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Classes\TokenAccess;

class CustomValidateToken
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
        if ($request->header('Authorization') != null) {
            $tokenAccess = new TokenAccess($request->header('Authorization'));
            if ($tokenAccess->validateAPI() == true) {
                Log::debug("Token ON => ".$request->header('Authorization'));
                return $next($request);
            }else{
                Log::debug("Rejected => ".$request->header('Authorization'));
                return abort(403, "TOKEN decline");
            }
        }
    }
}
