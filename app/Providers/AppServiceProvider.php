<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Article;
use App\Models\Candidate;
use App\Models\Country;
use App\Models\County;
use App\Models\Election;
use App\Models\Locality;
use App\Models\MenuItem;
use App\Models\MenuLocation;
use App\Models\Menu;
use App\Models\Page;
use App\Models\Party;
use App\Models\Record;
use App\Models\Turnout;
use App\Models\User;
use App\Models\Vote;
use App\Models\ScheduledJob;
use Dedoc\Scramble\Scramble;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\Pages\CreateRecord;
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
            'article' => Article::class,
            'candidate' => Candidate::class,
            'country' => Country::class,
            'county' => County::class,
            'election' => Election::class,
            'locality' => Locality::class,
            'menu_item' => MenuItem::class,
            'menu_location' => MenuLocation::class,
            'menu' => Menu::class,
            'page' => Page::class,
            'party' => Party::class,
            'record' => Record::class,
            'turnout' => Turnout::class,
            'user' => User::class,
            'vote' => Vote::class,
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
