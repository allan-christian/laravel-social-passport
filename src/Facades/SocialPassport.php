<?php

namespace AllanChristian\SocialPassport\Facades;

use Illuminate\Support\Facades\Facade;

class SocialPassport extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \AllanChristian\SocialPassport\SocialPassport::class;
    }
}
