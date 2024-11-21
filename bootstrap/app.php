<?php

declare(strict_types=1);

use Filament\Facades\Filament;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Queue\Console\PruneBatchesCommand;
use Laravel\Horizon\Console\SnapshotCommand;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        api: __DIR__ . '/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies('*');

        // Fix for `Route [login] not defined` exception
        // @see https://github.com/filamentphp/filament/discussions/5226#discussioncomment-10555366
        $middleware->redirectGuestsTo(fn () => Filament::getCurrentPanel()?->getLoginUrl() ?? route('front.index'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        Integration::handles($exceptions);
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule
            ->command(SnapshotCommand::class)
            ->everyFiveMinutes();

        $schedule
            ->command(PruneBatchesCommand::class, [
                'hours' => 24 * 7,
                'unfinished' => 24 * 7,
                'cancelled' => 24 * 7,
            ])
            ->daily();
    })
    ->create();
