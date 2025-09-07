<?php

namespace App\Providers;

use App\Repositories\Admin\Category\CategoryRepository;
use App\Repositories\Admin\Category\CategoryRepositoryInterface;
use App\Repositories\Admin\City\CityRepository;
use App\Repositories\Admin\City\CityRepositoryInterface;
use App\Repositories\Admin\Country\CountryRepository;
use App\Repositories\Admin\Country\CountryRepositoryInterface;
use App\Repositories\Admin\Currency\CurrencyRepository;
use App\Repositories\Admin\Currency\CurrencyRepositoryInterface;
use App\Repositories\Admin\Dashboard\DashboardRepository;
use App\Repositories\Admin\Dashboard\DashboardRepositoryInterface;
use App\Repositories\Admin\Language\LanguageRepository;
use App\Repositories\Admin\Language\LanguageRepositoryInterface;
use App\Repositories\Listing\ListingRepository;
use App\Repositories\Listing\ListingRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

/**
 * Service provider for binding repository interfaces to their implementations.
 *
 * This provider registers all repository interface to concrete implementation
 * bindings in the service container, enabling dependency injection throughout
 * the application following the Repository pattern.
 *
 * @package App\Providers
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register repository bindings in the service container.
     *
     * Binds all repository interfaces to their concrete implementations
     * to enable dependency injection and loose coupling.
     *
     * @return void
     */
    public function register(): void
    {
        // Existing bindings
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(CountryRepositoryInterface::class, CountryRepository::class);
        $this->app->bind(CurrencyRepositoryInterface::class, CurrencyRepository::class);
        $this->app->bind(LanguageRepositoryInterface::class, LanguageRepository::class);
        $this->app->bind(DashboardRepositoryInterface::class, DashboardRepository::class);

        // Listing system bindings
        $this->app->bind(ListingRepositoryInterface::class, ListingRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(CityRepositoryInterface::class, CityRepository::class);
    }

    /**
     * Bootstrap repository services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
