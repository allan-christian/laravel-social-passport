<?php

namespace AllanChristian\SocialPassport\Http\Controllers;

use AllanChristian\SocialPassport\Http\Resources\SocialAccountResource;
use AllanChristian\SocialPassport\Models\SocialAccount;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

class SocialAccountsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        return SocialAccountResource::collection(
            SocialAccount::where('owner_id', auth()->id())
                ->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return SocialAccountResource
     */
    public function store(Request $request)
    {
        $attributes = $request->validate([
            'provider_name' => 'required|string|filled',
            'provider_id' => 'required|filled'
        ]);

        $socialAccount = SocialAccount::create(
            array_merge(
                Arr::only($attributes, ['provider_name', 'provider_id']),
                [
                    'owner_id' => auth()->id()
                ]
            )
        );

        return $this->show($socialAccount);
    }

    /**
     * Display the specified resource.
     *
     * @param SocialAccount $socialAccount
     * @return SocialAccountResource
     */
    public function show(SocialAccount $socialAccount)
    {
        return new SocialAccountResource($socialAccount);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param SocialAccount $socialAccount
     * @return SocialAccountResource
     */
    public function update(Request $request, SocialAccount $socialAccount)
    {
        $attributes = $request->validate([
            'provider_name' => 'sometimes|string|filled',
            'provider_id' => 'sometimes|filled'
        ]);

        $socialAccount->update(
            Arr::only($attributes, ['provider_name', 'provider_id'])
        );

        return $this->show($socialAccount);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param SocialAccount $socialAccount
     * @return Response
     * @throws \Exception
     */
    public function destroy(SocialAccount $socialAccount)
    {
        if ($socialAccount->delete()) {
            return response()->json(['message' => 'OK'], 200);
        }

        return response()->json(['message' => 'Unprocessable Entity'], 422);
    }
}
