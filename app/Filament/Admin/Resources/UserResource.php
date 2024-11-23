<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Enums\User\Role;
use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Str;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static bool $isScopedToTenant = false;

    protected static ?int $navigationSort = 31;

    public static function getNavigationGroup(): ?string
    {
        return __('app.navigation.admin');
    }

    public static function getModelLabel(): string
    {
        return __('app.user.label.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.user.label.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Forms\Components\Split::make([
                    SpatieMediaLibraryFileUpload::make('avatar')
                        ->collection('avatar')
                        ->avatar()
                        ->grow(false),

                    Group::make()
                        ->schema([
                            TextInput::make('name')
                                ->label(__('app.field.name'))
                                ->required(),

                            TextInput::make('email')
                                ->label(__('app.field.email'))
                                ->required()
                                ->unique(ignoreRecord: true),

                            Forms\Components\Toggle::make('change_password')
                                ->label(__('app.field.change_password'))
                                ->default(false)
                                ->live()
                                ->hidden(fn (?User $record) => ! filled($record)),

                            TextInput::make('password')
                                ->label(__('app.field.source_password'))
                                ->password()
                                ->hidden(fn (Get $get, ?User $record) => filled($record) && ! $get('change_password'))
                                ->hintAction(Action::make('generate-password')
                                    ->label(__('app.generate'))
                                    ->action(fn (TextInput $component) => $component->state(Str::password(12))))
                                ->autocomplete('new-password')
                                ->suffixAction(Action::make('show-password')
                                    ->icon('heroicon-o-eye')
                                    ->label(fn (Get $get) => $get('password') ? __('app.hide') : __('app.show'))
                                    ->action(fn (TextInput $component) => $component->password(! $component->isPassword())))
                                ->required(),

                            Select::make('role')
                                ->label(__('app.field.role'))
                                ->options(Role::options())
                                ->enum(Role::class)
                                ->live()
                                ->required(),

                            Select::make('election_id')
                                ->label(__('app.election.label.plural'))
                                ->relationship('elections', 'slug')
                                ->hidden(fn (Get $get) => filled($get('role')) ? Role::from($get('role')) !== Role::CONTRIBUTOR : true)
                                ->multiple()
                                ->preload(),
                            TiptapEditor::make('description')
                                ->label(__('app.field.description'))
                                ->nullable(),

                        ]),
                ])->from('md'),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns(1)
            ->schema([
                Infolists\Components\Split::make([
                    SpatieMediaLibraryImageEntry::make('avatar')
                        ->collection('avatar')
                        ->circular()
                        ->grow(false),

                    Infolists\Components\Group::make()
                        ->schema([
                            TextEntry::make('name')
                                ->label(__('app.field.name')),

                            TextEntry::make('email')
                                ->label(__('app.field.email')),

                            TextEntry::make('role')
                                ->label(__('app.field.role')),

                            TextEntry::make('elections.slug')
                                ->label(__('app.election.label.plural')),

                            TextEntry::make('articles.title')
                                ->label(__('app.article.plural')),

                            TextEntry::make('description')
                                ->html()
                                ->label(__('app.field.description')),
                        ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('avatar')
                    ->collection('avatar')
                    ->conversion('thumb')
                    ->shrink(),

                TextColumn::make('name')
                    ->label(__('app.field.name'))
                    ->searchable(),

                TextColumn::make('email')
                    ->label(__('app.field.email'))
                    ->searchable(),

                TextColumn::make('role')
                    ->label(__('app.field.role')),

                TextColumn::make('elections.slug')
                    ->limitList(1)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('app.election.label.plural')),

                TextColumn::make('articles_count')
                    ->label(__('app.article.plural'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->filters([

            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->iconButton(),

                Tables\Actions\EditAction::make()
                    ->iconButton(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),

        ];
    }
}
