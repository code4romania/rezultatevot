<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\ScheduledJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->isLocal()) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }

        $this->registerStrMacros();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        tap($this->app->isLocal(), function (bool $shouldBeEnabled) {
            Model::preventLazyLoading($shouldBeEnabled);
            Model::preventAccessingMissingAttributes($shouldBeEnabled);
        });

        $this->resolveSchedule();

        $this->setSeoDefaults();
    }

    protected function registerStrMacros(): void
    {
        Str::macro('initials', fn (?string $value) => collect(explode(' ', (string) $value))
            ->map(fn (string $word) => Str::upper(Str::substr($word, 0, 1)))
            ->join(''));
    }

    protected function setSeoDefaults(): void
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
            ->favicon()
            ->twitter();
    }

    protected function resolveSchedule(): void
    {
        $this->app->resolving(Schedule::class, function (Schedule $schedule) {
            ScheduledJob::query()
                ->with('election')
                ->where('is_enabled', true)
                ->each(
                    fn (ScheduledJob $job) => $schedule
                        ->job(new $job->job($job))
                        ->cron($job->cron->value)
                        ->withoutOverlapping()
                        ->onOneServer()
                );
        });
    }
}
