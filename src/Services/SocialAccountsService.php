<?php

namespace AllanChristian\SocialPassport\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Laravel\Socialite\Two\User as ProviderUser;
use AllanChristian\SocialPassport\Models\SocialAccount;
use League\OAuth2\Server\Exception\OAuthServerException;
use AllanChristian\SocialPassport\Facades\SocialPassport;
use Illuminate\Contracts\Container\BindingResolutionException;
use AllanChristian\SocialPassport\Resolvers\SocialAccountServiceInterface;

class SocialAccountsService implements SocialAccountServiceInterface
{
    /**
     * Find or create user instance by provider user instance and provider name.
     *
     * @param ProviderUser $providerUser
     * @param string $provider
     *
     * @return Authenticatable
     *
     * @throws BindingResolutionException
     * @throws OAuthServerException
     */
    public function findOrCreate(ProviderUser $providerUser, string $provider): ?Authenticatable
    {
        $socialAccount = SocialAccount::where('provider_name', $provider)
            ->where('provider_id', $providerUser->getId())
            ->first();

        $model = app()->make(SocialPassport::getAuthProviderModel());

        if ($socialAccount) {
            return $socialAccount->owner;
        } else {
            $owner = $model->where('email', $providerUser->getEmail())->first();

            if ($owner) {
                /*
                 * Security check.
                 */
                if (! config('social-passport.autoLinkOnLogin')) {
                    throw new OAuthServerException(
                        'A valid login session is required to link this account.',
                        100,
                        'login_required',
                        400
                    );
                }
            } else {
                $owner = $model->create([
                    'name' => $providerUser->getName(),
                    'username' => $providerUser->getNickname(),
                    'email' => $providerUser->getEmail(),
                ]);
            }

            $owner->socialAccounts()->create([
                'provider_id' => $providerUser->getId(),
                'provider_name' => $provider,
            ]);

            return $owner;
        }
    }
}
