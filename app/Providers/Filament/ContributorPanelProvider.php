<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Contributor\Pages\Auth\Login;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class ContributorPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('contributor')
            ->path('contributor')
            ->login(Login::class)
            ->brandLogo(fn () => new HtmlString(\Blade::render('<x-icon-logo />')))
            ->brandLogoHeight('60px')
            ->colors([
                'primary' => Color::hex('#6b46c1'),
            ])
            ->discoverResources(in: app_path('Filament/Contributor/Resources'), for: 'App\\Filament\\Contributor\\Resources')
            ->discoverPages(in: app_path('Filament/Contributor/Pages'), for: 'App\\Filament\\Contributor\\Pages')
            ->discoverWidgets(in: app_path('Filament/Contributor/Widgets'), for: 'App\\Filament\\Contributor\\Widgets')
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
            ]);
    }
}
