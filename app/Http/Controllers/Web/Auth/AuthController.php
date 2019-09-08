<?php

namespace Vanguard\Http\Controllers\Web\Auth;

use Vanguard\Events\User\LoggedIn;
use Vanguard\Events\User\LoggedOut;
use Vanguard\Events\User\Registered;
use Vanguard\Http\Requests\Auth\LoginRequest;
use Vanguard\Http\Requests\Auth\RegisterRequest;
use Vanguard\Repositories\Role\RoleRepository;
use Vanguard\Repositories\User\UserRepository;
use Vanguard\Services\Auth\TwoFactor\Contracts\Authenticatable;
use Vanguard\Support\Enum\UserStatus;
use Auth;
use Authy;
use Carbon\Carbon;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Validator;

class AuthController extends Controller
{
    /**
     * @var UserRepository
     */
    private $users;

    /**
     * Create a new authentication controller instance.
     * @param UserRepository $users
     */
    public function __construct(UserRepository $users)
    {
        $this->middleware('guest', ['except' => ['getLogout']]);
        $this->middleware('auth', ['only' => ['getLogout']]);
        $this->middleware('registration', ['only' => ['getRegister', 'postRegister']]);
        $this->users = $users;
    }

    /**
     * Show the application login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogin()
    {
        $socialProviders = config('auth.social.providers');

        return view('auth.login', compact('socialProviders'));
    }

    /**
     * Handle a login request to the application.
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|AuthController
     */
    public function postLogin(LoginRequest $request)
    {
        // In case that request throttling is enabled, we have to check if user can perform this request.
        // We'll key this by the username and the IP address of the client making these requests into this application.
        $throttles = settings('throttle_enabled');

        //Redirect URL that can be passed as hidden field.
        $to = $request->has('to') ? "?to=" . $request->get('to') : '';

        if ($throttles && $this->hasTooManyLoginAttempts($request)) {
            return $this->sendLockoutResponse($request);
        }

        $credentials = $this->getCredentials($request);

        if (! Auth::validate($credentials)) {
            // If the login attempt was unsuccessful we will increment the number of attempts
            // to login and redirect the user back to the login form. Of course, when this
            // user surpasses their maximum number of attempts they will get locked out.
            if ($throttles) {
                $this->incrementLoginAttempts($request);
            }

            return redirect()->to('login' . $to)
                ->withErrors(trans('auth.failed'));
        }

        $user = Auth::getProvider()->retrieveByCredentials($credentials);

        if ($user->isUnconfirmed()) {
            return redirect()->to('login' . $to)
                ->withErrors(trans('app.please_confirm_your_email_first'));
        }

        if ($user->isBanned()) {
            return redirect()->to('login' . $to)
                ->withErrors(trans('app.your_account_is_banned'));
        }

        Auth::login($user, settings('remember_me') && $request->get('remember'));

        return $this->handleUserWasAuthenticated($request, $throttles, $user);
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  Request $request
     * @param  bool $throttles
     * @param $user
     * @return \Illuminate\Http\Response
     */
    protected function handleUserWasAuthenticated(Request $request, $throttles, $user)
    {
        if ($throttles) {
            $this->clearLoginAttempts($request);
        }

        if (settings('2fa.enabled') && Authy::isEnabled($user)) {
            return $this->logoutAndRedirectToTokenPage($request, $user);
        }

        event(new LoggedIn);

        if ($request->has('to')) {
            return redirect()->to($request->get('to'));
        }

        return redirect()->intended();
    }

    protected function logoutAndRedirectToTokenPage(Request $request, Authenticatable $user)
    {
        Auth::logout();

        $request->session()->put('auth.2fa.id', $user->id);

        return redirect()->route('auth.token');
    }

    public function getToken()
    {
        return session('auth.2fa.id') ? view('auth.token') : redirect('login');
    }

    public function postToken(Request $request)
    {
        $this->validate($request, ['token' => 'required']);

        if (! session('auth.2fa.id')) {
            return redirect('login');
        }

        $user = $this->users->find(
            $request->session()->pull('auth.2fa.id')
        );

        if (! $user) {
            throw new NotFoundHttpException;
        }

        if (! Authy::tokenIsValid($user, $request->token)) {
            return redirect()->to('login')->withErrors(trans('app.2fa_token_invalid'));
        }

        Auth::login($user);

        event(new LoggedIn);

        return redirect()->intended('/');
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  Request  $request
     * @return array
     */
    protected function getCredentials(Request $request)
    {
        // The form field for providing username or password
        // have name of "username", however, in order to support
        // logging users in with both (username and email)
        // we have to check if user has entered one or another
        $usernameOrEmail = $request->get($this->loginUsername());

        if ($this->isEmail($usernameOrEmail)) {
            return [
                'email' => $usernameOrEmail,
                'password' => $request->get('password')
            ];
        }

        return $request->only($this->loginUsername(), 'password');
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogout()
    {
        event(new LoggedOut(Auth::user()));

        Auth::logout();

        return redirect('');
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function loginUsername()
    {
        return 'username';
    }

    /**
     * Determine if the user has too many failed login attempts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function hasTooManyLoginAttempts(Request $request)
    {
        return app(RateLimiter::class)->tooManyAttempts(
            $request->input($this->loginUsername()).$request->ip(),
            $this->maxLoginAttempts()
        );
    }

    /**
     * Increment the login attempts for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return int
     */
    protected function incrementLoginAttempts(Request $request)
    {
        app(RateLimiter::class)->hit(
            $request->input($this->loginUsername()).$request->ip(),
            $this->lockoutTime() / 60
        );
    }

    /**
     * Determine how many retries are left for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return int
     */
    protected function retriesLeft(Request $request)
    {
        $attempts = app(RateLimiter::class)->attempts(
            $request->input($this->loginUsername()).$request->ip()
        );

        return $this->maxLoginAttempts() - $attempts + 1;
    }

    /**
     * Redirect the user after determining they are locked out.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendLockoutResponse(Request $request)
    {
        $seconds = app(RateLimiter::class)->availableIn(
            $request->input($this->loginUsername()).$request->ip()
        );

        return redirect('login')
            ->withInput($request->only($this->loginUsername(), 'remember'))
            ->withErrors([
                $this->loginUsername() => $this->getLockoutErrorMessage($seconds),
            ]);
    }

    /**
     * Get the login lockout error message.
     *
     * @param  int  $seconds
     * @return string
     */
    protected function getLockoutErrorMessage($seconds)
    {
        return trans('auth.throttle', ['seconds' => $seconds]);
    }

    /**
     * Clear the login locks for the given user credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function clearLoginAttempts(Request $request)
    {
        app(RateLimiter::class)->clear(
            $request->input($this->loginUsername()).$request->ip()
        );
    }

    /**
     * Get the maximum number of login attempts for delaying further attempts.
     *
     * @return int
     */
    protected function maxLoginAttempts()
    {
        return settings('throttle_attempts', 5);
    }

    /**
     * The number of seconds to delay further login attempts.
     *
     * @return int
     */
    protected function lockoutTime()
    {
        $lockout = (int) settings('throttle_lockout_time');

        if ($lockout <= 1) {
            $lockout = 1;
        }

        return 60 * $lockout;
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRegister()
    {
        $socialProviders = config('auth.social.providers');

        return view('auth.register', compact('socialProviders'));
    }

    /**
     * Handle a registration request for the application.
     *
     * @param RegisterRequest $request
     * @param RoleRepository $roles
     * @return \Illuminate\Http\Response
     */
    public function postRegister(RegisterRequest $request, RoleRepository $roles)
    {
        // Determine user status. User's status will be set to UNCONFIRMED
        // if he has to confirm his email or to ACTIVE if email confirmation is not required
        $status = settings('reg_email_confirmation')
            ? UserStatus::UNCONFIRMED
            : UserStatus::ACTIVE;

        $role = $roles->findByName('User');

        // Add the user to database
        $user = $this->users->create(array_merge(
            $request->only('email', 'username', 'password'),
            ['status' => $status, 'role_id' => $role->id]
        ));

        event(new Registered($user));

        $message = settings('reg_email_confirmation')
            ? trans('app.account_create_confirm_email')
            : trans('app.account_created_login');

        return redirect('login')->with('success', $message);
    }

    /**
     * Confirm user's email.
     *
     * @param $token
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirmEmail($token)
    {
        if ($user = $this->users->findByConfirmationToken($token)) {
            $this->users->update($user->id, [
                'status' => UserStatus::ACTIVE,
                'confirmation_token' => null
            ]);

            return redirect()->to('login')
                ->withSuccess(trans('app.email_confirmed_can_login'));
        }

        return redirect()->to('login')
            ->withErrors(trans('app.wrong_confirmation_token'));
    }

    /**
     * Validate if provided parameter is valid email.
     *
     * @param $param
     * @return bool
     */
    private function isEmail($param)
    {
        return ! Validator::make(
            ['username' => $param],
            ['username' => 'email']
        )->fails();
    }
}
