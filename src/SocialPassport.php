<?php

namespace AllanChristian\SocialPassport;

class SocialPassport
{
    /**
     * Indicates if migrations will be run.
     *
     * @var bool
     */
    public static $runsMigrations = true;

    /**
     * Indicates if routes will be bind.
     *
     * @var bool
     */
    public static $bindRoutes = true;

    /**
     * Indicates if password routes will be bind.
     *
     * @var bool
     */
    public static $bindPasswordRoutes = true;

    /**
     * Indicates if forgot routes will be bind.
     *
     * @var bool
     */
    public static $bindForgotRoutes = false;

    /**
     * Configure Social Passport to not register its migrations.
     *
     * @return static
     */
    public static function ignoreMigrations()
    {
        static::$runsMigrations = false;

        return new static;
    }

    /**
     * Configure Social Passport to not bind its routes.
     *
     * @return static
     */
    public static function ignoreRoutes()
    {
        static::$bindRoutes = false;

        return new static;
    }

    /**
     * Configure Social Passport to not bind its password login/logout routes.
     *
     * @return static
     */
    public static function ignorePasswordRoutes()
    {
        static::$bindPasswordRoutes = false;

        return new static;
    }

    /**
     * Configure Social Passport to not bind its forgot password routes.
     *
     * @return static
     */
    public static function bindForgotRoutes()
    {
        static::$bindForgotRoutes = true;

        return new static;
    }

    /**
     * Retrieve Auth API provider's model class
     *
     * @return Class
     *
     * @throws \Exception
     */
    public function getAuthProviderModel () {
        if (is_null($model = config('auth.providers.' . config('auth.guards.api.provider') . '.model'))) {
            throw new \Exception('Unable to determine authentication model from config/auth.php configuration.');
        }

        return $model;
    }
}
