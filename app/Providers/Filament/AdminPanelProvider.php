<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\Auth\Login;
use App\Filament\Admin\Resources\ElectionResource;
use App\Filament\Admin\Resources\MenuResource;
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
use Filament\Infolists\Infolist;
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
                    ->usingMenuModel(Menu::class)
                    ->usingMenuItemModel(MenuItem::class)
                    ->usingMenuLocationModel(MenuLocation::class)
                    ->addLocations([
                        'header' => 'Header',
                        'footer' => 'Footer',
                    ])
                    ->addMenuPanels([
                        ModelMenuPanel::make()
                            ->model(Page::class),

                    ]),
            ])
            ->viteTheme('resources/css/filament/common/theme.css')
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
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
        Table::$defaultDateDisplayFormat = static::$defaultDateDisplayFormat;
        Table::$defaultDateTimeDisplayFormat = static::$defaultDateTimeDisplayFormat;
        Table::$defaultTimeDisplayFormat = static::$defaultTimeDisplayFormat;

        Infolist::$defaultDateDisplayFormat = static::$defaultDateDisplayFormat;
        Infolist::$defaultDateTimeDisplayFormat = static::$defaultDateTimeDisplayFormat;
        Infolist::$defaultTimeDisplayFormat = static::$defaultTimeDisplayFormat;

        DateTimePicker::$defaultDateDisplayFormat = static::$defaultDateDisplayFormat;
        DateTimePicker::$defaultDateTimeDisplayFormat = static::$defaultDateTimeDisplayFormat;
        DateTimePicker::$defaultDateTimeWithSecondsDisplayFormat = static::$defaultDateTimeWithSecondsDisplayFormat;
        DateTimePicker::$defaultTimeDisplayFormat = static::$defaultTimeDisplayFormat;
        DateTimePicker::$defaultTimeWithSecondsDisplayFormat = static::$defaultTimeWithSecondsDisplayFormat;
    }
}
