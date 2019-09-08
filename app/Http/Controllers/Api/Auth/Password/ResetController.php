<?php

namespace Vanguard\Http\Controllers\Api\Auth\Password;

use Vanguard\Events\User\ResetedPasswordViaEmail;
use Vanguard\Http\Controllers\Api\ApiController;
use Vanguard\Http\Requests\Auth\PasswordResetRequest;
use Password;

class ResetController extends ApiController
{
    /**
     * Create a new password controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Reset the given user's password.
     *
     * @param PasswordResetRequest $request
     * @return \Illuminate\Http\Response
     */
    public function index(PasswordResetRequest $request)
    {
        $credentials = $request->only('email', 'password', 'password_confirmation', 'token');

        $response = Password::reset($credentials, function ($user, $password) {
            $this->resetPassword($user, $password);
        });

        switch ($response) {
            case Password::PASSWORD_RESET:
                return $this->respondWithSuccess();

            default:
                return $this->setStatusCode(400)
                    ->respondWithError(trans($response));
        }
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->password = $password;
        $user->save();

        event(new ResetedPasswordViaEmail($user));
    }
}
