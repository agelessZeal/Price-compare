<?php

namespace Vanguard\Http\Controllers\Web\Auth;

use Authy;
use Vanguard\Events\User\LoggedIn;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Http\Requests\Auth\Social\SaveEmailRequest;
use Vanguard\Repositories\User\UserRepository;
use Auth;
use Session;
use Socialite;
use Laravel\Socialite\Contracts\User as SocialUser;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Vanguard\Services\Auth\Social\SocialManager;

class SocialAuthController extends Controller
{
    /**
     * @var UserRepository
     */
    private $users;

    /**
     * @var SocialManager
     */
    private $socialManager;

    public function __construct(UserRepository $users, SocialManager $socialManager)
    {
        $this->middleware('guest');

        $this->users = $users;
        $this->socialManager = $socialManager;
    }

    /**
     * Redirect user to specified provider in order to complete the authentication process.
     *
     * @param $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToProvider($provider)
    {
        if (strtolower($provider) == 'facebook') {
            return Socialite::driver('facebook')->with(['auth_type' => 'rerequest'])->redirect();
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle response authentication provider.
     *
     * @param $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleProviderCallback($provider)
    {
        $socialUser = $this->getUserFromProvider($provider);

        $user = $this->users->findBySocialId($provider, $socialUser->getId());

        if (! $user) {
            if (! settings('reg_enabled')) {
                return redirect('login')->withErrors(trans('app.only_users_with_account_can_login'));
            }

            // Only allow missing email from Twitter provider
            if (! $socialUser->getEmail()) {
                return strtolower($provider) == 'twitter'
                    ? $this->handleMissingEmail($socialUser)
                    : redirect('login')->withErrors(trans('app.you_have_to_provide_email'));
            }

            $user = $this->socialManager->associate($socialUser, $provider);
        }

        return $this->loginAndRedirect($user);
    }

    /**
     * Display form where users authenticated for the first time via
     * Twitter can provide their emails address.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getTwitterEmail()
    {
        $account = $this->getSocialAccountFromSession();

        return view('auth.social.twitter-email', compact('account'));
    }

    /**
     * Save provided email address and log the user in.
     *
     * @param SaveEmailRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postTwitterEmail(SaveEmailRequest $request)
    {
        $account = $this->getSocialAccountFromSession();

        $account->email = $request->get('email');

        $user = $this->socialManager->associate($account, 'twitter');

        return $this->loginAndRedirect($user);
    }

    /**
     * Get user from authentication provider.
     *
     * @param $provider
     * @return SocialUser
     */
    private function getUserFromProvider($provider)
    {
        return Socialite::driver($provider)->user();
    }

    /**
     * Redirect user to page where he can provide an email,
     * since email is not provided inside oAuth response.
     *
     * @param $socialUser
     * @return \Illuminate\Http\RedirectResponse
     */
    private function handleMissingEmail($socialUser)
    {
        Session::put('social.user', $socialUser);

        return redirect()->to('auth/twitter/email');
    }

    /**
     * Log provided user in and redirect him to intended page.
     *
     * @param $user
     * @return \Illuminate\Http\RedirectResponse
     */
    private function loginAndRedirect($user)
    {
        if ($user->isBanned()) {
            return redirect()->to('login')
                ->withErrors(trans('app.your_account_is_banned'));
        }

        if (settings('2fa.enabled') && Authy::isEnabled($user)) {
            session()->put('auth.2fa.id', $user->id);
            return redirect()->route('auth.token');
        }

        Auth::login($user);

        event(new LoggedIn);

        return redirect()->intended('/');
    }

    /**
     * Get social account from session or display 404
     * page if someone is trying to access this page directly.
     *
     * @return mixed
     */
    private function getSocialAccountFromSession()
    {
        $account = Session::get('social.user');

        if (! $account) {
            throw new NotFoundHttpException;
        }

        return $account;
    }
}
