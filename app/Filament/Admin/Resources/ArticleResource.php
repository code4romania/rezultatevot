<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ArticleResource\Pages;
use App\Models\Article;
use Carbon\Carbon;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?int $navigationSort = 10;

    public static function getModelLabel(): string
    {
        return __('app.article.singular');
    }

    public static function getPluralLabel(): string
    {
        return __('app.article.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->label(__('app.article.title'))
                            ->maxLength(255),

                        Select::make('author_id')
                            ->relationship('author', 'name')
                            ->required()
                            ->label(__('app.article.author'))
                            ->preload(),

                        DateTimePicker::make('published_at')
                            ->label(__('app.article.published_at'))
                            ->nullable(),

                        TiptapEditor::make('content')
                            ->required()
                            ->label(__('app.article.content'))
                            ->columnSpanFull(),
                    ]),

                Section::make()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('media')
                            ->multiple()
                            ->reorderable()
                            ->previewable(false),
                    ]),

                Section::make()
                    ->schema([
                        Repeater::make('embeds')
                            ->label(__('app.article.embeds'))
                            ->defaultItems(0)
                            ->schema([
                                TextInput::make('html')
                                    ->label(__('app.article.html'))
                                    ->required(),
                            ]),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->prefix('#')
                    ->sortable()
                    ->shrink(),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('author.name')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('published_at')
                    ->formatStateUsing(fn (?Carbon $state) => $state?->toDateTimeString())
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('author')
                    ->relationship('author', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
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
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
