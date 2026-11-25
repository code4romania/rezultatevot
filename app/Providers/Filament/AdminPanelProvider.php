<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use Datlechin\FilamentMenuBuilder\ItemTypes\ModelItemType;
use Filament\Pages\Dashboard;
use Filament\Widgets\AccountWidget;
use Filament\Schemas\Schema;
use App\Filament\Admin\Pages\Auth\Login;
use App\Filament\Admin\Resources\Elections\ElectionResource;
use App\Filament\Admin\Resources\Menus\MenuResource;
use App\Models\Election;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\MenuLocation;
use App\Models\Page;
use Datlechin\FilamentMenuBuilder\FilamentMenuBuilderPlugin;
use Datlechin\FilamentMenuBuilder\MenuPanel\ModelMenuPanel;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Infolists\Components\TextEntry;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\Column;
use Filament\Tables\Table;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;

class AdminPanelProvider extends PanelProvider
{
    public static string $defaultDateDisplayFormat = 'd.m.Y';

    public static string $defaultDateTimeDisplayFormat = 'd.m.Y H:i';

    public static string $defaultDateTimeWithSecondsDisplayFormat = 'd.m.Y H:i:s';

    public static string $defaultTimeDisplayFormat = 'H:i';

    public static string $defaultTimeWithSecondsDisplayFormat = 'H:i:s';

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->maxContentWidth('full')
            ->tenant(Election::class)
            ->brandLogo(fn () => view('filament.brand'))
            ->brandLogoHeight('3rem')
            ->colors([
                'primary' => Color::Red,
            ])
            ->plugins([
                BreezyCore::make()
                    ->myProfile(slug: 'profile'),

                FilamentMenuBuilderPlugin::make()
                    ->usingResource(MenuResource::class)
                    ->addLocations([
                        'header' => 'Header',
                        'footer' => 'Footer',
                    ])
                    ->itemTypes([
                        ModelItemType::make(Page::class)
                    ]),
            ])
            ->viteTheme('resources/css/filament/common/theme.css')
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets([
                AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->navigationItems([
                NavigationItem::make('Settings')
                    ->url(fn () => ElectionResource::getUrl('view', ['record' => Filament::getTenant()]))
                    ->group(__('app.navigation.admin'))
                    ->icon('heroicon-o-cog')
                    ->sort(35),
            ])
            ->collapsibleNavigationGroups(false)
            ->databaseNotifications();
    }

    public function register(): void
    {
        parent::register();

        $this->registerMacros();
        $this->setDefaultDateTimeDisplayFormats();
    }

    public function boot(): void
    {
        TextEntry::configureUsing(function (TextEntry $entry) {
            return $entry->default('-');
        });
    }

    protected function registerMacros(): void
    {
        Column::macro('shrink', fn () => $this->extraHeaderAttributes(['class' => 'w-1']));
    }

    protected function setDefaultDateTimeDisplayFormats(): void
    {
        Table::configureUsing((static fn($value) => static fn(Table $component) => $component->defaultDateDisplayFormat($value))(static::$defaultDateDisplayFormat));
        Table::configureUsing((static fn($value) => static fn(Table $component) => $component->defaultDateTimeDisplayFormat($value))(static::$defaultDateTimeDisplayFormat));
        Table::configureUsing((static fn($value) => static fn(Table $component) => $component->defaultTimeDisplayFormat($value))(static::$defaultTimeDisplayFormat));

        Schema::configureUsing((static fn($value) => static fn(Schema $schema) => $schema->defaultDateDisplayFormat($value))(static::$defaultDateDisplayFormat));
        Schema::configureUsing((static fn($value) => static fn(Schema $schema) => $schema->defaultDateTimeDisplayFormat($value))(static::$defaultDateTimeDisplayFormat));
        Schema::configureUsing((static fn($value) => static fn(Schema $schema) => $schema->defaultTimeDisplayFormat($value))(static::$defaultTimeDisplayFormat));

        DateTimePicker::configureUsing((static fn($value) => static fn(DateTimePicker $component) => $component->defaultDateDisplayFormat($value))(static::$defaultDateDisplayFormat));
        DateTimePicker::configureUsing((static fn($value) => static fn(DateTimePicker $component) => $component->defaultDateTimeDisplayFormat($value))(static::$defaultDateTimeDisplayFormat));
        DateTimePicker::configureUsing((static fn($value) => static fn(DateTimePicker $component) => $component->defaultDateTimeWithSecondsDisplayFormat($value))(static::$defaultDateTimeWithSecondsDisplayFormat));
        DateTimePicker::configureUsing((static fn($value) => static fn(DateTimePicker $component) => $component->defaultTimeDisplayFormat($value))(static::$defaultTimeDisplayFormat));
        DateTimePicker::configureUsing((static fn($value) => static fn(DateTimePicker $component) => $component->defaultTimeWithSecondsDisplayFormat($value))(static::$defaultTimeWithSecondsDisplayFormat));
    }
}
