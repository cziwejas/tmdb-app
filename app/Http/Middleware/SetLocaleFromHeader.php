<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromHeader
{
    protected array $supportedLocales = ['pl', 'en', 'de'];

    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Accept-Language');

        if ($header) {
            // np. pl-PL,pl;q=0.9,en;q=0.8
            $locale = substr($header, 0, 2);

            if (in_array($locale, $this->supportedLocales)) {
                app()->setLocale($locale);
            } else {
                app()->setLocale('en');
            }
        } else {
            app()->setLocale('en');
        }

        return $next($request);
    }
}