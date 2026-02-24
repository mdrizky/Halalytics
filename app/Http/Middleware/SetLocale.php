<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
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
        $rawLanguage = strtolower((string) $request->header('Accept-Language', 'id'));
        $primaryToken = trim(explode(',', $rawLanguage)[0]);
        $baseLanguage = trim(explode('-', $primaryToken)[0]);

        $supported = ['id', 'en', 'ms', 'ar'];
        $resolved = in_array($baseLanguage, $supported, true) ? $baseLanguage : 'id';

        app()->setLocale($resolved);

        return $next($request);
    }
}
