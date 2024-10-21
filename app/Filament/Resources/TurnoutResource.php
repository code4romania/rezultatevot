<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\TurnoutResource\Pages;
use App\Models\Turnout;
use Filament\Resources\Resource;
use Filament\Tables;
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
                TextColumn::make('location')
                    ->label(__('admin.field.location'))
                    ->searchable(
                        query: fn (Builder $query, string $search) => $query
                            ->whereRelation('country', 'countries.name', 'like', "%{$search}%")
                            ->orWhereRelation('county', 'counties.name', 'like', "%{$search}%")
                            ->orWhereRelation('locality', 'localities.name', 'like', "%{$search}%")
                    )
                    ->state(function (Turnout $record) {
                        if ($record->country) {
                            return $record->country->name;
                        }

                        return \sprintf('%s, %s', $record->locality->name, $record->county->name);
                    })
                    ->description(function (Turnout $record) {
                        if ($record->country) {
                            return $record->country->id;
                        }

                        return $record->locality->id;
                    }),

                TextColumn::make('initial_permanent')
                    ->label(__('admin.field.initial_permanent'))
                    ->numeric()
                    ->alignRight()
                    ->sortable(),

                TextColumn::make('initial_complement')
                    ->label(__('admin.field.initial_complement'))
                    ->numeric()
                    ->alignRight()
                    ->sortable(),

                TextColumn::make('permanent')
                    ->label(__('admin.field.voters_permanent'))
                    ->numeric()
                    ->alignRight()
                    ->sortable(),

                TextColumn::make('complement')
                    ->label(__('admin.field.voters_complement'))
                    ->numeric()
                    ->alignRight()
                    ->sortable(),

                TextColumn::make('supplement')
                    ->label(__('admin.field.voters_supplement'))
                    ->numeric()
                    ->alignRight()
                    ->sortable(),

                TextColumn::make('mobile')
                    ->label(__('admin.field.voters_mobile'))
                    ->numeric()
                    ->alignRight()
                    ->sortable(),

                TextColumn::make('total')
                    ->label(__('admin.field.voters_total'))
                    ->numeric()
                    ->alignRight()
                    ->sortable(),

                TextColumn::make('percent')
                    ->label(__('admin.field.voters_percent'))
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
            ->deferLoading()
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTurnouts::route('/'),
        ];
    }
}
