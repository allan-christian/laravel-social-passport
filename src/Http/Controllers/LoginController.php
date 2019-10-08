<?php

namespace AllanChristian\SocialPassport\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Password;
use Laravel\Socialite\Facades\Socialite;
use AllanChristian\SocialPassport\Models\SocialAccount;

class LoginController extends Controller
{
    /**
     * Passport Password Grant Client ID.
     *
     * @var string
     */
    protected $clientId;

    /**
     * Passport Password Grant Client Secret.
     *
     * @var string
     */
    protected $clientSecret;

    /**
     * LoginController constructor.
     *
     * Initialize constructor and validate required configuration values.
     */
    public function __construct()
    {
        $this->clientId = config('social-passport.client.id');
        $this->clientSecret = config('social-passport.client.secret');

        if (! $this->clientId || ! $this->clientSecret) {
            abort('501', 'Not Implemented');
        }
    }

    /**
     * Bridges request to Passport's oAuth token route.
     *
     * @param Request $request
     * @return mixed
     */
    public function login(Request $request)
    {
        $request->request->add([
            'grant_type' => 'password',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ]);

        return Route::dispatch(Request::create(route('passport.token'), 'POST'));
    }

    public function loginProvider(Request $request, $provider)
    {
        $socialUser = Socialite::driver($provider)->stateless()->user();

        $request->request->replace([
            'grant_type' => 'social',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'provider' => $provider,
            'access_token' => $socialUser->token,
        ]);

        return Route::dispatch(Request::create(route('passport.token'), 'POST'));
    }

    /**
     * Bridges request to Passport's oAuth token route.
     * @param Request $request
     * @return mixed
     */
    public function refresh(Request $request)
    {
        $request->request->add([
            'grant_type' => 'refresh_token',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ]);

        return Route::dispatch(Request::create(route('passport.token'), 'POST'));
    }

    /**
     * Revokes logged user access and refresh tokens.
     *
     * If request body contains a boolean true "everywhere" value, all logged user tokens
     * will be revoked.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        if ($request->input('everywhere', false) === true) {
            $request->user()->tokens->each(function ($token) {
                DB::table('oauth_refresh_tokens')
                    ->where('access_token_id', $token->id)
                    ->update([
                        'revoked' => true,
                    ]);

                $token->revoke();
            });
        } else {
            $accessToken = $request->user()->token();

            DB::table('oauth_refresh_tokens')
                ->where('access_token_id', $accessToken->id)
                ->update([
                    'revoked' => true,
                ]);

            $accessToken->revoke();
        }

        return response()->json(['message' => 'OK'], 200);
    }

    /**
     * Uses Laravel's built-in forgot email method.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function passwordForgot(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $response = Password::broker()->sendResetLink(
            $request->only('email')
        );

        return response()->json(['message' => trans($response)], $response === Password::RESET_LINK_SENT ? 200 : 404);
    }

    /**
     * Uses Laravel's built-in reset password method.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function passwordReset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $response = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        return response()->json(['message' => trans($response)], $response === Password::PASSWORD_RESET ? 200 : 422);
    }

    /**
     * Exchange code to retrieve social account and link it to logged user model.
     *
     * @param $provider
     * @return JsonResponse
     */
    public function providerLink($provider)
    {
        $user = auth()->user();
        $socialUser = Socialite::driver($provider)->stateless()->user();

        $socialAccount = SocialAccount::where('provider_name', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if (! $socialAccount) {
            $user->socialAccounts()->create([
                'provider_id' => $socialUser->getId(),
                'provider_name' => $provider,
            ]);

            return response()->json(['message' => 'OK'], 200);
        }

        return response()->json(['message' => 'Unprocessable Entity'], 422);
    }

    /**
     * Delete social account from logged user model that belongs to provider.
     *
     * @param $provider
     * @return JsonResponse
     */
    public function providerUnlink($provider)
    {
        $socialAccounts = SocialAccount::where('provider_name', $provider)
            ->where('owner_id', auth()->id());

        if ($socialAccounts->count()) {
            $socialAccounts->delete();

            return response()->json(['message' => 'OK'], 200);
        }

        return response()->json(['message' => 'Unprocessable Entity'], 422);
    }
}
