<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Enums\DataLevel;
use App\Models\Country;
use App\Models\County;
use App\Models\Election;
use App\Models\Locality;
use App\Services\CacheService;
use ArchTech\SEO\SEOManager;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

abstract class ElectionPage extends Component implements HasForms
{
    use InteractsWithForms;

    public Election $election;

    #[Url(as: 'nivel', history: true, except: DataLevel::TOTAL)]
    public DataLevel $level = DataLevel::TOTAL;

    #[Url(as: 'tara', history: true)]
    public ?string $country = null;

    #[Url(as: 'judet', history: true)]
    public ?int $county = null;

    #[Url(as: 'localitate', history: true)]
    public ?int $locality = null;

    public function mount()
    {
        $this->checkDefaultPage();

        $validation = Validator::make([
            'country' => $this->country,
            'county' => $this->county,
            'locality' => $this->locality,
        ], [
            'country' => ['nullable', 'exists:countries,id'],
            'county' => ['nullable', 'exists:counties,id'],
            'locality' => ['nullable', 'exists:localities,id'],
        ]);

        abort_if($validation->fails(), 404);
    }

    public function form(Form $form): Form
    {
        $whereHasKey = match (static::class) {
            ElectionResults::class => 'records',
            ElectionTurnouts::class => 'turnouts',
        };

        return $form
            ->schema([
                Grid::make()
                    ->columns(3)
                    ->maxWidth(MaxWidth::ThreeExtraLarge)
                    ->schema([
                        Select::make('level')
                            ->label(__('app.field.level'))
                            ->hiddenLabel()
                            ->options(DataLevel::options())
                            ->default(DataLevel::NATIONAL->value)
                            ->enum(DataLevel::class)
                            ->afterStateUpdated(function (Set $set) {
                                $set('country', null);
                                $set('county', null);
                                $set('locality', null);
                            })
                            ->selectablePlaceholder(false)
                            ->native(false)
                            ->lazy(),

                        Select::make('country')
                            ->label(__('app.field.country'))
                            ->placeholder(__('app.field.country'))
                            ->hiddenLabel()
                            ->options(function () use ($whereHasKey) {
                                return CacheService::make(['countries', $whereHasKey], $this->election)
                                    ->remember(
                                        fn () => Country::query()
                                            ->whereHas($whereHasKey, function (Builder $query) {
                                                $query->whereBelongsTo($this->election)
                                                    ->whereNotNull('country_id');
                                            })
                                            ->pluck('name', 'id')
                                    );
                            })
                            ->afterStateUpdated(function (Set $set) {
                                $set('county', null);
                                $set('locality', null);
                            })
                            ->visible(fn (Get $get) => DataLevel::isValue($get('level'), DataLevel::DIASPORA))
                            ->searchable()
                            ->lazy(),

                        Select::make('county')
                            ->label(__('app.field.county'))
                            ->placeholder(__('app.field.county'))
                            ->hiddenLabel()
                            ->options(function () use ($whereHasKey) {
                                return CacheService::make(['counties', $whereHasKey], $this->election)
                                    ->remember(
                                        fn () => County::query()
                                            ->whereHas($whereHasKey, function (Builder $query) use ($whereHasKey) {
                                                $query->whereBelongsTo($this->election)
                                                    ->whereNotNull("{$whereHasKey}.county_id");
                                            })
                                            ->pluck('name', 'id')
                                    );
                            })
                            ->afterStateUpdated(function (Set $set) {
                                $set('locality', null);
                            })
                            ->visible(fn (Get $get) => DataLevel::isValue($get('level'), DataLevel::NATIONAL))
                            ->searchable()
                            ->lazy(),

                        Select::make('locality')
                            ->label(__('app.field.locality'))
                            ->hiddenLabel()
                            ->placeholder(__('app.field.locality'))
                            ->options(function (Get $get) use ($whereHasKey) {
                                $county_id = $get('county');

                                return CacheService::make(['localities', $whereHasKey], $this->election, county: $county_id)
                                    ->remember(
                                        fn () => Locality::query()
                                            ->where('county_id', $county_id)
                                            ->whereHas($whereHasKey, function (Builder $query) use ($county_id) {
                                                $query->whereBelongsTo($this->election);

                                                if ($county_id !== 403) {
                                                    $query->whereNull('parent_id');
                                                }
                                            })
                                            ->limit(150)
                                            ->pluck('name', 'id')
                                    );
                            })
                            ->visible(fn (Get $get) => DataLevel::isValue($get('level'), DataLevel::NATIONAL) &&
                            ! \is_null($get('county')))
                            ->searchable()
                            ->lazy(),
                    ]),
            ]);
    }

    public function componentKey(string $name, ?DataLevel $level = null, ?string $country = null, ?int $county = null, ?int $locality = null): string
    {
        $key = collect([
            "component-{$name}",
        ]);

        if (filled($level)) {
            $key->push("level-{$level->value}");
        }

        if (filled($country)) {
            $key->push("country-{$country}");
        }

        if (filled($county)) {
            $key->push("county-{$county}");
        }

        if (filled($locality)) {
            $key->push("locality-{$locality}");
        }

        return hash('xxh128', $key->join('|'));
    }

    /**
     * Used to refresh the embed component when the url changes.
     */
    #[Computed]
    public function embedKey(): string
    {
        return hash('xxh128', "embed-{$this->getEmbedUrl()}");
    }

    #[On('map:click')]
    public function refreshData(?string $country = null, ?int $county = null, ?int $locality = null): void
    {
        if (filled($country)) {
            $this->country = $country;
        }

        if (filled($county)) {
            $this->county = $county;
        }

        if (filled($locality)) {
            $this->locality = $locality;
        }
    }

    #[Computed]
    public function getQueryParameters(): array
    {
        return collect([
            'nivel' => $this->level->value,
            'tara' => $this->country,
            'judet' => $this->county,
            'localitate' => $this->locality,
        ])
            ->filter(fn ($value) => filled($value) && $value !== DataLevel::TOTAL->value)
            ->toArray();
    }

    public function seo(string $title): SEOManager
    {
        return seo()
            ->title(\sprintf(
                '%s | %s %s',
                $title,
                $this->election->type->getLabel(),
                $this->election->year
            ));
    }

    private function checkDefaultPage(): void
    {
        if (blank($this->getQueryParameters()) && data_get($this->election, 'properties.default_place')) {
            $level = data_get($this->election, 'properties.default_place.level', 'total');
            $country = data_get($this->election, 'properties.default_place.country', null);
            $county = data_get($this->election, 'properties.default_place.county', null);
            $locality = data_get($this->election, 'properties.default_place.locality', null);

            if ($level) {
                $this->level = DataLevel::from($level);
            }

            if ($country) {
                $this->country = $country;
                $this->level = DataLevel::DIASPORA;
            }

            if ($county) {
                $this->county = (int) $county;
                $this->level = DataLevel::NATIONAL;
            }

            if ($locality) {
                $this->locality = (int) $locality;
            }
        }
    }
}
