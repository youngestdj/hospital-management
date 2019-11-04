<?php

namespace App\Http\Middleware;

use Closure;
use App\Exceptions\CustomException;

class TargetUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $target)
    {
        $request->attributes->add(['target' => $target]);
        return $next($request);
    }
}
