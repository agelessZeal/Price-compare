<?php

namespace Vanguard\Providers;

use Carbon\Carbon;
use Vanguard\Repositories\Activity\ActivityRepository;
use Vanguard\Repositories\Activity\EloquentActivity;
use Vanguard\Repositories\Category\CategoryRepository;
use Vanguard\Repositories\Category\EloquentCategory;
use Vanguard\Repositories\Country\CountryRepository;
use Vanguard\Repositories\Country\EloquentCountry;
use Vanguard\Repositories\Permission\EloquentPermission;
use Vanguard\Repositories\Permission\PermissionRepository;
use Vanguard\Repositories\Favorite\EloquentFavorite;
use Vanguard\Repositories\Product\EloquentProduct;
use Vanguard\Repositories\Favorite\FavoriteRepository;
use Vanguard\Repositories\Product\ProductRepository;
use Vanguard\Repositories\RelatedProduct\EloquentRelatedProduct;
use Vanguard\Repositories\RelatedProduct\RelatedProductRepository;
use Vanguard\Repositories\Role\EloquentRole;
use Vanguard\Repositories\Role\RoleRepository;
use Vanguard\Repositories\Session\DbSession;
use Vanguard\Repositories\Session\SessionRepository;
use Vanguard\Repositories\SkuGroup\EloquentSkuGroup;
use Vanguard\Repositories\SkuGroup\SkuGroupRepository;
use Vanguard\Repositories\User\EloquentUser;
use Vanguard\Repositories\User\UserRepository;
use Illuminate\Support\ServiceProvider;
use Vanguard\Category;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Carbon::setLocale(config('app.locale'));
        config(['app.name' => settings('app_name')]);

        view()->composer('*',function($view) {

            $category = new EloquentCategory;
            $view->with(['categories'=>$category->getAll(),'parentCategories'=>$category->reFormatCategory()]);
        });
        \Illuminate\Database\Schema\Builder::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(UserRepository::class, EloquentUser::class);
        $this->app->singleton(ActivityRepository::class, EloquentActivity::class);
        $this->app->singleton(RoleRepository::class, EloquentRole::class);
        $this->app->singleton(PermissionRepository::class, EloquentPermission::class);
        $this->app->singleton(SessionRepository::class, DbSession::class);
        $this->app->singleton(CountryRepository::class, EloquentCountry::class);
        $this->app->singleton(CategoryRepository::class, EloquentCategory::class);
        $this->app->singleton(ProductRepository::class, EloquentProduct::class);
        $this->app->singleton(FavoriteRepository::class, EloquentFavorite::class);
        $this->app->singleton(SkuGroupRepository::class, EloquentSkuGroup::class);
        $this->app->singleton(RelatedProductRepository::class, EloquentRelatedProduct::class);

        if ($this->app->environment('local')) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
        }
    }
}
