<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Elections;

use App\Enums\DataLevel;
use App\Enums\DefaultElectionPage;
use App\Enums\ElectionType;
use App\Filament\Admin\Resources\Elections\Pages\ListElections;
use App\Filament\Admin\Resources\Elections\Pages\ViewElection;
use App\Filament\Admin\Resources\Elections\RelationManagers\ScheduledJobRelationManager;
use App\Models\Country;
use App\Models\County;
use App\Models\Election;
use App\Models\Locality;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ElectionResource extends Resource
{
    protected static ?string $model = Election::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 30;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static bool $isScopedToTenant = false;

    public static function getNavigationGroup(): ?string
    {
        return __('app.navigation.admin');
    }

    public static function getModelLabel(): string
    {
        return __('app.election.label.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.election.label.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('type')
                    ->label(__('app.field.type'))
                    ->options(ElectionType::options())
                    ->enum(ElectionType::class)
                    ->required(),

                DatePicker::make('date')
                    ->label(__('app.field.date'))
                    ->default(today())
                    ->required(),

                TextInput::make('title')
                    ->label(__('app.field.title'))
                    ->required(),

                TextInput::make('subtitle')
                    ->label(__('app.field.subtitle'))
                    ->nullable(),

                TextInput::make('slug')
                    ->label(__('app.field.slug'))
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->columnSpanFull(),

                Toggle::make('is_live')
                    ->label(__('app.field.is_live'))
                    ->default(false),

                Toggle::make('is_visible')
                    ->label(__('app.field.is_visible'))
                    ->default(false),

                Toggle::make('has_lists')
                    ->label(__('app.field.has_lists'))
                    ->default(false),

                Toggle::make('properties.show_threshold')
                    ->label(__('app.field.show_threshold'))
                    ->default(false),

                TextInput::make('properties.total_seats')
                    ->label(__('app.field.total_seats'))
                    ->nullable()
                    ->numeric(),

                Select::make('properties.default_tab')
                    ->label(__('app.field.default_tab'))
                    ->options(DefaultElectionPage::options())
                    ->enum(DefaultElectionPage::class)
                    ->selectablePlaceholder(false)
                    ->nullable(),

                Fieldset::make(__('app.field.default_place'))
                    ->columnSpanFull()
                    ->statePath('properties.default_place')
                    ->columns(2)
                    ->schema([
                        Select::make('level')
                            ->label(__('app.field.level'))
                            ->options(DataLevel::options())
                            ->afterStateUpdated(fn (Set $set) => $set('country', null))
                            ->enum(DataLevel::class)
                            ->live()
                            ->nullable(),
                        Select::make('country')
                            ->label(__('app.field.country'))
                            ->options(Country::pluck('name', 'id'))
                            ->hidden(fn (Get $get) => DataLevel::DIASPORA->isNot($get('level')))
                            ->nullable(),

                        Group::make([
                            Select::make('county')
                                ->label(__('app.field.county'))
                                ->options(County::pluck('name', 'id'))
                                ->hidden(fn (Get $get) => DataLevel::NATIONAL->isNot($get('level')))
                                ->afterStateUpdated(fn (Set $set) => $set('locality', null))
                                ->live()
                                ->nullable(),
                            Select::make('locality')
                                ->label(__('app.field.locality'))
                                ->options(fn (Get $get) => $get('county') ? Locality::where('county_id', $get('county'))->pluck('name', 'id') : [])
                                ->hidden(fn (Get $get) => ! $get('county'))
                                ->nullable(),
                        ]),
                    ]),

                KeyValue::make('properties.tabs')
                    ->label(__('app.field.tabs'))
                    ->columnSpanFull()
                    ->nullable(),

                RichEditor::make('properties.alert')
                    ->label(__('app.field.alert'))
                    ->columnSpanFull()
                    ->nullable(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make()
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        TextEntry::make('type')
                            ->label(__('app.field.type')),

                        TextEntry::make('title')
                            ->label(__('app.field.title')),

                        TextEntry::make('subtitle')
                            ->label(__('app.field.subtitle')),

                        TextEntry::make('slug')
                            ->label(__('app.field.slug')),

                        TextEntry::make('date')
                            ->label(__('app.field.date'))
                            ->date(),

                        IconEntry::make('is_live')
                            ->label(__('app.field.is_live'))
                            ->boolean(),

                        IconEntry::make('has_lists')
                            ->label(__('app.field.has_lists'))
                            ->boolean(),

                        IconEntry::make('is_visible')
                            ->label(__('app.field.is_visible'))
                            ->boolean(),

                        Section::make(__('app.field.default_place'))
                            ->columnSpanFull()
                            ->statePath('properties.default_place')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('level')
                                    ->label(__('app.field.level')),

                                TextEntry::make('country')
                                    ->label(__('app.field.country')),

                                TextEntry::make('county')
                                    ->label(__('app.field.county')),

                                TextEntry::make('locality')
                                    ->label(__('app.field.locality')),
                            ]),

                        TextEntry::make('properties.alert')
                            ->label(__('app.field.alert'))
                            ->columnSpanFull()
                            ->html(),
                    ]),

                Section::make()
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(__('app.field.created_at'))
                            ->dateTime(),

                        TextEntry::make('updated_at')
                            ->label(__('app.field.updated_at'))
                            ->dateTime(),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('app.field.id'))
                    ->sortable()
                    ->shrink(),

                TextColumn::make('type')
                    ->label(__('app.field.type'))
                    ->sortable(),

                TextColumn::make('title')
                    ->label(__('app.field.title'))
                    ->searchable()
                    ->sortable()
                    ->description(fn (Election $record) => $record->subtitle),

                TextColumn::make('date')
                    ->label(__('app.field.date'))
                    ->sortable(),

                IconColumn::make('is_live')
                    ->label(__('app.field.is_live'))
                    ->boolean(),

                IconColumn::make('is_visible')
                    ->label(__('app.field.is_visible'))
                    ->boolean(),

            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('app.field.type')),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->recordActions([
                ViewAction::make()
                    ->iconButton(),

                EditAction::make()
                    ->iconButton(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            ScheduledJobRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListElections::route('/'),
            'view' => ViewElection::route('/{record}'),
        ];
    }
}
