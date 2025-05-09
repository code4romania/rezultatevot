<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\ScheduledJob;
use Dedoc\Scramble\Scramble;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\Pages\CreateRecord;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\QueryException;
use Illuminate\Encryption\MissingAppKeyException;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Config;
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

        JsonResource::withoutWrapping();

        tap($this->getAppVersion(), function (string $version) {
            Config::set('scramble.info.version', $version);
            Config::set('sentry.release', $version);
        });

        SpatieMediaLibraryFileUpload::configureUsing(function (SpatieMediaLibraryFileUpload $fileUpload) {
            $fileUpload->disk(config('filament.default_filesystem_disk'));
        });

        TiptapEditor::configureUsing(function (TiptapEditor $editor) {
            $editor->disk(config('filament.default_filesystem_disk'));
        });
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

        CreateRecord::disableCreateAnother();
        CreateAction::configureUsing(fn (CreateAction $action) => $action->createAnother(false));

        $this->enforceMorphMap();

        $this->resolveSchedule();

        Scramble::registerApi('v1', [
            'api_path' => 'api/v1',
        ]);
    }

    /**
     * Read the application version.
     *
     * @return string
     */
    public function getAppVersion(): string
    {
        $version = base_path('.version');

        if (! file_exists($version)) {
            return 'develop';
        }

        return trim(file_get_contents($version));
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
            'article' => \App\Models\Article::class,
            'candidate' => \App\Models\Candidate::class,
            'country' => \App\Models\Country::class,
            'county' => \App\Models\County::class,
            'election' => \App\Models\Election::class,
            'locality' => \App\Models\Locality::class,
            'menu_item' => \App\Models\MenuItem::class,
            'menu_location' => \App\Models\MenuLocation::class,
            'menu' => \App\Models\Menu::class,
            'page' => \App\Models\Page::class,
            'party' => \App\Models\Party::class,
            'record' => \App\Models\Record::class,
            'turnout' => \App\Models\Turnout::class,
            'user' => \App\Models\User::class,
            'vote' => \App\Models\Vote::class,
        ]);
    }

    protected function resolveSchedule(): void
    {
        $this->app->resolving(Schedule::class, function (Schedule $schedule) {
            try {
                ScheduledJob::query()
                    ->with('election')
                    ->where('is_enabled', true)
                    ->each(fn (ScheduledJob $job) => rescue(
                        fn () => $schedule
                            ->job(new $job->job($job))
                            ->cron($job->cron->value)
                            ->withoutOverlapping()
                            ->onOneServer()
                    ));
            } catch (QueryException|MissingAppKeyException $th) {
                // fix for composer install
            }
        });
    }
}
