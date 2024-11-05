<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Models\County;
use App\Models\Election;
use App\Models\Locality;
use Exception;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

abstract class ElectionPage extends Component implements HasForms
{
    use InteractsWithForms;

    public Election $election;

    #[Url(as: 'tara', history: true)]
    public ?string $country = null;

    #[Url(as: 'judet', history: true)]
    public ?int $county = null;

    #[Url(as: 'localitate', history: true)]
    public ?int $locality = null;

    public function form(Form $form): Form
    {
        return $form
            ->columns(2)
            ->schema([
                Select::make('county')
                    ->options(County::pluck('name', 'id'))
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function (Set $set) {
                        $set('locality', null);
                    })
                    ->placeholder('Național'),

                Select::make('locality')
                    ->options(
                        fn (Get $get) => Locality::query()
                            ->where('county_id', $get('county'))
                            ->limit(1000)
                            ->pluck('name', 'id')
                    )
                    ->searchable()
                    ->live(),

            ]);
    }

    #[Computed]
    protected function level(): string
    {
        if ($this->country) {
            return 'country';
        }

        if ($this->county) {
            return 'county';
        }

        return 'national';

        if ($this->locality) {
            return 'locality';
        }

        throw new Exception;
    }

    /**
     * Used to refresh the map when the country or county changes.
     */
    #[Computed]
    public function mapKey(): string
    {
        return md5("map-{$this->country}-{$this->county}");
    }
}
