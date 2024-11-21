<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCloudfrontHeaders
{
    protected string $header = 'Cloudfront-Forwarded-Proto';

    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasHeader($this->header)) {
            $request->headers->add([
                'X-Forwarded-Proto' => $request->header($this->header),
                'X-Forwarded-Port' => '443',
            ]);
        }

        return $next($request);
    }
}
