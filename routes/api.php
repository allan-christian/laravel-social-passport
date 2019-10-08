<?php

use AllanChristian\SocialPassport\SocialPassport;

Route::group([
    'middleware' => 'api',
    'prefix' => 'api',
    'namespace' => '\AllanChristian\SocialPassport\Http\Controllers',
], function () {
    if (SocialPassport::$bindPasswordRoutes) {
        Route::post('login', 'LoginController@login')->name('social-passport.login');
        Route::post('login/refresh', 'LoginController@refresh')->name('social-passport.login.refresh');
    }

    if (SocialPassport::$bindForgotRoutes) {
        Route::post('login/forgot', 'LoginController@passwordForgot')->name('social-passport.password.forgot');
        Route::post('login/reset', 'LoginController@passwordReset')->name('social-passport.password.reset');
    }

    Route::post('login/{provider}', 'LoginController@loginProvider')->name('social-passport.login.provider');

    Route::group([
        'middleware' => 'auth:api'
    ], function () {
        if (SocialPassport::$bindPasswordRoutes) {
            Route::post('logout', 'LoginController@logout')->name('social-passport.login.logout');
        }

        Route::group([
            'prefix' => 'social-providers'
        ], function () {
            Route::post('/{social_provider}', 'LoginController@providerLink')
                ->name('social-passport.providers.link');

            Route::delete('/{social_provider}', 'LoginController@providerUnlink')
                ->name('social-passport.providers.unlink');
        });

        Route::apiResource('social-accounts', 'SocialAccountsController');
    });
});
