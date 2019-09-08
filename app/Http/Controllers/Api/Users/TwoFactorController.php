<?php

namespace Vanguard\Http\Controllers\Api\Users;

use Authy;
use Vanguard\Events\User\TwoFactorDisabledByAdmin;
use Vanguard\Events\User\TwoFactorEnabledByAdmin;
use Vanguard\Http\Controllers\Api\ApiController;
use Vanguard\Http\Requests\User\EnableTwoFactorRequest;
use Vanguard\Transformers\UserTransformer;
use Vanguard\User;

/**
 * Class TwoFactorController
 * @package Vanguard\Http\Controllers\Api\Users
 */
class TwoFactorController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:users.manage');
    }

    /**
     * Enable 2FA for specified user.
     * @param User $user
     * @param EnableTwoFactorRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(User $user, EnableTwoFactorRequest $request)
    {
        if (Authy::isEnabled($user)) {
            return $this->setStatusCode(422)
                ->respondWithError("2FA is already enabled for this user.");
        }

        $user->setAuthPhoneInformation(
            $request->country_code,
            $request->phone_number
        );

        Authy::register($user);

        $user->save();

        event(new TwoFactorEnabledByAdmin($user));

        return $this->respondWithItem($user, new UserTransformer);
    }

    /**
     * Disable 2FA for specified user.
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $user)
    {
        if (! Authy::isEnabled($user)) {
            return $this->setStatusCode(422)
                ->respondWithError("2FA is not enabled for this user.");
        }

        Authy::delete($user);

        $user->save();

        event(new TwoFactorDisabledByAdmin($user));

        return $this->respondWithItem($user, new UserTransformer);
    }
}
