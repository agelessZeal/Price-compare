<?php

namespace Vanguard\Http\Controllers\Api\Auth;

use Vanguard\Events\User\Registered;
use Vanguard\Http\Controllers\Api\ApiController;
use Vanguard\Http\Requests\Auth\RegisterRequest;
use Vanguard\Repositories\Role\RoleRepository;
use Vanguard\Repositories\User\UserRepository;
use Vanguard\Support\Enum\UserStatus;

class RegistrationController extends ApiController
{
    /**
     * @var UserRepository
     */
    private $users;

    /**
     * @var RoleRepository
     */
    private $roles;

    /**
     * Create a new authentication controller instance.
     * @param UserRepository $users
     * @param RoleRepository $roles
     */
    public function __construct(UserRepository $users, RoleRepository $roles)
    {
        $this->middleware('registration');

        $this->users = $users;
        $this->roles = $roles;
    }

    /**
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(RegisterRequest $request)
    {
        // Determine user status. User's status will be set to UNCONFIRMED
        // if he has to confirm his email or to ACTIVE if email confirmation is not required
        $status = settings('reg_email_confirmation')
            ? UserStatus::UNCONFIRMED
            : UserStatus::ACTIVE;

        $role = $this->roles->findByName('User');

        $user = $this->users->create(array_merge(
            $request->only('email', 'username', 'password'),
            ['status' => $status, 'role_id' => $role->id]
        ));

        event(new Registered($user));

        return $this->setStatusCode(201)
            ->respondWithArray([
                'requires_email_confirmation' => !! settings('reg_email_confirmation')
            ]);
    }

    /**
     * Verify email via email confirmation token.
     * @param $token
     * @return \Illuminate\Http\Response
     */
    public function verifyEmail($token)
    {
        if (! settings('reg_email_confirmation')) {
            return $this->errorNotFound();
        }

        if ($user = $this->users->findByConfirmationToken($token)) {
            $this->users->update($user->id, [
                'status' => UserStatus::ACTIVE,
                'confirmation_token' => null
            ]);

            return $this->respondWithSuccess();
        }

        return $this->setStatusCode(400)
            ->respondWithError("Invalid confirmation token.");
    }
}
