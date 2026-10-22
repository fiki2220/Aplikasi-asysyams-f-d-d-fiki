<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use App\Http\Middleware\RedirectUnauthorizedFilamentAccess;
use App\Filament\Auth\CustomLogin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(CustomLogin::class)
            ->brandName('Admin Asy-Syams')
            ->colors([
                'primary' => Color::Green,
            ])
            ->favicon(asset('images/logo.png'))
            ->darkMode(false)
            ->homeUrl('/')

            // ⭐ OPTIMASI: SPA mode — navigasi antar halaman tanpa full reload
            ->spa()

            // ⭐ OPTIMASI: Sidebar bisa di-collapse
            ->sidebarCollapsibleOnDesktop()

            // ⭐ OPTIMASI: Font sistem (nol request, paling cepat)
            ->font('system-ui')

            // ⭐ OPTIMASI: Theme via Vite (CSS di-bundle, bukan dari CDN)
            ->viteTheme('resources/css/filament/admin/theme.css')

            // ════════════════════════════════════════════════════════
            // RENDER HOOKS
            // ════════════════════════════════════════════════════════

            // 1️⃣ PRELOADER
            ->renderHook(
                PanelsRenderHook::BODY_START,
                fn (): string => view('components.loader')->render(),
            )

            // 2️⃣ CSS CUSTOM
            ->renderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn (): string => view('filament.auth.login-styles')->render(),
            )

            // 3️⃣ BACK-HOME-LINK
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                fn (): string => view('filament.auth.back-home-link')->render(),
            )

            // 4️⃣ LOADER SCRIPT
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => view('filament.auth.login-loader')->render(),
            )

            // 5️⃣ USER MENU — Klik Nama → Dashboard
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label(fn () => auth()->user()?->name ?? 'Profil')
                    ->url('/dashboard', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-user-circle'),
            ])

            ->pages([
                // Pages\Dashboard::class,
            ])

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
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
                RedirectUnauthorizedFilamentAccess::class,
            ]);
    }
}