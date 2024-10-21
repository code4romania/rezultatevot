<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\LocalityResource\Pages;
use App\Models\Locality;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LocalityResource extends Resource
{
    protected static ?string $model = Locality::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $isScopedToTenant = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                    ->label(__('admin.field.siruta'))
                    ->sortable(),

                TextColumn::make('name')
                    ->label(__('admin.field.name'))
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
                    ->label(__('admin.field.county'))
                    ->searchable()
                    ->sortable(),

            ])
            ->filters([
                SelectFilter::make('county')
                    ->relationship('county', 'name')
                    ->label(__('admin.field.county')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageLocalities::route('/'),
        ];
    }
}
