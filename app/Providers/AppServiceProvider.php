<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\ScheduledJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\QueryException;
use Illuminate\Support\Number;
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

        Number::useLocale($this->app->getLocale());

        $this->enforceMorphMap();

        $this->resolveSchedule();

        $this->setSeoDefaults();
    }

    protected function registerStrMacros(): void
    {
        Str::macro('initials', fn (?string $value) => collect(explode(' ', (string) $value))
            ->map(fn (string $word) => Str::upper(Str::substr($word, 0, 1)))
            ->join(''));
    }

    protected function enforceMorphMap(): void
    {
        Relation::enforceMorphMap([
            'candidate' => \App\Models\Candidate::class,
            'country' => \App\Models\Country::class,
            'county' => \App\Models\County::class,
            'election' => \App\Models\Election::class,
            'locality' => \App\Models\Locality::class,
            'party' => \App\Models\Party::class,
            'record' => \App\Models\Record::class,
            'turnout' => \App\Models\Turnout::class,
            'user' => \App\Models\User::class,
        ]);
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
            try {
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
            } catch (QueryException $th) {
                // fix for composer install
            }
        });
    }
}
