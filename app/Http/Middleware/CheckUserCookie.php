<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class CheckUserCookie
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle(Request $request, Closure $next)
    {
        $cookieUserAnswer = Cookie::get('user_answer');
        $cookieEntityToken = Cookie::get('user_entity_token');

        if (!empty($cookieUserAnswer)) {
            return redirect()->route('finish');
        }

        return $next($request);
    }
}
