<?php

declare(strict_types=1);

namespace App\Providers\Filament;

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
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Filament\Widgets\AccountWidget;
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
}
