<?php

namespace AllanChristian\SocialPassport\Services;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Contracts\Auth\Authenticatable;
use AllanChristian\SocialPassport\Resolvers\SocialUserResolverInterface;

class SocialUserResolver implements SocialUserResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function resolveUserByProviderCredentials(string $provider, string $accessToken): ?Authenticatable
    {
        $providerUser = null;

        try {
            $providerUser = Socialite::driver($provider)->userFromToken($accessToken);
        } catch (Exception $exception) {
        }

        if ($providerUser) {
            return app()->make(config('social-passport.social_accounts_service'))->findOrCreate($providerUser, $provider);
        }

        return null;
    }
}
