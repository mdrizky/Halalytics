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
        $locale = null;

        // Check session (for web requests)
        if ($request->hasSession() && $request->session()->has('locale')) {
            $locale = $request->session()->get('locale');
        }

        // Fallback to Accept-Language header
        if (!$locale) {
            $rawLanguage = strtolower((string) $request->header('Accept-Language', 'id'));
            $primaryToken = trim(explode(',', $rawLanguage)[0]);
            $locale = trim(explode('-', $primaryToken)[0]);
        }

        $supported = ['id', 'en'];
        $resolved = in_array($locale, $supported, true) ? $locale : 'id';

        app()->setLocale($resolved);

        return $next($request);
    }
}
