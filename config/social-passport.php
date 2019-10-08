<?php

return [
    /**
     * Stores Passport's Password client ID and Secret
     */
    'client' => [
        'id' => env('SOCIAL_PASSPORT_CLIENT_ID'),
        'secret' => env('SOCIAL_PASSPORT_CLIENT_SECRET')
    ],

    /**
     * On login, if a model with the social email is found, it will be linked automatically to the social profile.
     *
     * In some cases (where the social network does not request for email validation), this can allow
     * malicious users to create social accounts with a email that does not belong to him and
     * linking will give him access to the app.
     */
    'autoLinkOnLogin' => true,

    /*
    * This class is responsible for find or create users based on social profile. You can replace this with
    * any class that implements \AllanChristian\SocialPassport\Resolvers\SocialAccountServiceInterface.
    */
    'social_accounts_service' => \AllanChristian\SocialPassport\Services\SocialAccountsService::class,

    /*
    * This class is responsible for retrieving social profile via Socialite and call the Social Accounts Service.
    * You can replace this with any class that implements \AllanChristian\SocialPassport\Resolvers\SocialUserResolverInterface.
    */
    'social_user_resolver' => \AllanChristian\SocialPassport\Services\SocialUserResolver::class,
];
