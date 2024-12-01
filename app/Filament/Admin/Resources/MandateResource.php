<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MandateResource\Pages;
use App\Filament\Filters\LocationFilter;
use App\Models\Candidate;
use App\Models\Mandate;
use App\Models\Party;
use App\Tables\Columns\LocationColumn;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MandateResource extends Resource
{
    protected static ?string $model = Mandate::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string
    {
        return __('app.navigation.election_data');
    }

    public static function getModelLabel(): string
    {
        return __('app.mandate.label.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.mandate.label.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('county_id')
                    ->relationship('county', 'name')
                    ->label(__('app.field.county'))
                    ->searchable()
                    ->preload()
                    ->live(),

                Select::make('locality_id')
                    ->relationship(
                        'locality',
                        'name',
                        fn (Builder $query, Get $get) => $query
                            ->where('county_id', $get('county_id'))
                    )
                    ->label(__('app.field.locality'))
                    ->searchable(),

                MorphToSelect::make('votable')
                    ->types([
                        MorphToSelect\Type::make(Party::class)
                            ->titleAttribute('name'),
                        MorphToSelect\Type::make(Candidate::class)
                            ->titleAttribute('name'),
                    ])
                    ->searchable()
                    ->required()
                    ->columnSpanFull(),

                TextInput::make('initial')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(255),

                TextInput::make('redistributed')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('county', 'locality'))
            ->columns([
                LocationColumn::make('location'),

                TextColumn::make('votable.name')
                    ->label(__('app.field.candidate'))
                    ->searchable(),

                TextColumn::make('initial')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('redistributed')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                LocationFilter::make()
                    ->withoutCountry(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->paginated([10, 25, 50, 100])
            ->deferLoading();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageMandates::route('/'),
        ];
    }
}
