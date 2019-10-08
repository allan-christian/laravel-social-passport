<?php

namespace AllanChristian\SocialPassport\Resolvers;

use Illuminate\Contracts\Auth\Authenticatable;
use Laravel\Socialite\Two\User as ProviderUser;

interface SocialAccountServiceInterface
{
    /**
     * Resolve user by provider credentials.
     *
     * @param ProviderUser $providerUser
     * @param string $provider
     *
     * @return Authenticatable|null
     */
    public function findOrCreate(ProviderUser $providerUser, string $provider): ?Authenticatable;
}
