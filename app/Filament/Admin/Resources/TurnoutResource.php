<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TurnoutResource\Pages;
use App\Models\Turnout;
use App\Tables\Columns\LocationColumn;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TurnoutResource extends Resource
{
    protected static ?string $model = Turnout::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return __('app.turnout.label');
    }

    public static function getPluralModelLabel(): string
    {
        return static::getModelLabel();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('country', 'county', 'locality'))
            ->columns([
                LocationColumn::make('location'),

                TextColumn::make('initial_permanent')
                    ->label(__('app.field.initial_permanent'))
                    ->numeric()
                    ->alignRight()
                    ->sortable(),

                TextColumn::make('initial_complement')
                    ->label(__('app.field.initial_complement'))
                    ->numeric()
                    ->alignRight()
                    ->sortable(),

                TextColumn::make('permanent')
                    ->label(__('app.field.voters_permanent'))
                    ->numeric()
                    ->alignRight()
                    ->sortable(),

                TextColumn::make('complement')
                    ->label(__('app.field.voters_complement'))
                    ->numeric()
                    ->alignRight()
                    ->sortable(),

                TextColumn::make('supplement')
                    ->label(__('app.field.voters_supplement'))
                    ->numeric()
                    ->alignRight()
                    ->sortable(),

                TextColumn::make('mobile')
                    ->label(__('app.field.voters_mobile'))
                    ->numeric()
                    ->alignRight()
                    ->sortable(),

                TextColumn::make('total')
                    ->label(__('app.field.voters_total'))
                    ->numeric()
                    ->alignRight()
                    ->sortable(),

                TextColumn::make('percent')
                    ->label(__('app.field.voters_percent'))
                    ->suffix('%')
                    ->badge()
                    ->numeric()
                    ->alignRight()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->paginated([10, 25, 50, 100])
            ->deferLoading();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTurnouts::route('/'),
        ];
    }
}
