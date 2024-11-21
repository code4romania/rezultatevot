<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CountyResource\Pages;
use App\Models\County;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CountyResource extends Resource
{
    protected static ?string $model = County::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static bool $isScopedToTenant = false;

    protected static ?int $navigationSort = 28;

    public static function getNavigationGroup(): ?string
    {
        return __('app.navigation.nomenclature');
    }

    public static function getModelLabel(): string
    {
        return __('app.county.label.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.county.label.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('id')
                    ->label(__('app.field.siruta'))
                    ->unique(ignoreRecord: true)
                    ->required(),

                TextInput::make('name')
                    ->label(__('app.field.name'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('app.field.siruta'))
                    ->sortable()
                    ->shrink(),

                TextColumn::make('name')
                    ->label(__('app.field.name'))
                    ->searchable()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCounties::route('/'),
        ];
    }
}
