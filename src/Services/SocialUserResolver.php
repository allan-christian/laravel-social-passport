<?php

namespace AllanChristian\SocialPassport\Services;


use AllanChristian\SocialPassport\Resolvers\SocialUserResolverInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Laravel\Socialite\Facades\Socialite;


class SocialUserResolver implements SocialUserResolverInterface
{
    /**
     * @inheritDoc
     */
    public function resolveUserByProviderCredentials(string $provider, string $accessToken): ?Authenticatable
    {
        $providerUser = null;

        try {
            $providerUser = Socialite::driver($provider)->userFromToken($accessToken);
        } catch (Exception $exception) {}

        if ($providerUser) {
            return app()->make(config('social-passport.social_accounts_service'))->findOrCreate($providerUser, $provider);
        }

        return null;
    }
}
