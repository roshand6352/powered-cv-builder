<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Contracts\Repositories\CvRepositoryInterface::class,
            \App\Repositories\CvRepository::class,
        );

        $this->app->singleton(\App\Services\Ai\AiProviderManager::class);

        $this->app->bind(
            \App\Contracts\Ai\AiClientInterface::class,
            \App\Services\Ai\AiProviderManager::class,
        );

        $this->app->bind(
            \App\Contracts\Ai\OpenAiClientInterface::class,
            \App\Services\Ai\OpenAiClient::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Support\Cache\ApplicationCache::registerInvalidation();

        Blade::anonymousComponentPath(resource_path('views/web/components'));

        \Illuminate\Support\Facades\Route::bind('cv', function (string $value) {
            $user = auth()->user();

            if (! $user) {
                abort(404);
            }

            $cv = app(\App\Contracts\Repositories\CvRepositoryInterface::class)
                ->findForUser($user, $value);

            if (! $cv) {
                abort(404);
            }

            return $cv;
        });

        \Illuminate\Support\Facades\Route::bind('coverLetter', function (string $value) {
            $user = auth()->user();

            if (! $user) {
                abort(404);
            }

            $coverLetter = $user->coverLetters()
                ->with([
                    'cv:id,title,cv_data,template_id,user_id',
                    'template:id,name,slug,layout_json',
                    'user.plan',
                ])
                ->where('slug', $value)
                ->first();

            if (! $coverLetter) {
                abort(404);
            }

            return $coverLetter;
        });

        \Illuminate\Support\Facades\Route::bind('pdfExport', function (string $value) {
            $user = auth()->user();

            if (! $user) {
                abort(404);
            }

            $export = $user->pdfExports()->find($value);

            if (! $export) {
                abort(404);
            }

            return $export;
        });

        \Illuminate\Support\Facades\Route::bind('aiLog', function (string $value) {
            $user = auth()->user();

            if (! $user) {
                abort(404);
            }

            $log = $user->aiLogs()->find($value);

            if (! $log) {
                abort(404);
            }

            return $log;
        });
    }
}
