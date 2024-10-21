<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ElectionResource\Pages;
use App\Models\Election;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ElectionResource extends Resource
{
    protected static ?string $model = Election::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 30;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static bool $isScopedToTenant = false;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation.admin');
    }

    public static function getModelLabel(): string
    {
        return __('app.election.label.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.election.label.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Select::make('type')
                            ->label(__('admin.field.type'))
                            ->relationship('type', 'name')
                            ->required(),

                        TextInput::make('title')
                            ->label(__('admin.field.title'))
                            ->required(),

                        TextInput::make('subtitle')
                            ->label(__('admin.field.subtitle'))
                            ->nullable(),

                        /*
                         * @see https://dev.mysql.com/doc/refman/8.4/en/year.html Documentation for the YEAR data type
                         */
                        TextInput::make('year')
                            ->label(__('admin.field.year'))
                            ->minValue(1901)
                            ->maxValue(2155)
                            ->numeric()
                            ->default(today()->year)
                            ->required(),

                        Toggle::make('is_live')
                            ->label(__('admin.field.is_live'))
                            ->default(false),

                    ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns(3)
            ->schema([
                Infolists\Components\Section::make()
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        TextEntry::make('title')
                            ->label(__('admin.field.title')),

                        TextEntry::make('subtitle')
                            ->label(__('admin.field.subtitle')),

                        TextEntry::make('year')
                            ->label(__('admin.field.year')),

                        IconEntry::make('is_live')
                            ->label(__('admin.field.is_live'))
                            ->boolean(),
                    ]),

                Infolists\Components\Section::make()
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(__('admin.field.created_at'))
                            ->dateTime(),

                        TextEntry::make('updated_at')
                            ->label(__('admin.field.updated_at'))
                            ->dateTime(),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('admin.field.id'))
                    ->sortable()
                    ->shrink(),

                TextColumn::make('type.name')
                    ->label(__('admin.field.type'))
                    ->sortable(),

                TextColumn::make('title')
                    ->label(__('admin.field.title'))
                    ->searchable()
                    ->sortable()
                    ->description(fn (Election $record) => $record->subtitle),

                TextColumn::make('year')
                    ->label(__('admin.field.year'))
                    ->sortable(),

                IconColumn::make('is_live')
                    ->label(__('admin.field.is_live'))
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('admin.field.type'))
                    ->relationship('type', 'name'),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->iconButton(),

                Tables\Actions\EditAction::make()
                    ->iconButton(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return [];
        if ($page instanceof EditRecord) {
            return [];
        }

        return $page->generateNavigationItems([
            Pages\ViewElection::class,
            Pages\EditElection::class,
            // Pages\ElectionRounds\ManageElectionRounds::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListElections::route('/'),
            'create' => Pages\CreateElection::route('/create'),
            'view' => Pages\ViewElection::route('/{record}'),
            'edit' => Pages\EditElection::route('/{record}/edit'),
        ];
    }
}
