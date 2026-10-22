<?php

namespace App\Providers;

use App\Features\Academic\Models\Semester;
use App\Features\Grades\Models\Assessment;
use App\Features\Grades\Models\Evaluation;
use App\Features\Grades\Observers\AssessmentObserver;
use App\Features\Grades\Observers\EvaluationObserver;
use App\Features\Payments\Observers\SemesterObserver;
use App\Features\Payments\Observers\UserObserver;
use App\Features\SiteSettings\Models\SiteSetting;
use App\Features\SiteSettings\Observers\SiteSettingObserver;  // ⭐ INI YANG DIPERBAIKI
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Observers — Akademik
        Assessment::observe(AssessmentObserver::class);
        Evaluation::observe(EvaluationObserver::class);

        // ⭐ Observers — Auto-generate tagihan SPP
        User::observe(UserObserver::class);
        Semester::observe(SemesterObserver::class);

        // ⭐ Observer — Auto-hapus file WebP SiteSetting
        SiteSetting::observe(SiteSettingObserver::class);
    }
}