<?php

namespace Vanguard\Http\Controllers\Api;

use Auth;
use Carbon\Carbon;
use League\Fractal\Resource\Collection;
use Vanguard\Repositories\Activity\ActivityRepository;
use Vanguard\Repositories\User\UserRepository;
use Vanguard\Support\Enum\UserStatus;
use Vanguard\Transformers\UserTransformer;

/**
 * Class StatsController
 * @package Vanguard\Http\Controllers\Api
 */
class StatsController extends ApiController
{
    /**
     * @var UserRepository
     */
    private $users;
    /**
     * @var ActivityRepository
     */
    private $activities;

    public function __construct(UserRepository $users, ActivityRepository $activities)
    {
        $this->middleware('auth');
        $this->users = $users;
        $this->activities = $activities;
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function index()
    {
        if (Auth::user()->hasRole('Admin')) {
            return $this->adminStats();
        }

        return $this->defaultStats();
    }

    /**
     * Get admin stats with details about users per
     * status and users per month for current year.
     * @return \Illuminate\Http\JsonResponse
     */
    private function adminStats()
    {
        $usersPerMonth = $this->users->countOfNewUsersPerMonth(
            Carbon::now()->startOfYear(),
            Carbon::now()
        );

        $usersPerStatus = [
            'total' => $this->users->count(),
            'new' => $this->users->newUsersCount(),
            'banned' => $this->users->countByStatus(UserStatus::BANNED),
            'unconfirmed' => $this->users->countByStatus(UserStatus::UNCONFIRMED)
        ];

        $latestRegistrations = $this->users->latest(7);

        $resource = new Collection(
            $latestRegistrations,
            new UserTransformer
        );

        return $this->respondWithArray([
            'users_per_month' => $usersPerMonth,
            'users_per_status' => $usersPerStatus,
            'latest_registrations' => $this->fractal()->createData($resource)->toArray()
        ]);
    }

    /**
     * Get user activity for each day in specified period.
     * @return mixed
     */
    private function defaultStats()
    {
        return $this->activities->userActivityForPeriod(
            Auth::user()->id,
            Carbon::now()->subWeeks(2),
            Carbon::now()
        );
    }
}
