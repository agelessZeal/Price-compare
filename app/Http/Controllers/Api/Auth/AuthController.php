<?php

namespace Vanguard\Http\Controllers\Api\Auth;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Vanguard\Events\User\LoggedIn;
use Vanguard\Events\User\LoggedOut;
use Vanguard\Http\Controllers\Api\ApiController;
use Vanguard\Http\Requests\Auth\LoginRequest;
use Vanguard\Services\Auth\Api\JWT;

/**
 * Class LoginController
 * @package Vanguard\Http\Controllers\Api\Auth
 */
class AuthController extends ApiController
{
    public function __construct()
    {
        $this->middleware('guest')->only('login');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Attempt to log the user in and generate unique
     * JWT token on successful authentication.
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('username', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return $this->errorUnauthorized('Invalid credentials.');
            }
        } catch (JWTException $e) {
            return $this->errorInternalError('Could not create token.');
        }

        $user = auth()->user();

        if ($user->isBanned()) {
            $this->invalidateToken($token);
            return $this->errorUnauthorized('Your account is banned by administrators.');
        }

        if ($user->isUnconfirmed()) {
            $this->invalidateToken($token);
            return $this->errorUnauthorized('Please confirm your email address first.');
        }

        event(new LoggedIn);

        return $this->respondWithArray(compact('token'));
    }

    private function invalidateToken($token)
    {
        JWTAuth::setToken($token);
        JWTAuth::invalidate();
    }

    /**
     * Logout user and invalidate token.
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        event(new LoggedOut);

        auth()->logout();

        return $this->respondWithSuccess();
    }
}
