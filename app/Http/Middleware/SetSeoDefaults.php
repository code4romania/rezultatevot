<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class SetSeoDefaults
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        seo()
            ->withUrl()
            ->title(
                default: config('app.name'),
                modifier: fn (string $title) => $title . ' — ' . config('app.name')
            )
            // TODO: Add a default description
            // ->description(default: '')
            ->locale(app()->getLocale())
            ->image(Vite::asset('resources/images/banner-social.png'))
            ->favicon()
            ->twitter();

        return $next($request);
    }
}
