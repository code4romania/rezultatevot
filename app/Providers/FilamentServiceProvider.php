<?php

declare(strict_types=1);

namespace App\Providers;

use Filament\Actions\CreateAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Column;
use Filament\Tables\Table;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    public static string $defaultDateDisplayFormat = 'd.m.Y';

    public static string $defaultDateTimeDisplayFormat = 'd.m.Y H:i';

    public static string $defaultDateTimeWithSecondsDisplayFormat = 'd.m.Y H:i:s';

    public static string $defaultTimeDisplayFormat = 'H:i';

    public static string $defaultTimeWithSecondsDisplayFormat = 'H:i:s';

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->setDefaultDateTimeDisplayFormats();

        $this->configureActions();
        $this->configureForms();
        $this->configureFilters();
        $this->configureInfolists();
        $this->configurePages();
        $this->configureTables();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    protected function configureActions(): void
    {
        CreateRecord::disableCreateAnother();

        CreateAction::configureUsing(fn (CreateAction $action) => $action->createAnother(false));
    }

    protected function configureForms(): void
    {
        SpatieMediaLibraryFileUpload::configureUsing(function (SpatieMediaLibraryFileUpload $fileUpload) {
            $fileUpload->disk(config('filament.default_filesystem_disk'));
        });

        // TODO: remove when the fix is released
        RichEditor::configureUsing(function (RichEditor $editor) {
            $editor->disk(config('filament.default_filesystem_disk'));
        });
    }

    protected function configureFilters(): void
    {
        //
    }

    protected function configureInfolists(): void
    {
        TextEntry::configureUsing(function (TextEntry $entry) {
            return $entry->placeholder('—');
        });
    }

    protected function configurePages(): void
    {
        //
    }

    protected function configureTables(): void
    {
        Column::macro('shrink', fn () => $this->extraHeaderAttributes(['class' => 'w-1']));

        Column::configureUsing(function (Column $column) {
            return $column->placeholder('—');
        });
    }

    protected function setDefaultDateTimeDisplayFormats(): void
    {
        Table::configureUsing(
            fn (Table $table) => $table
                ->defaultDateDisplayFormat(static::$defaultDateDisplayFormat)
                ->defaultDateTimeDisplayFormat(static::$defaultDateTimeDisplayFormat)
                ->defaultTimeDisplayFormat(static::$defaultTimeDisplayFormat)
        );

        Schema::configureUsing(
            fn (Schema $schema) => $schema
                ->defaultDateDisplayFormat(static::$defaultDateDisplayFormat)
                ->defaultDateTimeDisplayFormat(static::$defaultDateTimeDisplayFormat)
                ->defaultTimeDisplayFormat(static::$defaultTimeDisplayFormat)
        );

        DateTimePicker::configureUsing(
            fn (DateTimePicker $dateTimePicker) => $dateTimePicker
                ->defaultDateDisplayFormat(static::$defaultDateDisplayFormat)
                ->defaultDateTimeDisplayFormat(static::$defaultDateTimeDisplayFormat)
                ->defaultDateTimeWithSecondsDisplayFormat(static::$defaultDateTimeWithSecondsDisplayFormat)
                ->defaultTimeDisplayFormat(static::$defaultTimeDisplayFormat)
                ->defaultTimeWithSecondsDisplayFormat(static::$defaultTimeWithSecondsDisplayFormat)
        );
    }
}
