<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Localities;

use App\Filament\Admin\Resources\Localities\Pages\ManageLocalities;
use App\Models\Locality;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LocalityResource extends Resource
{
    protected static ?string $model = Locality::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-map-pin';

    protected static bool $isScopedToTenant = false;

    protected static ?int $navigationSort = 27;

    public static function getNavigationGroup(): ?string
    {
        return __('app.navigation.nomenclature');
    }

    public static function getModelLabel(): string
    {
        return __('app.locality.label.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.locality.label.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('county_id')
                    ->relationship('county', 'name')
                    ->required(),

                TextInput::make('level')
                    ->required()
                    ->numeric(),

                TextInput::make('type')
                    ->required()
                    ->numeric(),

                TextInput::make('parent_id')
                    ->numeric(),

                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('app.field.siruta'))
                    ->sortable(),

                TextColumn::make('name')
                    ->label(__('app.field.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('level')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('type')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('parent.name')
                    ->sortable(),

                TextColumn::make('county.name')
                    ->label(__('app.field.county'))
                    ->searchable()
                    ->sortable(),

            ])
            ->filters([
                SelectFilter::make('county')
                    ->relationship('county', 'name')
                    ->label(__('app.field.county')),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageLocalities::route('/'),
        ];
    }
}
