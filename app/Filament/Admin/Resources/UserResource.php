<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Enums\User\Role;
use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static bool $isScopedToTenant = false;

    protected static ?int $navigationSort = 31;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation.admin');
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
                                ->label(__('admin.field.name'))
                                ->required(),

                            TextInput::make('email')
                                ->label(__('admin.field.email'))
                                ->required()
                                ->unique(ignoreRecord: true),

                            Select::make('role')
                                ->label(__('admin.field.role'))
                                ->options(Role::options())
                                ->enum(Role::class)
                                ->reactive()
                                ->required(),
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
                                ->label(__('admin.field.name')),

                            TextEntry::make('email')
                                ->label(__('admin.field.email')),

                            TextEntry::make('role')
                                ->label(__('admin.field.role')),
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
                    ->label(__('admin.field.name'))
                    ->searchable(),

                TextColumn::make('email')
                    ->label(__('admin.field.email'))
                    ->searchable(),

                TextColumn::make('role')
                    ->label(__('admin.field.role')),
            ])
            ->filters([
                //
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
