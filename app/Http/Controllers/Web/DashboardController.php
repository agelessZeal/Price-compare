<?php

namespace Vanguard\Http\Controllers\Web;

use Vanguard\Http\Controllers\Controller;
use Vanguard\Repositories\Activity\ActivityRepository;
use Vanguard\Repositories\User\UserRepository;
use Vanguard\Support\Enum\UserStatus;
use Auth;
use Carbon\Carbon;
use Vanguard\Repositories\Category\CategoryRepository;
use Vanguard\Repositories\Product\ProductRepository;
use Vanguard\Repositories\Favorite\FavoriteRepository;
use Vanguard\Repositories\RelatedProduct\RelatedProductRepository;

class DashboardController extends Controller
{
    /**
     * @var UserRepository
     */
    private $users;
    /**
     * @var ActivityRepository
     */
    private $activities;

    /**
     * DashboardController constructor.
     * @param UserRepository $users
     * @param ActivityRepository $activities
     */

    private $category;
    private $product;
    private $favorite;
    private $relatedProduct;

    public function __construct(UserRepository $users,
                                ActivityRepository $activities,
                                CategoryRepository $categoryRepository,
                                ProductRepository $productRepository,
                                RelatedProductRepository $relatedProductRepository,
                                FavoriteRepository $favoriteRepository)
    {
        //$this->middleware('auth');
        $this->users = $users;
        $this->activities = $activities;
        $this->category = $categoryRepository;
        $this->product = $productRepository;
        $this->favorite = $favoriteRepository;
        $this->relatedProduct = $relatedProductRepository;
    }

    /**
     * Displays dashboard based on user's role.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if (Auth::check()) {
            if (Auth::user()->hasRole('Admin')) {
                return $this->adminDashboard();
            }

        }
        return $this->defaultDashboard();
    }

    /**
     * Displays dashboard for admin users.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    private function adminDashboard()
    {
        $usersPerMonth = $this->users->countOfNewUsersPerMonth(
            Carbon::now()->subYear(),
            Carbon::now()
        );

        $stats = [
            'total' => $this->users->count(),
            'new' => $this->users->newUsersCount(),
            'banned' => $this->users->countByStatus(UserStatus::BANNED),
            'unconfirmed' => $this->users->countByStatus(UserStatus::UNCONFIRMED)
        ];

        $latestRegistrations = $this->users->latest(7);

        return view('dashboard.admin', compact('stats', 'latestRegistrations', 'usersPerMonth'));
    }

    /**
     * Displays default dashboard for non-admin users.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function defaultDashboard()
    {
        $mostPopularPdts = $this->relatedProduct->findMostPopularProducts();
        $recentSearchPdts = $this->relatedProduct->findRecentSearch();
        return view('home.index', compact('mostPopularPdts','recentSearchPdts'));
    }

    public function viewMoreCategory()
    {
        return view('category.detail');
    }
}
