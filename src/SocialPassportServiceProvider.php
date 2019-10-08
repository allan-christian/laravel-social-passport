<?php

namespace AllanChristian\SocialPassport;

use AllanChristian\SocialPassport\Grants\SocialGrant;
use AllanChristian\SocialPassport\Resolvers\SocialUserResolverInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use Laravel\Passport\Passport;
use League\OAuth2\Server\AuthorizationServer;

class SocialPassportServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        if (SocialPassport::$runsMigrations) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }

        if (SocialPassport::$bindRoutes) {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        }

        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'allan-christian');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'allan-christian');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/social-passport.php', 'social-passport');

        // Bind
        $this->app->bind(SocialUserResolverInterface::class, config('social-passport.social_user_resolver'));

        // Create social grant
        $this->app->resolving(AuthorizationServer::class, function (AuthorizationServer $server) {
            $server->enableGrantType(
                $this->makeSocialGrant(),
                Passport::tokensExpireIn()
            );
        });

        // Register the service the package provides.
        $this->app->singleton(SocialPassport::class, function ($app) {
            return new SocialPassport();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [SocialPassport::class];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/social-passport.php' => config_path('social-passport.php'),
        ], 'social-passport.config');

        // Publishing the migrations.
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'social-passport.migrations');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/allan-christian'),
        ], 'social-passport.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/allan-christian'),
        ], 'social-passport.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/allan-christian'),
        ], 'social-passport.views');*/

        // Registering package commands.
        // $this->commands([]);
    }

    /**
     * Create and configure a Social grant instance.
     *
     * @return SocialGrant
     *
     * @throws BindingResolutionException
     */
    protected function makeSocialGrant(): SocialGrant
    {
        $grant = new SocialGrant(
            $this->app->make(SocialUserResolverInterface::class),
            $this->app->make(RefreshTokenRepository::class)
        );

        $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());

        return $grant;
    }
}
