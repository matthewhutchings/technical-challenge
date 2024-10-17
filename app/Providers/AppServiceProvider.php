<?php

declare(strict_types=1);

namespace App\Providers;

use App\Client\IsbnClient;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureCommands();
        $this->configureDates();
        $this->configureModels();
        $this->configureMorphMap();
        $this->configurePasswordValidation();
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerIsbnClient(); // Register the IsbnClient in the container
    }

    /**
     * Register the IsbnClient binding.
     */
    private function registerIsbnClient(): void
    {
        $this->app->bind(IsbnClient::class, function ($app) {
            return new IsbnClient(
                env('ISBN_USERNAME'),
                env('ISBN_PASSWORD')
            );
        });
    }

    /**
     * Configure the application's commands
     */
    private function configureCommands(): void
    {
        DB::prohibitDestructiveCommands($this->app->isProduction());
    }

    /**
     * Configure the dates.
     */
    private function configureDates(): void
    {
        Date::use(CarbonImmutable::class);
    }

    /**
     * Configure the models.
     */
    private function configureModels(): void
    {
        Model::unguard();
        Model::preventSilentlyDiscardingAttributes();
        Model::preventAccessingMissingAttributes();

        // Prevent lazy loading always...
        Model::preventLazyLoading();

        // ...but in production, log the violation instead of throwing an exception
        if ($this->app->isProduction()) {
            Model::handleLazyLoadingViolationUsing(function (Model $model, string $relation) {
                $class = get_class($model);

                info("Attempted to lazy load [{$relation}] on model [{$class}].");
            });
        }
    }

    /**
     * Configure the application's morph map.
     */
    private function configureMorphMap(): void
    {
        Relation::enforceMorphMap([
            'user' => \App\Models\User::class,
        ]);
    }

    /**
     * Configure the password validation rules.
     */
    private function configurePasswordValidation(): void
    {
        Password::defaults(fn() => app()->isProduction() ? Password::min(8)->uncompromised() : null);
    }
}
