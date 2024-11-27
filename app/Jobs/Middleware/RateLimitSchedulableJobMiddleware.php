<?php

declare(strict_types=1);

namespace App\Jobs\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class RateLimitSchedulableJobMiddleware
{
    public function __construct(
        public string $key
    ) {
        //
    }

    /**
     * Process the queued job.
     *
     * @param \Closure(object): void $next
     */
    public function handle(object $job, Closure $next): void
    {
        Redis::throttle('rate-limit-job:' . $this->key)
            ->allow(1)
            ->every(1)
            ->then(
                fn () => $next($job),
                fn () => $job->release(2)
            );
    }
}
